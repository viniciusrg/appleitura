<?php

namespace App\Http\Controllers;

use App\Http\Actions\KeepReading\IndexKeepReadingAction;
use App\Http\Actions\KeepReading\StoreKeepReadingAction;
use Illuminate\Http\Request;

class KeepReadingController extends Controller
{
    public function store(Request $request, string $book_id){
        $book = new StoreKeepReadingAction();
        return $book->execute($request, $book_id);
    }

    public function index(Request $request){
        $book = new IndexKeepReadingAction();
        return $book->execute($request);
    }
}
