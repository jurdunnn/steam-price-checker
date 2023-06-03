<?php

namespace App\Actions\Game;

use App\Enums\ModifierType;
use App\Models\Game;
use App\Models\Modifier;

class AddMetacriticModifier
{
    public function __invoke(Game $game, array $data)
    {
        $score = $data['metacritic']['score'] ?? null;

        Modifier::create($game, ModifierType::METACRITIC, [
            'positive' => 'High Metacritic Score',
            'neutral' => 'No Metacritic Score',
            'negative' => 'Low Metacritic Score',
        ], function () use ($score) {
            return $score > 70;
        });
    }
}
