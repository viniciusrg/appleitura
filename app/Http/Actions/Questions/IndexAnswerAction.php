<?php

namespace App\Http\Actions\Questions;

use App\Http\Resources\QuestionResource;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class IndexAnswerAction
{
    public function execute()
    {
        try {
            $users = User::all();
            $questions = [];

            foreach ($users as $user) {
                $userQuestions = $user->question()->get();
                $questions[$user->id] = QuestionResource::collection($userQuestions);
            }

            return response()->json($questions, 200);
        } catch (\Exception $e) {
            Log::error(['Show answer error: '] . $e);
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
