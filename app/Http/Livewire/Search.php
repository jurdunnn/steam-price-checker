<?php

namespace App\Http\Livewire;

use App\Services\SteamService;
use Illuminate\Support\Arr;
use Livewire\Component;

class Search extends Component
{
    public string $search;

    public array $games;

    private SteamService $steam;

    public function booted()
    {
        $this->steam = new SteamService;
    }

    public function mount()
    {
        $this->search = '';

        $this->games = [];
    }

    public function render()
    {
        return view('livewire.search');
    }

    public function updatingSearch()
    {
        $this->games = [];
    }

    public function updatedSearch($value)
    {
        $this->setGames();

        $this->search = $value;
    }

    private function setGames()
    {
        $appids = $this->steam->search($this->search, 5);

        $games = [];

        foreach ($appids as $id) {
            $game = $this->steam->getSteamGame($id);

            if ($game) {
                $games[] = $game;
            }
        }

        $this->games = $games;
    }
}