<?php

namespace App\Http\Livewire;

use App\Models\Game;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Search extends Component
{
    public string $search;

    public Collection $games;

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

        $this->games = Game::select(DB::raw('*, CASE WHEN title = "' . $search . '" THEN 0 ELSE 1 END as priority'))
            ->where('title', 'LIKE', '%' . $search . '%')
            ->orderByRaw('priority ASC')
            ->limit(5)
            ->get();

        if (empty($this->search)) {
            $this->games = collect();
        }
    }
}
