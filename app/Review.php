<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    //追加
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

}
