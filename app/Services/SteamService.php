<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class SteamService
{
    public function getGameInfoFromSteam($appid): ?array
    {
        $reponse = Http::get("https://store.steampowered.com/api/appdetails?appids=$appid");

        return Arr::flatten(json_decode($reponse, true), 1)[1] ?? null;
    }
}
