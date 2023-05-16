<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameMeta extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'dlc',
        'video',
        'unreleased',
        'free'
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function addMetas(array $data): void
    {
        $releaseDate = $data['release_date'] ?? null;

        if ($data['release_date']) {
            return;
        }

        if ($releaseDate['date'] == 'Coming soon' || $releaseDate['date'] == 'To be announced') {
            $isUnreleased = false;
        } else {
            $carbonDate = Carbon::parse($releaseDate['date']) ?? null;

            $isUnreleased = $carbonDate ? $carbonDate > now() : false;
        }

        $this->update([
            'free' => $data['is_free'] ?? 0,
            'dlc' => $data['type'] == 'dlc' ?? 0,
            'video' => $data['type'] == 'video' ?? 0,
            'unreleased' => $isUnreleased,
        ]);
    }
}
