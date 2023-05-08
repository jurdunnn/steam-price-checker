<?php

namespace App\Services;

use Illuminate\Support\Arr;

class SteamService
{
    public function search(string $query): array
    {
        $steamAppsFile = file_get_contents(public_path('steam_apps.json'));

        $games = json_decode($steamAppsFile, true)['applist']['apps'];

        $appids = [];

        foreach ($games as $game) {
            if (stripos($game['name'], $query) !== false) {
                $appids[] = $game['appid'];
            }
        }

        return $this->getGames($appids);
    }

    private function getGames(array $appids): array
    {
        $limit = 5;

        $appids = array_slice($appids, 0, $limit);

        $games = [];

        foreach ($appids as $id) {
            $games[] = $this->getSteamGameInfo($id);
        }

        $games = Arr::flatten($games, 2);

        $games = array_filter($games, function ($game) {
            return is_array($game);
        });

        return $games;
    }

    public function getSteamGameInfo(string $appid): ?array
    {
        $url = "https://store.steampowered.com/api/appdetails?appids=$appid";

        $reponse = $this->curlUrl($url);

        return json_decode($reponse, true);
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
