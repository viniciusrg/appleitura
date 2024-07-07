<?php

namespace App\Http\Controllers;

use App\Http\Actions\Book\IndexBookAction;
use App\Http\Actions\Book\ShowBookAction;
use App\Http\Actions\Book\StoreBookAction;
use App\Http\Actions\Book\ExclusiveBooksIndexAction;
use App\Http\Actions\Book\RandomIndexAction;
use App\Http\Actions\Book\TopWeekBooksAction;
use App\Http\Actions\Book\UpdateBookAction;
use App\Http\Requests\StoreBookRequest;
use Illuminate\Http\Request;

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

    public function index(Request $request)
    {
        $book = new IndexBookAction();
        return $book->execute($request);
    }

    public function show(Request $request, string $book_id)
    {
        $book = new ShowBookAction();
        return $book->execute($request, $book_id);
    }

    public function exclusiveBooksIndex(Request $request)
    {
        $book = new ExclusiveBooksIndexAction();
        return $book->execute($request);
    }

    public function topWeekBooksIndex(Request $request)
    {
        $book = new TopWeekBooksAction();
        return $book->execute($request);
    }

    public function randomIndex(Request $request)
    {
        $book = new RandomIndexAction();
        return $book->execute($request);
    }
}
