<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use DB;
use App\Post;

class VotesController extends Controller
{
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
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

        DB::beginTransaction();
        try {
            $user->votes()->create([
                'post_id' => $request->post_id,
                'type' => $request->type,
            ]);
            $this->changeHideTime($post);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'internal server error'], 500);
        }
        DB::commit();

        return response()->json(['message' => 'successfully voted'], 201);
    }

    public function changeHideTime($post)
    {
        $upvotesCount = $post->votes()->where('type', 'up')->count();
        $downvotesCount = $post->votes()->where('type', 'down')->count();

        $secondsToAdd = ($upvotesCount - $downvotesCount) * 10;

        if ($secondsToAdd >= 600) {
            $secondsToAdd = 600;
        } else if ($secondsToAdd <= -600) {
            $secondsToAdd = -600;
        }

        $changedHidedAt = Carbon::parse($post->created_at)
            ->addMinutes(30)->addSeconds($secondsToAdd);
        $post->hided_at = $changedHidedAt;
        $post->save();
    }
}
