<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Board;
use App\Post;

class PostsController extends Controller
{
    public function index(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'board_name' => 'required|string',
            'page' => 'required|integer',
        ]);

        if ($validation->fails()) {
            return response()->json($validation->errors(), 400);
        }

        $user = Auth::guard('api')->user();

        $perPage = 50;
        $skip = ($request->page - 1) * $perPage;

        $boardId = Board::where('name', $request->board_name)->value('id');

        $posts = Post::where('board_id', $boardId)
            ->skip($skip)->take($perPage)
            ->orderBy('id', 'desc')
            ->with('user')
            ->withCount([
                'comments',
                'votes as up_count' => function ($query) {
                    $query->where('type', 'up');
                },
                'votes as down_count' => function ($query) {
                    $query->where('type', 'down');
                },
            ])
            ->get();

        // votes object 모양 수정
        $posts = $posts
            ->map(function ($post) {
                $votes = new \stdClass();
                $votes->up_count = $post->up_count;
                $votes->down_count = $post->down_count;
                $post->votes = $votes;

                unset($post->up_count); unset($post->down_count);
                return $post;
            });

        $result['posts'] = $posts;
        $result['count'] = Post::where('board_id', $boardId)->count();

        return response()->json($result);
    }

    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'board_name' => 'required|string',
            'title' => 'required|max:100',
            'body' => 'required',
        ]);

        if ($validation->fails()) {
            return response()->json($validation->errors(), 400);
        }

        $bodyIsEmpty = $this->isEmptyBody($request->body);
        if ($bodyIsEmpty) {
            $errorMessage= new \stdClass();
            $errorMessage->body = ['body is empty'];
            return response()->json($errorMessage, 400);
        }

        $boardId = Board::where('name', $request->board_name)->value('id');

        $user = $request->user();

        $post = $user->posts()->create([
            'board_id' => $boardId,
            'title' => $request->title,
            'body' => $request->body,
        ]);

        return response()->json($post, 201);
    }

    // 빈 포스트 확인 펑션
    private function isEmptyBody($string)
    {
        $stringsToCheckArray = ['&nbsp;', ' ', '<p>', '</p>'];
        foreach ($stringsToCheckArray as $stringToCheck) {
            $strArray = explode($stringToCheck, $string);
            $string = $this->attachStrings($strArray);
        }

        if ($string === '') {
            return true;
        } else {
            return false;
        }
    }

    private function attachStrings($strArray)
    {
        $string = '';
        foreach ($strArray as $str) {
            $string = $string . $str;
        }
        return $string;
    }


    public function show($post)
    {
        $user = Auth::guard('api')->user();

        $post = Post::where('id', $post)
            ->with(['user', 'myVote'])
            ->withCount([
                'comments',
                'votes as up_count' => function ($query) {
                    $query->where('type', 'up');
                },
                'votes as down_count' => function ($query) {
                    $query->where('type', 'down');
                },
            ])->first();

        $post->view_count ++;
        $post->save();

        $votes = new \stdClass();
        $votes->up_count = $post->up_count;
        $votes->down_count = $post->down_count;
        $post->votes = $votes;
        $post->has_voted = $user ? $post->votes()->where('user_id', $user->id)->exists() : false;

        if ($post->has_voted) {
            $post->my_vote_type = $post->myVote->type;
        } else {
            $post->my_vote_type = null;
        }

        unset($post->up_count); unset($post->down_count); unset($post->myVote);
        return $post;
    }

    public function update(Request $request, Post $post)
    {
        $validation = Validator::make($request->all(), [
            'title' => 'required|max:100',
            'body' => 'required',
        ]);

        if ($validation->fails()) {
            return response()->json($validation->errors(), 400);
        }

        $bodyIsEmpty = $this->isEmptyBody($request->body);
        if ($bodyIsEmpty) {
            $errorMessage= new \stdClass();
            $errorMessage->body = ['body is empty'];
            return response()->json($errorMessage, 400);
        }

        $user = $request->user();
        if ($user->id !== $post->user_id) {
            return response()->json(['message' => 'not your post'], 401);
        }

        $post->title = $request->title;
        $post->body = $request->body;
        $post->save();

        return $post;
    }

    public function delete(Request $request, Post $post)
    {
        $user = $request->user();
        if ($user->id !== $post->user_id) {
            return response()->json(['message' => 'not your post'], 401);
        }

        $post->delete();

        return response()->json(['message' => 'post deleted'], 200);
    }

    public function bestIndex(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'board_name' => 'required|string',
        ]);

        if ($validation->fails()) {
            return response()->json($validation->errors(), 400);
        }

        $boardId = Board::where('name', $request->board_name)->value('id');

        $posts = Post::withCount('votes')->where('board_id', $boardId)
            ->orderBy('votes_count', 'desc')
            ->where('created_at', '>=', Carbon::now()->subDays(3))->take(20)
            ->get();

        return $posts;
    }
}
