<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function search(string $search)
    {
        Game::flushEventListeners();

        return Game::select(DB::raw('id, title, CASE WHEN title = "' . $search . '" THEN 0 ELSE 1 END as priority'))
            ->where('title', 'LIKE', '%' . $search . '%')
            ->limit(5)
            ->get();
    }

    public function get(string $id)
    {
        return Game::where('id', $id)
            ->with('image')
            ->first();
    }
}
