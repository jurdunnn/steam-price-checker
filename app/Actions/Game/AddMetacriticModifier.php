<?php

namespace App\Actions\Game;

use App\Enums\ModifierType;
use App\Models\Game;

class AddMetacriticModifier
{
    public function __invoke(Game $game, array $data)
    {
        if ($game->modifiers()
            ->where('type', ModifierType::METACRITIC)
            ->exists()
        ) {
            return;
        }

        $score = $data['metacritic']['score'] ?? null;

        if ($score > 70) {
            $contents = [
                'title' => 'High Metacritic Score',
                'type' => ModifierType::METACRITIC,
                'color' => 'green',
                'strength' => 10
            ];
        } elseif ($score > 50) {
            $contents = [
                'title' => 'Average Metacritic Score',
                'type' => ModifierType::METACRITIC,
                'color' => 'gray',
                'strength' => 0
            ];
        } elseif ($score === null) {
            $contents = [
                'title' => 'No Metacritic Score',
                'type' => ModifierType::METACRITIC,
                'color' => 'gray',
                'strength' => 0
            ];
        } else {
            $contents = [
                'title' => 'Poor Metacritic Score',
                'type' => ModifierType::METACRITIC,
                'color' => 'red',
                'strength' => -10
            ];
        }

        $game->modifiers()->firstOrCreate($contents);
    }
}
