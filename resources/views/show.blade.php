@extends('layouts.app')

@section('css')
    <link href="{{ asset('css/show.css') }}" rel="stylesheet">
@endsection
@section('content')
<div class="container">
  <h1 class='pagetitle'>レビュー詳細ページ</h1>
  <div class="card">
    <div class="card-body d-flex">
      <section class='review-main'>
        <h2 class='h2'>本のタイトル</h2>
        <p class='h2 mb20'>{{ $review->title }}</p>
        <h2 class='h2'>レビュー本文</h2>
        <p>{{ $review->body }}</p>
      </section>  
      <aside class='review-image'>
@if(!empty($review->image))
        <img class='book-image' src="{{ asset('storage/images/'.$review->image) }}">
@else
        <img class='book-image' src="{{ asset('images/dummy.png') }}">
@endif
      </aside>
    @if( Auth::check() )
        @if($like_model->like_exist(Auth::user()->id,$review->id))
          <p class="favorite-marke">
            <a class="js-like-toggle loved" href="" data-review_id="{{ $review->id }}"><i class="fas fa-heart"></i>いいね！ON</a>
            <span class="likesCount">{{$likes_count}}</span>
          </p>
        @else
          <p class="favorite-marke">
            <a class="js-like-toggle" href="" data-review_id="{{ $review->id }}"><i class="fas fa-heart"></i>いいね！OFF</a>
            <span class="likesCount">{{$likes_count}}</span>
          </p>
        @endif
      @else
        ログインしてね
      @endif      
    </div>
    <a href="{{ route('index') }}" class='btn btn-info btn-back mb20'>一覧へ戻る</a>
  </div>
</div>
@endsection