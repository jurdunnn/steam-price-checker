<?php

namespace App\Models;

use App\Services\SteamService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'steam_app_id',
        'title',
    ];

    public function image(): HasOne
    {
        return $this->hasOne(GameImage::class);
    }

    public function getImageAttribute()
    {
        if (!GameImage::where('game_id', $this->id)->exists()) {
            $steam = new SteamService;

            $steam->createImageForGame(game: $this);
        }

        return $this->image()->first()->image_url ?? null;
    }
}
