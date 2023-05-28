<?php

namespace App\Actions\Game;

use App\Enums\AverageType;
use App\Enums\ModifierType;
use App\Models\Game;
use App\Models\Modifier;
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

        Modifier::create($game, ModifierType::REVIEW_DIST, [
            'positive' => 'Positive Review Distribution',
            'negative' => 'Poor Review Distribution',
        ], function () use ($game) {
            return $game->reviews->total_positive > $game->reviews->total_negative;
        });

        $averages = resolve(AveragesService::class);

        $averages->calculateAverage(AverageType::REVIEW_POSITIVE, 'reviews', 'total_positive');

        $averages->calculateAverage(AverageType::REVIEW_POOR, 'reviews', 'total_negative');

        Modifier::create($game, ModifierType::AVERAGE_POSITIVE, [
            'positive' => 'Above Average Total Positive Reviews',
            'negative' => 'Below Average Total Positive Reviews',
        ], function () use ($game) {
            return $game->reviews->total_positive > $game->reviews->getAverage(AverageType::REVIEW_POSITIVE);
        });

        Modifier::create($game, ModifierType::AVERAGE_NEGATIVE, [
            'positive' => 'Below Average Total Negative Reviews',
            'negative' => 'Above Average Total Negative Reviews',
        ], function () use ($game) {
            return $game->reviews->total_negative > $game->reviews->getAverage(AverageType::REVIEW_POOR);
        });
    }
}
