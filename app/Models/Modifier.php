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
}
