<?php

namespace App\Http\Actions\Book;

use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Support\Facades\Log;

class UpdateBookAction
{
    public function execute($request)
    {
        try {
            $data = $request->only(['book_id', 'title', 'description', 'author', 'read_time', 'content', 'content_audio', 'total_views', 'week_views']);

            $book = Book::find($data['book_id']);

            if (!$book){
                return response()->json(['message' => 'Book not found.'], 404);
            }

            $book->update($data);
            $book->save();

            return new BookResource($book);
        } catch (\Exception $e) {
            Log::error(['Update book error: '] . $e);
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
