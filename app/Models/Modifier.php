<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modifier extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function games()
    {
        return $this->morphedByMany(Game::class, 'modifiable');
    }

    /**
     * Create a modifier for the given game.
     *
     * @param Game $game The game instance for which the modifier is created.
     * @param int $type The type of the modifier.
     * @param array $titles An array of titles for the modifier based on different conditions.
     * @param callable $callback A callback function or closure that determines the result and specific modifier data.
     *
     * @return void
     */
    public static function create(Game $game, int $type, array $titles, callable $callback)
    {
        // Check if a modifier of the same type already exists for the game
        if ($game->modifiers()->where('type', $type)->exists()) {
            return;
        }

        // Define color and strength mappings as class constants
        $colors = [
            'positive' => 'green',
            'neutral' => 'gray',
            'negative' => 'red',
        ];

        $strengths = [
            'positive' => 10,
            'neutral' => 0,
            'negative' => -10,
        ];

        // Execute the callback to determine the result and specific modifier data
        $result = $callback();

        // Create the modifier based on the callback result
        switch ($result) {
            case true:
                $modifierData = [
                    'title' => $titles['positive'],
                    'color' => $colors['positive'],
                    'strength' => $strengths['positive'],
                ];
                break;
            case false:
                $modifierData = [
                    'title' => $titles['negative'],
                    'color' => $colors['negative'],
                    'strength' => $strengths['negative'],
                ];
                break;
            default:
                $modifierData = [
                    'title' => $titles['neutral'],
                    'color' => $colors['neutral'],
                    'strength' => $strengths['neutral'],
                ];
                break;
        }

        // Create the modifier
        $game->modifiers()->create(array_merge(['type' => $type], $modifierData));
    }
}
