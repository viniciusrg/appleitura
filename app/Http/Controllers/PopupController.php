<?php

namespace App\Http\Controllers;

use App\Http\Actions\Popup\IndexPopupAction;
use App\Http\Actions\Popup\StorePopupAction;
use App\Http\Requests\StorePopupRequest;

class PopupController extends Controller
{
    public function store(StorePopupRequest $request){
        $popup = new StorePopupAction();
        return $popup->execute($request);
    }

    public function index() {
        $popup = new IndexPopupAction();
        return $popup->execute();
    }
}
