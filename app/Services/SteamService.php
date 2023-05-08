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

        $bestMatches =  $this->find_best_matches($games, $query, $limit);

        return $bestMatches;
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

    private function find_best_matches(array $data, string $query, int $limit = 5): array
    {
        $matches = [];
        $query = strtolower($query);

        foreach ($data as $game) {
            $name = strtolower($game['name']);

            // How many edits it will take to transform the query to match the name
            $distance = levenshtein($name, $query);

            $matches[] = [
                'game' => $game,
                'distance' => $distance,
            ];
        }

        // Sort matches with the lost distance toward the beginning of the array
        usort($matches, function ($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });

        $matches = array_slice($matches, 0, $limit);

        return array_map(function ($match) {
            return $match['game']['appid'];
        }, $matches);
    }
}
