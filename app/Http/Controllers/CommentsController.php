<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use App\Comment;

class CommentsController extends Controller
{
    public function index (Request $request)
    {
        $validation = Validator::make($request->all(), [
            'post_id' => 'required|integer',
            'page' => 'required|integer',
        ]);

        if ($validation->fails()) {
            return response()->json($validation->errors(), 400);
        }

        $perPage = 50;

        $skip = ($request->page - 1) * $perPage;

        $comments = Comment::where('post_id', $request->post_id)->orderBy('id', 'desc')
            ->skip($skip)->take($perPage)
            ->with('user')->get();

        $result['comments'] = $comments;
        $result['count'] = Comment::where('post_id', $request->post_id)->count();

        return response()->json($result);
    }

    public function store (Request $request)
    {
        $validation = Validator::make($request->all(), [
            'post_id' => 'required|integer',
            'comment_id' => 'nullable|integer',
            'body' => 'required',
        ]);

        if ($validation->fails()) {
            return response()->json($validation->errors(), 400);
        }

        $user = $request->user();

        $comment = $user->comments()->create([
            'post_id' => $request->post_id,
            'comment_id' => $request->comment_id,
            'body' => $request->body,
        ]);
        $comment->user = $user;

        return response()->json($comment);
    }

    public function update (Request $request, Comment $comment)
    {
        $validation = Validator::make($request->all(), [
            'body' => 'required',
        ]);

        if ($validation->fails()) {
            return response()->json($validation->errors(), 400);
        }

        $user = $request->user();
        if ($user->id !== $comment->user_id) {
            return response()->json(['message' => 'not your comment'], 401);
        }

        $comment->body = $request->body;
        $comment->save();

        return response()->json($comment);
    }

    public function delete (Request $request, Comment $comment)
    {
        $user = $request->user();
        if ($user->id !== $comment->user_id) {
            return response()->json(['message' => 'not your comment'], 401);
        }

        $comment->delete();

        return response()->json(['message' => 'comment deleted'], 200);
    }

}
