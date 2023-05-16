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

    public function addMetas(array $data): void
    {
        $this->update([
            'free' => $data['is_free'] ?? false,
            'type' => $data['type'] ?? 'undefined',
            'unreleased' => $this->isUnreleased($data) ?? false,
        ]);
    }

    private function isUnreleased($data)
    {
        $releaseDate = $data['release_date'] ?? null;
        $carbonDate = null;
        $isUnreleased = false;

        if (preg_match('/(\d{1,2}) (\w{3}), (\d{4})/', $releaseDate['date'], $matches)) {
            $day = $matches[1];
            $month = $matches[2];
            $year = $matches[3];

            $months = [
                'Jan' => 'January',
                'Feb' => 'February',
                'Mar' => 'March',
            ];

            if (isset($months[$month])) {
                $fullMonth = $months[$month];
                $formattedDate = $day . ' ' . $fullMonth . ', ' . $year;
                $carbonDate = Carbon::createFromFormat('d F, Y', $formattedDate)->setTimezone('UTC');
                $isUnreleased = $carbonDate > now();
            }
        }

        return $isUnreleased;
    }
}
