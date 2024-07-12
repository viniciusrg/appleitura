<?php

namespace App\Http\Actions\Favorite;

use App\Http\Resources\BookResource;
use Illuminate\Support\Facades\Log;

class IndexFavoriteAction
{
    public function execute($request)
    {
        try {
            $user = $request->user();
            $favorites = $user->favorites()->paginate(8);

            return BookResource::collection($favorites);
        } catch (\Exception $e) {
            Log::error(['Index favorites error: '] . $e);
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
