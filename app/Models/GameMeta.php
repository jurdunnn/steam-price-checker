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
        if ($data['release_date']['date'] == 'Coming soon') {
            $releaseDate = true;
        } else {
            $releaseDate = Carbon::parse($data['release_date']['date']) > now();
        }

        $this->update([
            'free' => $data['is_free'],
            'dlc' => $data['type'] == 'dlc',
            'video' => $data['type'] == 'video',
            'unreleased' => $releaseDate,
        ]);
    }
}
