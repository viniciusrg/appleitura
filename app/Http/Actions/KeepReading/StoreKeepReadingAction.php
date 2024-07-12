<?php

namespace App\Http\Actions\KeepReading;

use Illuminate\Support\Facades\Log;

class StoreKeepReadingAction
{
    public function execute($request, $book_id)
    {
        try {
            $user = $request->user();

            if ($user->keepReadings()->find($book_id)){
                return;
            }

            $user->keepReadings()->attach($book_id);

            return response()->json(['message' => 'Successfully keepReadings.'], 200);
        } catch (\Exception $e) {
            Log::error(['Store keepReadings error: '] . $e);
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
