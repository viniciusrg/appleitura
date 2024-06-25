<?php

namespace App\Http\Actions\Book;

use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Support\Facades\Log;

class StoreBookAction
{
    public function execute($request)
    {
        try {
            $data = $request->only(['title', 'description', 'author', 'read_time', 'content', 'content_audio']);

            $book = Book::create($data);

            return new BookResource($book);
        } catch (\Exception $e) {
            Log::error(['Store book error: '] . $e);
            return response()->json(['message:' => $e->getMessage()], 500);
        }
    }
}
