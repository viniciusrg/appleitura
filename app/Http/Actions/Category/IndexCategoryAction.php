<?php

namespace App\Http\Actions\Category;

use App\Models\Category;
use Illuminate\Support\Facades\Log;

class IndexCategoryAction
{
    public function execute($request)
    {
        try {
            $categories = Category::all();
            return $categories;
            
        } catch (\Exception $e) {
            Log::error(['Index favorites error: ' . $e]);
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
