<?php

namespace App\Http\Controllers;

use App\Http\Actions\Book\IndexBookAction;
use App\Http\Actions\Book\ShowBookAction;
use App\Http\Actions\Book\StoreBookAction;
use App\Http\Actions\Book\TopBooksIndexAction;
use App\Http\Actions\Book\UpdateBookAction;
use App\Http\Requests\StoreBookRequest;

class BookController extends Controller
{
    public function store(StoreBookRequest $request)
    {
        $book = new StoreBookAction();
        return $book->execute($request);
    }

    public function update(StoreBookRequest $request)
    {
        $book = new UpdateBookAction();
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

    public function topBooksIndex()
    {
        $book = new TopBooksIndexAction();
        return $book->execute();
    }
}
