<?php

namespace App\Actions\Game;

use App\Enums\AverageType;
use App\Enums\ModifierType;
use App\Models\Game;
use App\Services\AveragesService;

class AddReviewsModifier
{
    public function __invoke(Game $game, array $data)
    {
        if ($game->reviews()->exists()) {
            return;
        }

        $data = $game->steam()->getReviews($game->steam_app_id)['query_summary'];

        $game->reviews()->create([
            'total_positive' => $data['total_positive'],
            'total_negative' => $data['total_negative'],
            'total_reviews' => $data['total_reviews'],
        ]);

        if ($game->modifiers()
            ->where('type', ModifierType::REVIEW_DIST)
            ->exists()
        ) {
            return;
        }

        if ($game->reviews->total_positive > $game->reviews->total_negative) {
            $game->modifiers()->create([
                'title' => 'Positive Review Distribution',
                'type' => ModifierType::REVIEW_DIST,
                'color' => 'green',
                'strength' => 10
            ]);
        } else {
            $game->modifiers()->create([
                'title' => 'Poor Review Distribution',
                'type' => ModifierType::REVIEW_DIST,
                'color' => 'red',
                'strength' => 10
            ]);
        }

        // Calculate Average Reviews
        $averages = resolve(AveragesService::class);

        $averages->calculateAverage(AverageType::REVIEW_POSITIVE, 'reviews', 'total_positive');

        $averages->calculateAverage(AverageType::REVIEW_POOR, 'reviews', 'total_negative');

        if ($game->modifiers()->where('type', AverageType::REVIEW_POSITIVE)->doesntExist()) {
            if ($game->reviews->total_positive > $game->reviews->getAverage(AverageType::REVIEW_POSITIVE)) {
                $game->modifiers()->create([
                    'title' => 'Above Average Total Positive Reviews',
                    'type' => ModifierType::AVERAGE_POSITIVE,
                    'color' => 'green',
                    'strength' => 10
                ]);
            } else {
                $game->modifiers()->create([
                    'title' => 'Below Average Total Positive Reviews',
                    'type' => ModifierType::AVERAGE_POSITIVE,
                    'color' => 'red',
                    'strength' => 10
                ]);
            }
        }

        if ($game->modifiers()->where('type', AverageType::REVIEW_POOR)->doesntExist()) {
            if ($game->reviews->total_negative > $game->reviews->getAverage(AverageType::REVIEW_POOR)) {
                $game->modifiers()->create([
                    'title' => 'Above Average Total Negative Reviews',
                    'type' => ModifierType::AVERAGE_NEGATIVE,
                    'color' => 'red',
                    'strength' => 10
                ]);
            } else {
                $game->modifiers()->create([
                    'title' => 'Below Average Total Negative Reviews',
                    'type' => ModifierType::AVERAGE_NEGATIVE,
                    'color' => 'green',
                    'strength' => 10
                ]);
            }
        }
    }
}
