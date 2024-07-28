<?php

namespace App\Http\Actions\Popup;

use App\Http\Resources\PopupResource;
use App\Models\PopUp;
use Illuminate\Support\Facades\Log;

class IndexPopupAction
{
    public function execute()
    {
        try {
            $popup = PopUp::latest()->first();
            
            if (!$popup){
                return ;
            }

            return new PopupResource($popup);
        } catch (\Exception $e) {
            Log::error(['Index popup error: '] . $e);
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
