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

    public function toSearchableArray()
    {
        return [
            'title' => $this->title,
        ];
    }

    public function reviews(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    public function images(): HasOne
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
        return $this->images()->first()->image_url ?? null;
    }

    public function doesNotHaveRequiredData(): bool
    {
        return ($this->images()->doesntExist()
            || !$this->modifiers->where('type', ModifierType::PLATFORM)->count()
            || !$this->modifiers->where('type', ModifierType::METACRITIC)->count()
            || $this->metas->type === null
            || $this->metas->unreleased === null
            || $this->metas->free === null
            || $this->reviews()->doesntExist()
        );
    }

    public function steam(): SteamService
    {
        return new SteamService;
    }
}
