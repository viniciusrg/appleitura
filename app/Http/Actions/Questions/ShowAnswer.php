<?php

namespace App\Http\Actions\Questions;

use App\Http\Resources\QuestionResponse;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class ShowAnswer
{
    public function execute($user_id)
    {
        try {
            $user = User::find($user_id);
            if (is_null($user)) {
                return response()->json(['message' => 'User not found'], 404);
            }
            $questions = $user->question;

            if (!$questions) {
                return response()->json([], 200);
            }

            return new QuestionResponse($questions);
        } catch (\Exception $e) {
            Log::error(['Show answer error: '] . $e);
            return response()->json(['message:' => $e->getMessage()], 500);
        }
    }
}
