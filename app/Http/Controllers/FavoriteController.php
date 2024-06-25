<?php

namespace App\Http\Controllers;

use App\Http\Actions\Favorite\IndexFavoriteAction;
use App\Http\Actions\Favorite\StoreFavoriteAction;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function store(Request $request, string $book_id)
    {
        $favorite = new StoreFavoriteAction();
        return $favorite->execute($request, $book_id);
    }

    public function index(Request $request)
    {
        $favorite = new IndexFavoriteAction();
        return $favorite->execute($request);
    }
}
