<?php

namespace App\Http\Controllers;

use App\Http\Actions\Chapter\ShowChapterAction;
use App\Http\Actions\Chapter\StoreChapterAction;
use App\Http\Requests\StoreUpdateChapterRequest;

class ChapterController extends Controller
{
    public function store(StoreUpdateChapterRequest $request)
    {
        $chapter = new StoreChapterAction();
        return $chapter->execute($request);
    }

    public function show(string $book_id, string $chapter_number)
    {
        $chapter = new ShowChapterAction();
        return $chapter->execute($book_id, $chapter_number);
    }
}
