<?php

namespace App\Http\Actions\Book;

use App\Http\Resources\BookResource;
use App\Models\Book;
use App\Services\UserAdminServices;
use App\Services\UserCategoryServices;
use Illuminate\Support\Facades\Log;

class SearchBookAction
{
    public function execute($request)
    {
        try {
            $query = $request->input('query');
            $user = $request->user();

            if (UserAdminServices::isAdmin($user) === 'admin') {
                $books = Book::where('title', 'LIKE', "%{$query}%")
                    ->orWhere('author', 'LIKE', "%{$query}%")
                    ->paginate(8);
            } else {
                $categoryIds = UserCategoryServices::getCategoryIds($user);

                $books = Book::InCategories($categoryIds)
                    ->where('title', 'LIKE', "%{$query}%")
                    ->orWhere('author', 'LIKE', "%{$query}%")
                    ->paginate(8);
            }

            return BookResource::collection($books);
        } catch (\Exception $e) {
            Log::error(['Show book error: '] . $e);
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
