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
    
    
        //サンプル追加
         public function ajaxlike(Request $request)
    {
        $review_id = $request->review_id;
        $like = new Like;
        $review = Review::findOrFail($review_id);

        // 空でない（既にいいねしている）なら
        if ($like->like_exist($review_id)) {
            //likesテーブルのレコードを削除
            $like = Like::where('review_id', review_id)->delete();
        } else {
            //空（まだ「いいね」していない）ならlikesテーブルに新しいレコードを作成する
            $like = new Like;
            $like->review_id = $request->review_id;
            $like->save();
        }

        //loadCountとすればリレーションの数を○○_countという形で取得できる（今回の場合はいいねの総数）
        $reviewLikesCount = $review->loadCount('likes')->likes_count;

        //一つの変数にajaxに渡す値をまとめる
        //今回ぐらい少ない時は別にまとめなくてもいいけど一応。笑
        $json = [
            'reviewLikesCount' => $reviewLikesCount,
        ];
        //下記の記述でajaxに引数の値を返す
        return response()->json($json);
    }
    
}

    //サンプル追加
    $(function () {
var like = $('.js-like-toggle');
var likeReviewId;

like.on('click', function () {
    var $this = $(this);
    likeReviewId = $this.data('reviewid');
    $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '/ajaxlike',  //routeの記述
            type: 'POST', //受け取り方法の記述（GETもある）
            data: {
                'review_id': likeReviewId //コントローラーに渡すパラメーター
            },
    })
        // Ajaxリクエストが成功した場合
        .done(function (data) {
//lovedクラスを追加
            $this.toggleClass('loved'); 

//.likesCountの次の要素のhtmlを「data.reviewLikesCount」の値に書き換える
            $this.next('.likesCount').html(data.reviewLikesCount); 

        })
        // Ajaxリクエストが失敗した場合
        .fail(function (data, xhr, err) {
//ここの処理はエラーが出た時にエラー内容をわかるようにしておく。
//とりあえず下記のように記述しておけばエラー内容が詳しくわかります。笑
            console.log('エラー');
            console.log(err);
            console.log(xhr);
        });
    
    return false;
});
});
//ここまで
    
    
    
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
