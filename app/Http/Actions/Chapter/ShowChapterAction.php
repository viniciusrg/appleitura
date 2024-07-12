<?php

namespace App\Http\Actions\Chapter;

use App\Models\Book;
use App\Models\Chapter;
use Illuminate\Support\Facades\Log;

class ShowChapterAction
{
    public function execute($book_id, $chapter_number)
    {
        try {

            Book::findOrFail($book_id);
            $chapter = Chapter::findOrFail($chapter_number);

            return ($chapter);
        } catch (\Exception $e) {
            Log::error('Show chapter error: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
