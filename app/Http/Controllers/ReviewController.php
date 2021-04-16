<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Like;
use Illuminate\Support\Facades¥Auth;
use App\Review;
use Auth;
use Log;
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
        $reviewLikesCount = Like::where('review_id', $review_id)->get()->count();

        $data = [
                'reviews' => $reviews,
                'like_model'=>$like_model,
            ];

        return view('reviews.index', $data);
    }
    
    
    public function like(Request $request)
    {
        $id = Auth::user()->id;
        $review_id = $request->review_id;
        
        Log::info('ログ出力テスト1');
        $like = new Like;
        $post = Review::where('review_id', $review_id);

         Log::info('ログ出力テスト2');

        // 空でない（既にいいねしている）なら
        if ($like->like_exist($id, $review_id)) {
            //likesテーブルのレコードを削除
            $like = Like::where('review_id', $review_id)->where('user_id', $id)->delete();
        } else {
            //空（まだ「いいね」していない）ならlikesテーブルに新しいレコードを作成する
            $like = new Like;
            $like->review_id = $request->review_id;
            $like->user_id = Auth::user()->id;
            $like->save();
        }

         Log::info('ログ出力テスト3');

        //loadCountとすればリレーションの数を○○_countという形で取得できる（今回の場合はいいねの総数）
        //$reviewLikesCount = $post->loadCount('likes')->likes_count;
        $reviewLikesCount = Like::where('review_id', $review_id)->get()->count();

        //一つの変数にajaxに渡す値をまとめる
        //今回ぐらい少ない時は別にまとめなくてもいいけど一応。笑
        $json = [
            'reviewLikesCount' => $reviewLikesCount,
        ];
        //下記の記述でajaxに引数の値を返す
        return response()->json($json);
    }    
    
    public function show($id)
{
    $review = Review::where('id', $id)->where('status', 1)->first();
    $like_model = new Like;
    $reviewLikesCount = Like::where('review_id', $id)->get()->count();
        $likes_count = $reviewLikesCount;
        
        return view('show', compact('review','like_model','likes_count'));
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
