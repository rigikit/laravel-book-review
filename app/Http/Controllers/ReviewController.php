<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Like;
use Illuminate\Support\Facades¥Auth;
use App\Review;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::where('status', 1)->orderBy('created_at', 'DESC')->paginate(9);

        return view('index', compact('reviews'));
        
        //サンプル追加
        $data = [];
        // ユーザの投稿の一覧を作成日時の降順で取得
        //withCount('テーブル名')とすることで、リレーションの数も取得できます。
        $reviews = Review::withCount('likes')->orderBy('created_at', 'desc')->paginate(10);
        $like_model = new Like;

        $data = [
                'reviews' => $reviews,
                'like_model'=>$like_model,
            ];

        return view('reviews.index', $data);
         //
    }
    
    
    public function show($id)
{
    $review = Review::where('id', $id)->where('status', 1)->first();

    return view('show', compact('review'));
}
    
    public function create()
    {
        return view('review');
    }
    
     public function store(Request $request)
    {
        $post = $request->all();
        
        $validatedData = $request->validate([
            'title' => 'required|max:255',
            'body' => 'required',
            'image' => 'mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        
        if ($request->hasFile('image')) {
            $request->file('image')->store('/public/images');
            $data = ['user_id' => \Auth::id(), 'title' => $post['title'], 'body' => $post['body'], 'image' => $request->file('image')->hashName()];
        } else {
            $data = ['user_id' => \Auth::id(), 'title' => $post['title'], 'body' => $post['body']];
        }
        
        Review::insert($data);

        return redirect('/')->with('flash_message', '投稿が完了しました');
    }
}
