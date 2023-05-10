<?php

namespace App\Services;

use App\Models\Game;
use Illuminate\Support\Arr;

class SteamService
{
    public function getGameInfoFromSteam($appid): ?array
    {
        $url = "https://store.steampowered.com/api/appdetails?appids=$appid";

        $reponse = $this->curlUrl($url);

        return Arr::flatten(json_decode($reponse, true), 1)[1] ?? null;
    }

    public function createImageForGame(Game $game): void
    {
        $info = $this->getGameInfoFromSteam($game->steam_app_id);

        if ($info && $info['header_image']) {
            $game->image()->create([
                'game_id' => $game->id,
                'image_url' => $info['header_image'],
            ]);

            return;
        }

        // If game information was not retrieved
        // delete game from database
        $game->delete();
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
