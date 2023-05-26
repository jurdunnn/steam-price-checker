<?php

namespace App\Actions\Game;

use App\Models\Game;

class AddImages
{
    public function __invoke(Game $game, array $data)
    {
        $image = $data['header_image'] ?? null;
        $backgroundImage = $data['background'] ?? null;

        if (!$game->images()->exists() || (!$game->images->image_url || !$game->images->background_image)) {
            $game->images()->create([
                'game_id' => $game->id,
                'image_url' => $image,
                'background_image' => $backgroundImage,
            ]);
        }
    }
}
