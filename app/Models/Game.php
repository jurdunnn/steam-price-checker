<?php

namespace App\Models;

use App\Enums\ModifierType;
use App\Services\SteamService;
use Exception;
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

    protected static function booted()
    {
        static::retrieved(function ($game) {
            if (!$game->metas()->first()) {
                $game->metas()->create();
            }

            if ($game->doesNotHaveRequiredData()) {
                $steam = new SteamService;

                $data = $steam->getGameInfoFromSteam($game->steam_app_id);

                // Delete game if no data was retrieved
                if ($data == null) {
                    $game->delete();

                    return;
                }

                $game->metas->addMetas($data);
                $game->addImageIfMissing($data);
                $game->addPlatformModifier($data);
                $game->addMetacriticScore($data);
            }
        });
    }

    public function toSearchableArray()
    {
        return [
            'title' => $this->title,
        ];
    }

    public function image(): HasOne
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
        return $this->image()->first()->image_url ?? null;
    }

    private function doesNotHaveRequiredData(): bool
    {
        return !$this->image()->first()
            || !$this->modifiers->where('type', ModifierType::PLATFORM)->count()
            || !$this->modifiers->where('type', ModifierType::METACRITIC)->count()
            || $this->metas->type === null
            || $this->metas->unreleased === null
            || $this->metas->free === null;
    }

    private function addImageIfMissing(array $data): void
    {
        $image = $data['header_image'] ?? null;

        if (!$this->image()->exists()) {
            $this->image()->create([
                'game_id' => $this->id,
                'image_url' => $image,
            ]);
        }
    }

    private function addPlatformModifier(array $data): void
    {
        if ($this->modifiers()
            ->where('type', ModifierType::PLATFORM)
            ->exists()
        ) {
            return;
        }

        $platforms = $data['platforms'] ?? null;

        if (!$platforms) {
            $this->modifiers()->firstOrCreate([
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
                $this->modifiers()->firstOrCreate([
                    'title' => 'Few Supported Platforms',
                    'type' => ModifierType::PLATFORM,
                    'color' => 'red',
                    'strength' => -10
                ]);
                break;
            case 2:
                $this->modifiers()->firstOrCreate([
                    'title' => 'Average Number of Platforms Supported',
                    'type' => ModifierType::PLATFORM,
                    'color' => 'gray',
                    'strength' => 0
                ]);
                break;
            case 3:
                $this->modifiers()->firstOrCreate([
                    'title' => 'Many Supported Platforms',
                    'type' => ModifierType::PLATFORM,
                    'color' => 'green',
                    'strength' => 10
                ]);
                break;
        }
    }

    private function addMetacriticScore(array $data)
    {
        if ($this->modifiers()
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

        $this->modifiers()->firstOrCreate($contents);
    }
}
