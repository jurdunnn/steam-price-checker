<?php

namespace App\Models;

use App\Enums\AverageType;
use App\Enums\ModifierType;
use App\Services\AveragesService;
use App\Services\SteamService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Laravel\Scout\Searchable;

class Game extends Model
{
    use HasFactory, Searchable;

    protected $fillable = [
        'steam_app_id',
        'title',
    ];

    public function toSearchableArray()
    {
        return [
            'title' => $this->title,
        ];
    }

    public function reviews(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    public function images(): HasOne
    {
        return $this->hasOne(GameImage::class);
    }

    public function modifiers(): MorphToMany
    {
        return $this->morphToMany(Modifier::class, 'modifiable');
    }

    public function metas(): HasOne
    {
        return $this->hasOne(GameMeta::class);
    }

    public function getMetasAttribute()
    {
        return $this->metas()->first();
    }

    public function getImageAttribute(): ?string
    {
        return $this->images()->first()->image_url ?? null;
    }

    public function doesNotHaveRequiredData(): bool
    {
        return ($this->images()->doesntExist()
            || !$this->modifiers->where('type', ModifierType::PLATFORM)->count()
            || !$this->modifiers->where('type', ModifierType::METACRITIC)->count()
            || $this->metas->type === null
            || $this->metas->unreleased === null
            || $this->metas->free === null
            || $this->reviews()->doesntExist()
        );
    }

    public function addReviews(): void
    {
        if ($this->reviews()->exists()) {
            return;
        }

        $data = $this->steam()->getReviews($this->steam_app_id)['query_summary'];

        $this->reviews()->create([
            'total_positive' => $data['total_positive'],
            'total_negative' => $data['total_negative'],
            'total_reviews' => $data['total_reviews'],
        ]);

        if ($this->modifiers()
            ->where('type', ModifierType::REVIEW_DIST)
            ->exists()
        ) {
            return;
        }

        if ($this->reviews->total_positive > $this->reviews->total_negative) {
            $this->modifiers()->create([
                'title' => 'Positive Review Distribution',
                'type' => ModifierType::REVIEW_DIST,
                'color' => 'green',
                'strength' => 10
            ]);
        } else {
            $this->modifiers()->create([
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

        if ($this->reviews->total_positive > $this->reviews->getAverage(AverageType::REVIEW_POSITIVE)) {
            $this->modifiers()->create([
                'title' => 'Above Average Total Positive Reviews',
                'type' => ModifierType::AVERAGE_POSITIVE,
                'color' => 'green',
                'strength' => 10
            ]);
        } else {
            $this->modifiers()->create([
                'title' => 'Below Average Total Positive Reviews',
                'type' => ModifierType::AVERAGE_POSITIVE,
                'color' => 'red',
                'strength' => 10
            ]);
        }

        if ($this->reviews->total_negative > $this->reviews->getAverage(AverageType::REVIEW_POOR)) {
            $this->modifiers()->create([
                'title' => 'Above Average Total Negative Reviews',
                'type' => ModifierType::AVERAGE_NEGATIVE,
                'color' => 'red',
                'strength' => 10
            ]);
        } else {
            $this->modifiers()->create([
                'title' => 'Below Average Total Negative Reviews',
                'type' => ModifierType::AVERAGE_NEGATIVE,
                'color' => 'green',
                'strength' => 10
            ]);
        }
    }

    public function steam(): SteamService
    {
        return new SteamService;
    }
}
