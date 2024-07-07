<?php

namespace App\Http\Actions\Book;

use App\Http\Resources\BookResource;
use App\Models\Book;
use App\Services\UserCategoryServices;
use Illuminate\Support\Facades\Log;

class ShowBookAction
{
    public function execute($request, $book_id)
    {
        try {
            // $book = Book::find($book_id);
            $user = $request->user();
            $categoryIds = UserCategoryServices::getCategoryIds($user);
            $book = Book::InCategories($categoryIds)->find($book_id);

            if (!$book) {
                return response()->json(['message' => 'Book not found'], 404);
            }

            // Incrementando o total_views do livro.
            $book->update(['total_views' => $book->total_views + 1]);

            // Gerenciando o week_views do livro.
            $book->update(['week_views' => $book->week_views + 1]);

            return new BookResource($book);
        } catch (\Exception $e) {
            Log::error(['Show book error: '] . $e);
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
