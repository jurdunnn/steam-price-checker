<?php

namespace App\Observers;

use App\Models\Game;
use App\Services\GameDataService;

class GameObserver
{
    /**
     * Handle the Game "retrieved" event.
     */
    public function retrieved(Game $game): void
    {
        if ($game->doesNotHaveRequiredData()) {
            $data = $game->steam()->getGameInfoFromSteam($game->steam_app_id);

            if ($data != null) {
                $gameDataService = new GameDataService($game, $data);

                $gameDataService->addGameMetas();
                $gameDataService->addImages();
                $game->addPlatformModifier($data);
                $game->addMetacriticScore($data);
                $game->addReviews();
            }
        }
    }
}
