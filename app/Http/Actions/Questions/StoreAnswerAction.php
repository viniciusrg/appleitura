<?php

namespace App\Http\Actions\Questions;

use App\Http\Resources\QuestionResource;
use App\Models\Question;
use Illuminate\Support\Facades\Log;

class StoreAnswerAction
{
    public function execute($request)
    {
        try {
            $data = $request->only(['q1', 'q2', 'q3']);
            $user = $request->user();

            if ($user->question){
                return response()->json(['message' => 'User already has a response.'], 400);
            }

            $questions = new Question($data);
            $user->question()->save($questions);

            return new QuestionResource($questions);
        } catch (\Exception $e) {
            Log::error(['Store answer error: '] . $e);
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
