<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    //
    //いいねしている投稿
    public function review()
    {
        return $this->belongsTo(Reviews::class);
    }

    //いいねが既にされているかを確認
    public function like_exist($review_id)
    {
        //Likesテーブルのレコードにユーザーidと投稿idが一致するものを取得
        $exist = Like::where('review_id', '=', $review_id)->get();

        // レコード（$exist）が存在するなら
        if (!$exist->isEmpty()) {
            return true;
        } else {
        // レコード（$exist）が存在しないなら
            return false;
        }
    }
}
