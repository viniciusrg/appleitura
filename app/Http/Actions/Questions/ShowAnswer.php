<?php

namespace App\Http\Actions\Questions;

use App\Http\Resources\QuestionResponse;
use Illuminate\Support\Facades\Log;

class ShowAnswer
{
    public function execute($request)
    {
        try {
            $questions = $request->user()->question;

            if(!$questions){
                return response()->json([], 200);
            }

            return new QuestionResponse($questions);
        } catch (\Exception $e) {
            Log::error(['Show answer error: '] . $e);
            return response()->json(['message:' => $e->getMessage()]);
        }
    }
}
