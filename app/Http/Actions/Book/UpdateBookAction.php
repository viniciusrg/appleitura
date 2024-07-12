<?php

namespace App\Http\Actions\Book;

use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UpdateBookAction
{
    public function execute($request)
    {
        try {
            $data = $request->only(['book_id', 'title', 'description', 'author', 'read_time', 'content_audio', 'total_views', 'week_views', 'categories_id']);

            $book = Book::find($data['book_id']);

            if (!$book) {
                return response()->json(['message' => 'Book not found.'], 404);
            }

            if ($request->hasFile('cover') && $request->file('cover')->isValid()) {

                // Deletar a cover antiga
                if ($book->cover) {
                    Storage::disk('public')->delete($book->cover);
                }

                $coverPath = $request->file('cover')->store("covers/{$data['title']}", 'public');
                $data['cover'] = $coverPath;
            }

            $book->update($data);
            $book->save();
            $book->categories()->sync($data['categories_id']);

            return new BookResource($book);
        } catch (\Exception $e) {
            Log::error(['Update book error: '] . $e);
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
