<?php

namespace App\Http\Controllers;

use App\Http\Actions\Book\IndexBookAction;
use App\Http\Actions\Book\ShowBookAction;
use App\Http\Actions\Book\StoreBookAction;
use App\Http\Requests\StoreBookRequest;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function store(StoreBookRequest $request)
    {
        $book = new StoreBookAction();
        return $book->execute($request);
    }

    public function index()
    {
        $book = new IndexBookAction();
        return $book->execute();
    }

    public function show(string $book_id)
    {
        $book = new ShowBookAction();
        return $book->execute($book_id);
    }
}
