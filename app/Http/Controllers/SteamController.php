<?php

namespace App\Http\Controllers;

class SteamController extends Controller
{
    public function get($appid)
    {
        // Get Steam list
        // http://api.steampowered.com/ISteamApps/GetAppList/v0002/?key=STEAMKEY&format=json

        $url = "https://store.steampowered.com/api/appdetails?appids=$appid";

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $reponse = curl_exec($ch);

        curl_close($ch);

        return json_decode($reponse);
    }
}
