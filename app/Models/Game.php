<?php

namespace App\Models;

use App\Enums\ModifierType;
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

    protected static function booted()
    {
        static::retrieved(function ($game) {
            if (!$game->metas()->first()) {
                $game->metas()->create();
            }

            if (
                !$game->image()->first()
                || !$game->modifiers->where('type', ModifierType::PLATFORM)->count()
                || $game->metas->dlc === null
                || $game->metas->video === null
                || $game->metas->unreleased === null
                || $game->metas->free === null
            ) {
                $steam = new SteamService;

                $data = $steam->getGameInfoFromSteam($game->steam_app_id);

                // Delete game if no data was retrieved;
                if ($data == null) {
                    $game->delete();
                }

                $game->metas->addMetas($data);
                $game->addImageIfMissing($data['header_image']);
                $game->addPlatformModifier($data['platforms']);
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


    private function addImageIfMissing(string $image): void
    {
        if (!$this->image()->exists()) {
            $this->image()->create([
                'game_id' => $this->id,
                'image_url' => $image,
            ]);
        }
    }

    private function addPlatformModifier(array $platforms): void
    {
        if ($this->modifiers()
            ->where('type', ModifierType::PLATFORM)
            ->exists()
        ) {
            return;
        }

        $platforms = array_filter($platforms, fn ($platform) => $platform != false);

        switch (sizeof($platforms)) {
            case 1:
                $this->modifiers()->firstOrCreate([
                    'title' => 'Not many support platforms',
                    'type' => ModifierType::PLATFORM,
                    'color' => 'red',
                    'strength' => -10
                ]);
                break;
            case 2:
                $this->modifiers()->firstOrCreate([
                    'title' => 'An acceptable number of platforms',
                    'type' => ModifierType::PLATFORM,
                    'color' => 'gray',
                    'strength' => 0
                ]);
                break;
            case 3:
                $this->modifiers()->firstOrCreate([
                    'title' => 'Supports many platforms',
                    'type' => ModifierType::PLATFORM,
                    'color' => 'green',
                    'strength' => 10
                ]);
                break;
        }
    }
}
