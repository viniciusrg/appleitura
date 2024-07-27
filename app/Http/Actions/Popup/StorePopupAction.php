<?php

namespace App\Http\Actions\Popup;

use App\Http\Resources\PopupResource;
use App\Models\PopUp;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class StorePopupAction
{
    public function execute($request)
    {
        try {
            $data = $request->only(['link', 'image']);

            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $random = Str::random(10);
                $popupPath = $request->file('image')->store("popups/{$random}", 'public');
                $data['image'] = $popupPath;
            }

            $popup = PopUp::create($data);

            return new PopupResource($popup);
        } catch (\Exception $e) {
            Log::error('Store popup error: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
