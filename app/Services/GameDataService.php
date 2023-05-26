<?php

namespace App\Services;

use App\Actions\Game\AddMetaData;
use App\Models\Game;

class GameDataService
{
    public function __construct(public Game $game, public array $data)
    {
        //
    }

    public function addGameMetas()
    {
        $addMetaData = resolve(AddMetaData::class);

        $addMetaData($this->game, $this->data);
    }
}
