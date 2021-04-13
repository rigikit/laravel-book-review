<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LikeController extends Controller
{
    //
    public function index(Request $request) {

        return view('like.inde')->with('ip', $request->ip());

    }

    public function user_list() {

        return $this->getUsers(); // 全ユーザーを取得

    }

    public function like(Request $request) {

        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $result = false;
        $model = \App\User::class;
        $exists = \App\Like::where('model', $model)
            ->where('parent_id', $request->user_id)
            ->where('ip', $request->ip())
            ->exists();

        if(!$exists) {

            $like = new \App\Like();
            $like->model = $model;
            $like->parent_id = $request->user_id;
            $like->ip = $request->ip();
            $result = $like->save();

        }

        return [
            'result' => $result,
            'users' => $this->getUsers() // 全ユーザーを取得
        ];

    }

    private function getUsers() {

        return \App\User::with('likes')
            ->withCount('likes')
            ->get();

    }
}
