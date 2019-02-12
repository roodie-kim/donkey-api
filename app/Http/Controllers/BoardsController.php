<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use App\Notifications\AddBoardRequest;
use App\Board;
use App\Top_Index;
use DB;

class BoardsController extends Controller
{
    public function index()
    {
        $boards = Top_Index::with([
                'boardCategories' => function($query) {
                    $query->with('boards');
                }
            ])
            ->get();

        return response()->json($boards);
    }

    public function recentIndex()
    {
        $boards = Board::orderBy('created_at', 'desc')
            ->take(10)->get();
        return response()->json($boards);
    }

    public function show($board)
    {
        $board = Board::with('boardInformation')
            ->where('name', $board)->first();

        return $board;
    }

    public function requestAddBoard(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required|string',
        ]);

        if ($validation->fails()) {
            return response()->json($validation->errors(), 400);
        }

        $user = $request->user();
        $boardRequest = $user->boardRequest()->create([
            'name' => $request->name,
        ]);

        $boardRequest->notify(new AddBoardRequest($boardRequest, $user));

        return response()->json(['message' => 'ok'], 201);
    }


// admin api
    public function adminIndex()
    {
        $boards = Board::with('boardCategory')->get();
        return response()->json($boards);
    }

    public function adminShow($item)
    {
        $board = Board::with('boardInformation')->find($item);

        return $board;
    }

    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'board_category_id' => 'required|integer',
            'name' => 'required|string',
            'description' => 'required|string',
        ]);

        if ($validation->fails()) {
            return response()->json($validation->errors(), 400);
        }

        DB::beginTransaction();
        try {
            $board = Board::create([
                'board_category_id' => $request->board_category_id,
                'name' => $request->name
            ]);

            $board->boardInformation()
                ->create([
                    'description' => $request->description,
                    'seo_image' => $request->seo_image,
                ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'internal server error'], 500);
        }
        DB::commit();

        return $board;
    }

    public function update(Request $request, $item)
    {
        $validation = Validator::make($request->all(), [
            'board_category_id' => 'required|integer',
            'name' => 'required|string',
        ]);

        if ($validation->fails()) {
            return response()->json($validation->errors(), 400);
        }

        $board = Board::find($item);

        DB::beginTransaction();
        try {
            $board->board_category_id = $request->board_category_id;
            $board->name = $request->name;
            $board->save();
            $board->boardInformation()
                ->updateOrCreate([
                    'description' => $request->description,
                    'seo_image' => $request->seo_image,
                ]);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'internal server error'], 500);
        }
        DB::commit();

        return $board;
    }
}
