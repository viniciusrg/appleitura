<?php

namespace App\Http\Actions\Questions;

use App\Http\Resources\QuestionResource;
use App\Models\Question;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class IndexAnswerAction
{
    public function execute()
    {
        try {
            $questions = Question::with('user')
            ->paginate(8);

            $questionsArray = $questions->map(function ($question) {
                return [
                    'q1' => $question->q1,
                    'q2' => $question->q2,
                    'q3' => $question->q3,
                    'user_email' => $question->user->email,
                ];
            })->toArray();

            return response()->json(['data' => $questionsArray], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error(['Index answer error: '] . $e);
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
