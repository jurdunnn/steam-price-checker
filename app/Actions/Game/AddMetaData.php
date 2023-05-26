<?php

namespace App\Actions\Game;

use App\Models\Game;
use Carbon\Carbon;

class AddMetaData
{
    public function __invoke(Game $game, array $data)
    {
        $method = $game->metas->first()->exists() ? 'update' : 'create';

        $game->metas->$method([
            'free' => $data['is_free'] ?? false,
            'type' => $data['type'] ?? 'undefined',
            'unreleased' => $this->isUnreleased($data) ?? false,
        ]);
    }

    private function isUnreleased($data)
    {
        $releaseDate = $data['release_date'] ?? null;
        $carbonDate = null;
        $isUnreleased = false;

        if (preg_match('/(\d{1,2}) (\w{3}), (\d{4})/', $releaseDate['date'], $matches)) {
            $day = $matches[1];
            $month = $matches[2];
            $year = $matches[3];

            $months = [
                'Jan' => 'January',
                'Feb' => 'February',
                'Mar' => 'March',
            ];

            if (isset($months[$month])) {
                $fullMonth = $months[$month];
                $formattedDate = $day . ' ' . $fullMonth . ', ' . $year;
                $carbonDate = Carbon::createFromFormat('d F, Y', $formattedDate)->setTimezone('UTC');
                $isUnreleased = $carbonDate > now();
            }
        }

        return $isUnreleased;
    }
}
