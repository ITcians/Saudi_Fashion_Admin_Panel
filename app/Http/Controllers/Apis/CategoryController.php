<?php

namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use App\Models\CategoryModel;
use Illuminate\Http\Request;
use stdClass;

class CategoryController extends Controller
{
    protected $res;

    function __construct()
    {
        $this->res = new stdClass;
    }

    function index(Request $request)
    {
        if ($request->input('search')) {
            //search
            return CategoryModel::where('category', 'like', '%' . $request->search . '%')->with(['sub_categories'])
                ->orderBy('category')
                ->get();
        }
        return CategoryModel::with(['sub_categories'])
            ->orderBy('category')
            ->get();
    }
}
