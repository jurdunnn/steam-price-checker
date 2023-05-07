<?php

namespace App\Http\Controllers;

class SteamController extends Controller
{
    public function get($appid)
    {
        $url = "https://store.steampowered.com/api/appdetails?appids=$appid";

        $reponse = $this->curlUrl($url);

        return json_decode($reponse);
    }

    public function search($query)
    {
        $steamAppsFile = file_get_contents(public_path('steam_apps.json'));

        $games = json_decode($steamAppsFile, true)['applist']['apps'];

        $appid = '';

        foreach ($games as $game) {
            if (stripos($game['name'], $query) !== false) {
                $appid = $game['appid'];
                break;
            }
        }

        return $this->get($appid);
    }

    private function curlUrl($url): string
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $reponse = curl_exec($ch);

        curl_close($ch);

        return $reponse;
    }
}
