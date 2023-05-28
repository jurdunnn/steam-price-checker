<?php

namespace App\Services;

use App\Actions\Game\AddImages;
use App\Actions\Game\AddMetaData;
use App\Actions\Game\AddPlatformModifier;
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

    public function addImages()
    {
        $addImages = resolve(AddImages::class);

        $addImages($this->game, $this->data);
    }

    public function addPlatformModifier()
    {
        $addPlatformModifier = resolve(AddPlatformModifier::class);

        $addPlatformModifier($this->game, $this->data);
    }
}
