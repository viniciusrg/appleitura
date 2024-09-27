<?php

namespace App\Http\Actions\Book;

use App\Http\Resources\ShowBookResource;
use App\Models\Book;
use App\Services\UserAdminServices;
use App\Services\UserCategoryServices;
use Illuminate\Support\Facades\Log;

class ShowBookAction
{
    public function execute($request, $book_id)
    {
        try {
            $user = $request->user();

            if (UserAdminServices::isAdmin($user) === 'admin') {
                $book = Book::find($book_id);
            } else {
                $categoryIds = UserCategoryServices::getCategoryIds($user);
                $book = Book::InCategories($categoryIds)->find($book_id);
                $book->chapters = "teste";
            }

            if (!$book) {
                return response()->json(['message' => 'Book not found'], 404);
            }

            $book->update(['total_views' => $book->total_views + 1]);
            $book->update(['week_views' => $book->week_views + 1]);

            return new ShowBookResource($book);
        } catch (\Exception $e) {
            Log::error(['Show book error: ' . $e]);
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
