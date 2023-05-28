<?php

namespace App\Actions\Game;

use App\Enums\ModifierType;
use App\Models\Game;

class AddPlatformModifier
{
    public function __invoke(Game $game, array $data)
    {
        if ($game->modifiers()
            ->where('type', ModifierType::PLATFORM)
            ->exists()
        ) {
            return;
        }

        $platforms = $data['platforms'] ?? null;

        if (!$platforms) {
            $game->modifiers()->firstOrCreate([
                'title' => 'No Platform Information',
                'type' => ModifierType::PLATFORM,
                'color' => 'gray',
                'strength' => 0
            ]);

            return;
        }

        $platforms = array_filter($platforms, fn ($platform) => $platform != false);

        switch (sizeof($platforms)) {
            case 1:
                $game->modifiers()->firstOrCreate([
                    'title' => 'Few Supported Platforms',
                    'type' => ModifierType::PLATFORM,
                    'color' => 'red',
                    'strength' => -10
                ]);
                break;
            case 2:
                $game->modifiers()->firstOrCreate([
                    'title' => 'Average Number of Platforms Supported',
                    'type' => ModifierType::PLATFORM,
                    'color' => 'gray',
                    'strength' => 0
                ]);
                break;
            case 3:
                $game->modifiers()->firstOrCreate([
                    'title' => 'Many Supported Platforms',
                    'type' => ModifierType::PLATFORM,
                    'color' => 'green',
                    'strength' => 10
                ]);
                break;
        }
    }
}
