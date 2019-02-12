<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use App\Top_Index;

class TopIndexController extends Controller
{
    public function index()
    {
        $topIndex = Top_Index::get();
        return response()->json($topIndex);
    }

    public function show(Top_Index $item)
    {
        return response()->json($item);
    }

    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required|string',
        ]);

        if ($validation->fails()) {
            return response()->json($validation->errors(), 400);
        }

        $result = Top_Index::create([
            'name' => $request->name,
        ]);
        return response()->json($result);
    }

    public function update(Request $request, Top_Index $item)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required|string',
        ]);

        if ($validation->fails()) {
            return response()->json($validation->errors(), 400);
        }

        $item->name = $request->name;
        $item->save();
        return response()->json($item);
    }
}
