<?php

namespace App\Http\Actions\Chapter;

use App\Models\Book;
use App\Models\Chapter;
use Illuminate\Support\Facades\Log;

class StoreChapterAction
{
    public function execute($request)
    {
        try {
            $data = $request->only(['book_id', 'subtitle', 'chapter_number', 'content']);

            Book::findOrFail($data['book_id']);
            $chapter = Chapter::create($data);

            return ($chapter);
        } catch (\Exception $e) {
            Log::error('Store chapter error: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
