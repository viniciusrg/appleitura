<?php

namespace App\Http\Actions\Chapter;

use App\Models\Book;
use App\Models\Chapter;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class StoreChapterAction
{
    public function execute($request)
    {
        try {
            $data = $request->all();

            Book::findOrFail($data[0]['book_id']);

            foreach ($data as $dataItem) {
                Chapter::create($dataItem);
            }

            return Response('', Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Store chapter error: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
