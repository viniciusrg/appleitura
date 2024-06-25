<?php

namespace App\Http\Controllers;

use App\Http\Actions\Questions\ShowAnswer;
use App\Http\Actions\Questions\StoreAnswer;
use App\Http\Requests\StoreAnswerRequest;
use Illuminate\Http\Request;

class QuestionsController extends Controller
{
    public function store(StoreAnswerRequest $request)
    {
        $data = new StoreAnswer();
        return $data->execute($request);
    }

    public function show(Request $request)
    {
        $data = new ShowAnswer();
        return $data->execute($request);
    }
}
