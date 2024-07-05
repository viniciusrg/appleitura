<?php

namespace App\Http\Actions\Book;

use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Support\Facades\Log;

class ExclusiveBooksIndexAction
{
    public function execute()
    {
        try {
            $books = Book::orderBy('total_views')->take(10)->get();

            return BookResource::collection($books);
        } catch (\Exception $e) {
            Log::error(['Store book error: '] . $e);
            return response()->json(['message:' => $e->getMessage()], 500);
        }
    }
}
