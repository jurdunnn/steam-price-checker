<?php

namespace App\Services;

use App\Models\Game;
use Illuminate\Support\Arr;

class SteamService
{
    public function search(string $query, $limit = null): array
    {
        $steamAppsFile = file_get_contents(public_path('steam_apps.json'));

        $games = json_decode($steamAppsFile, true)['applist']['apps'];

        $appids = [];

        foreach ($games as $game) {
            if (stripos($game['name'], $query) !== false && (!(sizeof($appids) == $limit))) {
                $appids[] = $game['appid'];
            }
        }

        return $appids;
    }

    public function getSteamGame(string $appid): ?Game
    {
        $game = Game::where('steam_app_id', $appid)->first();

        // If game exists and was fetched recently return game
        if ($game && $game->last_fetched > now()->addDays(-1)) {
            return $game;
        }

        // If game does not meet the above criteria fetch details from steam
        $data = $this->getGameInfoFromSteam($appid);

        if (!$data) {
            return null;
        }

        // If game exists, but was fetched longer ago then 1 day, update with new information
        if ($game) {
            return $game->update([
                'steam_app_id' => $data['steam_appid'],
                'title' => $data['name'],
                'image' => $data['header_image'],
                'last_fetched' => now()
            ]);
        } else { // If the above criteria is not true, create the game in the db.
            return Game::create([
                'steam_app_id' => $data['steam_appid'],
                'title' => $data['name'],
                'image' => $data['header_image'],
                'last_fetched' => now()
            ]);
        }
    }

    public function getGameInfoFromSteam($appid): ?array
    {
        $url = "https://store.steampowered.com/api/appdetails?appids=$appid";

        $reponse = $this->curlUrl($url);

        return Arr::flatten(json_decode($reponse, true), 1)[1] ?? null;
    }

    private function curlUrl(string $url): string
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $reponse = curl_exec($ch);

        curl_close($ch);

        return $reponse;
    }
}
