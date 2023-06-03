<?php

namespace App\Actions\Game;

use App\Enums\ModifierType;
use App\Models\Game;
use App\Models\Modifier;

class AddPlatformModifier
{
    public function __invoke(Game $game, array $data)
    {
        $platforms = $data['platforms'] ?? null;

        Modifier::create($game, ModifierType::PLATFORM, [
            'positive' => 'Many Platforms Supported',
            'neutral' => 'No Platform Information',
            'negative' => 'Few Platforms Supported',
        ], function () use ($platforms) {
            if ($platforms) {
                $platforms = array_filter($platforms, fn ($platform) => $platform != false);

                return sizeof($platforms) > 2;
            }

            return null;
        });
    }
}
