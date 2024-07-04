<?php

namespace App\Http\Actions\Book;

use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Support\Facades\Log;

class ShowBookAction
{
    public function execute($book_id)
    {
        try {
            $book = Book::find($book_id);

            if (!$book){
                return response()->json(['message:' => 'Book not found'], 404);
            }

            // dd($book->favorites()->get());

            return new BookResource($book);
        } catch (\Exception $e) {
            Log::error(['Show book error: '] . $e);
            return response()->json(['message:' => $e->getMessage()], 500);
        }
    }
}
