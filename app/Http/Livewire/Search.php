<?php

namespace App\Http\Livewire;

use App\Services\SteamService;
use Livewire\Component;

class Search extends Component
{
    public $search;

    public function render()
    {
        return view('livewire.search');
    }

    public function updated()
    {
        $steam = new SteamService();

        $reponse = $steam->search($this->search);

        dd($reponse);
    }
}
