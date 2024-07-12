<?php

namespace App\Http\Controllers;

use App\Http\Actions\Cahpter\StoreChapterAction;
use Illuminate\Http\Request;

class ChapterController extends Controller
{
    public function store(Request $request)
    {
        $chapter = new StoreChapterAction();
        return $chapter->execute($request);
    }
}
