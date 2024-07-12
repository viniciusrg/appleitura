<?php

namespace App\Http\Actions\Book;

use App\Http\Resources\BookResource;
use App\Models\Book;
use App\Services\UserCategoryServices;
use Illuminate\Support\Facades\Log;

class ExclusiveBooksIndexAction
{
    public function execute($request)
    {
        try {
            $user = $request->user();
            $categoryIds = UserCategoryServices::getCategoryIds($user);
            $books = Book::InCategories($categoryIds)->orderBy('total_views', 'desc')->take(10)->get();

            return BookResource::collection($books);
        } catch (\Exception $e) {
            Log::error(['Store book error: '] . $e);
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
