<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Board_Category;
use App\Top_Index;

class BoardCategoriesController extends Controller
{
    public function index()
    {
        $categories = Board_Category::with('topIndex')->get();
        return $categories;
    }

    public function show(Board_Category $item)
    {
        return response()->json($item);
    }

    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'top_index_id' => 'required|integer',
            'name' => 'required|string',
        ]);

        if ($validation->fails()) {
            return response()->json($validation->errors(), 400);
        }

        $topIndex = Top_Index::find($request->top_index_id);

        $boardCategory = $topIndex->boardCategories()
            ->create([
                'name' => $request->name,
            ]);

        return response()->json($boardCategory);
    }

    public function update(Request $request, Board_Category $item)
    {
        $validation = Validator::make($request->all(), [
            'top_index_id' => 'required|integer',
            'name' => 'required|string',
        ]);

        if ($validation->fails()) {
            return response()->json($validation->errors(), 400);
        }

        $item->name = $request->name;
        $item->top_index_id = $request->top_index_id;
        $item->save();
        return response()->json($item);
    }
}
