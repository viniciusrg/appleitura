<?php

namespace App\Http\Actions\Favorite;

use Illuminate\Support\Facades\Log;

class StoreFavoriteAction
{
    public function execute($request, $book_id)
    {
        try {
            $user = $request->user();

            if ($user->favorites()->find($book_id)){
                $user->favorites()->detach($book_id);
                return response()->json(['message:' => 'Successfully unfavorited.'], 200);
            }

            $user->favorites()->attach($book_id);

            return response()->json(['message:' => 'Successfully favorited.'], 200);
        } catch (\Exception $e) {
            Log::error(['Store favorite error: '] . $e);
            return response()->json(['message:' => $e->getMessage()], 500);
        }
    }
}
