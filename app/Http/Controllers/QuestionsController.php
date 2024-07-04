<?php

namespace App\Http\Controllers;

use App\Http\Actions\Questions\IndexAnswerAction;
use App\Http\Actions\Questions\StoreAnswerAction;
use App\Http\Requests\StoreAnswerRequest;

class QuestionsController extends Controller
{
    public function store(StoreAnswerRequest $request)
    {
        $data = new StoreAnswerAction();
        return $data->execute($request);
    }

    public function index()
    {
        $data = new IndexAnswerAction();
        return $data->execute();
    }
}
