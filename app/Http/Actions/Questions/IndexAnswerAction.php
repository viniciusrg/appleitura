<?php

namespace App\Http\Actions\Questions;

use App\Http\Resources\AnswerResource;
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
            $questions = Question::with('user')->paginate(8);

            return AnswerResource::collection($questions)->response()->setStatusCode(Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error(['Index answer error: '] . $e);
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
