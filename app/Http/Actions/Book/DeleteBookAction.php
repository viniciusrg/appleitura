<?php

namespace App\Http\Actions\Book;

use App\Models\Book;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class DeleteBookAction
{
    public function execute($request)
    {
        try {
            Book::findOrFail($request->book_id)->delete();

            return response('', Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error(['Update book error: ' . $e]);
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
