<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use App\Post;

class VotesController extends Controller
{
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'board_id' => 'required|integer',
            'post_id' => 'required|integer',
            'type' => 'required|string',
        ]);

        if ($validation->fails()) {
            return response()->json($validation->errors(), 400);
        }

        $user = $request->user();

        $post = Post::where('id', $request->post_id)
            ->with(['votes' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }])->first();

        if ($post->votes->count() !== 0) {
            return response()->json(['message' => 'you have already voted'], 400);
        }

        $user->votes()->create([
            'board_id' => $request->board_id,
            'post_id' => $request->post_id,
            'type' => $request->type,
        ]);

        return response()->json(['message' => 'successfully voted'], 201);
    }
}
