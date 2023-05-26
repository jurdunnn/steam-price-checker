<?php

namespace App\Observers;

use App\Models\Game;

class GameObserver
{

    /**
     * Handle the Game "retrieved" event.
     */
    public function retrieved(Game $game): void
    {
        if (!$game->metas()->first()) {
            $game->metas()->create();
        }

        if ($game->doesNotHaveRequiredData()) {
            $data = $game->steam()->getGameInfoFromSteam($game->steam_app_id);

            if ($data != null) {
                $game->metas->addMetas($data);
                $game->addImageIfMissing($data);
                $game->addPlatformModifier($data);
                $game->addMetacriticScore($data);
                $game->addReviews();
            }
        }
    }
}
