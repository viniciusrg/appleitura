<?php

namespace App\Http\Actions\Book;

use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Support\Facades\Log;

class IndexBookAction
{
    public function execute($request)
    {
        try {
            $user = $request->user();

            $categoryIds = $user->categories->pluck('id')->toArray();
            $books = Book::InCategories($categoryIds)->orderBy('id', 'desc')->paginate(8);

            return BookResource::collection($books);
        } catch (\Exception $e) {
            Log::error(['Index book error: '] . $e);
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
