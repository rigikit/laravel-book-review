<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLikesTable extends Migration
{
    public function up()
    {
        Schema::create('likes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedbigInteger('review_id');
            $table->unsignedbigInteger('user_id');            
            $table->timestamps();

            $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');

            $table->foreign('review_id')
                    ->references('id')
                    ->on('reviews')
                    ->onDelete('cascade');

        });
    }

    public function down()
    {
        Schema::dropIfExists('likes');
    }
}
