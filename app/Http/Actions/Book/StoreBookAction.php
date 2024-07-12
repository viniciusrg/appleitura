<?php

namespace App\Http\Actions\Book;

use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StoreBookAction
{
    public function execute($request)
    {
        DB::beginTransaction();
        try {
            $data = $request->only(['title', 'description', 'author', 'read_time', 'content', 'content_audio', 'categories_id']);

            if ($request->hasFile('cover') && $request->file('cover')->isValid()) {
                $coverPath = $request->file('cover')->store("covers/{$data['title']}", 'public');
                $data['cover'] = $coverPath;
            }
            
            $book = Book::create($data);
            $book->categories()->attach($data['categories_id']);

            DB::commit();

            return new BookResource($book);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Store book error: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
