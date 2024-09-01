<?php

namespace App\Http\Controllers;

use App\Http\Actions\Category\IndexCategoryAction;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $category = new IndexCategoryAction();
        return $category->execute($request);
    }
}
