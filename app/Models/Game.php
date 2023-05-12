<?php

namespace App\Models;

use App\Enums\ModifierType;
use App\Services\SteamService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'steam_app_id',
        'title',
    ];

    protected static function booted()
    {
        static::retrieved(function ($game) {
            if (
                !$game->image()->first()
                || !$game->modifiers->where('type', ModifierType::PLATFORM)->count()
            ) {
                $steam = new SteamService;

                $data = $steam->getGameInfoFromSteam($game->steam_app_id);

                // Delete game if no data was retrieved;
                if (!$data) {
                    $game->delete();
                }

                $game->addImageIfMissing($data['header_image']);
                $game->addPlatformModifier($data['platforms']);
            }
        });
    }

    public function image(): HasOne
    {
        return $this->hasOne(GameImage::class);
    }

    public function modifiers(): MorphToMany
    {
        return $this->morphToMany(Modifier::class, 'modifiable');
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
