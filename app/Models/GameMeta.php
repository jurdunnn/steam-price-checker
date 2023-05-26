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
        'type',
        'unreleased',
        'free'
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }
}
