<?php

namespace App\Http\Livewire;

use App\Models\Game;
use App\Services\SteamService;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Livewire\Component;

class Search extends Component
{
    public string $search;

    public Collection $games;

    private SteamService $steam;

    public function booted()
    {
        $this->steam = new SteamService;
    }

    public function mount()
    {
        $this->search = '';

        $this->games = collect();
    }

    public function render()
    {
        return view('livewire.search');
    }

    public function updatingSearch()
    {
        $this->games = collect();
    }

    public function updatedSearch()
    {
        $search = trim($this->search);

        $this->games = Game::where('title', 'LIKE', "%{$search}%")
            ->limit(5)
            ->get();
    }
}
