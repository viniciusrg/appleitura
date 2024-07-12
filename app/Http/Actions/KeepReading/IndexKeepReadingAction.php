<?php

namespace App\Http\Actions\KeepReading;

use App\Http\Resources\BookResource;
use Illuminate\Support\Facades\Log;

class IndexKeepReadingAction
{
    public function execute($request)
    {
        try {
            $user = $request->user();
            $books = $user->keepReadings()->paginate(8);

            return BookResource::collection($books);
        } catch (\Exception $e) {
            Log::error(['Index keepREadings error: '] . $e);
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
