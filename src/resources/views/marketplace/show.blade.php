@extends('layouts.base')

@section('title', $content->title.' | DigitalAssetPort')

@section('body')
  @include('layouts.header')

  <main class="app-main">
    @include('partials.flash')
    @include('partials.errors')

    <div class="detail-layout">
      <article>
        <div class="detail-image">
          <img src="{{ $content->thumbnail_url }}" alt="{{ $content->title }}">
        </div>

        <section class="panel" style="margin-top: 16px;">
          <div class="pill-row">
            <span class="pill">{{ $content->genre->name }}</span>
            <span class="pill pill--coral">{{ $content->subGenre->name }}</span>
          </div>
          <h1 class="section-title" style="margin-top: 12px;">{{ $content->title }}</h1>
          <p style="margin-top: 16px; white-space: pre-wrap;">{{ $content->description }}</p>

          <div class="pill-row" style="margin-top: 18px;">
            @foreach($content->tags as $tag)
              <span class="pill">{{ $tag->name }}</span>
            @endforeach
          </div>
        </section>

        <section class="panel" style="margin-top: 16px;" id="comments">
          <h2>コメント</h2>
          @auth
            <form action="{{ route('comments.store', $content) }}" method="POST" style="margin-bottom: 14px;">
              @csrf
              <textarea class="textarea" name="message" placeholder="コメントを書く">{{ old('message') }}</textarea>
              <div class="form-actions" style="margin-top: 10px;">
                <button class="button button--primary" type="submit">投稿</button>
              </div>
            </form>
          @else
            <p style="color: var(--muted); margin-bottom: 12px;"><a class="nav-link" href="{{ route('login') }}">ログイン</a>するとコメントできます。</p>
          @endauth

          @forelse($content->comments as $comment)
            <div class="comment" id="comment-{{ $comment->id }}">
              <img class="avatar-sm" src="{{ $comment->user->avatar_url }}" alt="">
              <div>
                <strong><a href="{{ route('profiles.show', $comment->user) }}">{{ $comment->user->display_name }}</a></strong>
                <p>{{ $comment->message }}</p>
              </div>
            </div>
          @empty
            <p style="color: var(--muted);">まだコメントはありません。</p>
          @endforelse
        </section>

        <section class="section">
          <div class="section-heading">
            <h2 class="section-title">{{ $content->author->display_name }}のコンテンツ</h2>
            <a class="nav-link" href="{{ route('profiles.show', $content->author) }}">もっと見る</a>
          </div>
          <div class="content-grid">
            @foreach($authorMore as $item)
              @include('partials.content-card', ['content' => $item])
            @endforeach
          </div>
        </section>

        <section class="section">
          <div class="section-heading">
            <h2 class="section-title">同じジャンルの人気コンテンツ</h2>
            <a class="nav-link" href="{{ route('search.advanced', ['genre' => $content->genre_id, 'sort' => 'favorites']) }}">もっと見る</a>
          </div>
          <div class="content-grid">
            @foreach($related as $item)
              @include('partials.content-card', ['content' => $item])
            @endforeach
          </div>
        </section>
      </article>

      <aside class="detail-side">
        <section class="panel">
          <h2>{{ $content->title }}</h2>
          <div class="meta-row">
            <img class="avatar-sm" src="{{ $content->author->avatar_url }}" alt="">
            <a href="{{ route('profiles.show', $content->author) }}">{{ $content->author->display_name }}</a>
          </div>
          <div class="spec-list">
            <div><span>評価率</span><strong>{{ $content->rating_label }}</strong></div>
            <div><span>評価数</span><strong>{{ number_format($content->ratings_count) }}</strong></div>
            <div><span>お気に入り</span><strong>{{ number_format($content->favorites_count) }}</strong></div>
            <div><span>価格</span><strong>{{ $content->formatted_price }}</strong></div>
          </div>

          <div class="form-actions" style="margin-top: 18px;">
            @auth
              <form method="POST" action="{{ route('favorites.toggle', $content) }}">
                @csrf
                <button class="icon-button" type="submit" aria-label="お気に入り">
                  <span class="material-symbols-outlined" aria-hidden="true">favorite</span>
                </button>
              </form>
              @if($content->user_id === auth()->id())
                <a class="button button--ghost" href="{{ route('contents.edit', $content) }}">編集</a>
              @else
                <form method="POST" action="{{ route('cart.store', $content) }}">
                  @csrf
                  <button class="button button--primary" type="submit">{{ $content->price === 0 ? 'ライブラリに追加' : '購入' }}</button>
                </form>
              @endif
            @else
              <a class="button button--primary" href="{{ route('login') }}">{{ $content->price === 0 ? 'ライブラリに追加' : '購入' }}</a>
            @endauth
          </div>
        </section>

        <section class="panel">
          <h2>データ情報</h2>
          <div class="spec-list">
            <div><span>ライセンス</span><strong>{{ $content->license_type }}</strong></div>
            <div><span>更新日時</span><strong>{{ $content->updated_at->format('Y/m/d') }}</strong></div>
            <div><span>販売日時</span><strong>{{ optional($content->published_at)->format('Y/m/d') }}</strong></div>
            <div><span>動作環境</span><strong>{{ $content->environment ?: '指定なし' }}</strong></div>
            <div><span>合計データサイズ</span><strong>{{ number_format($content->file_size_mb, 2) }}MB</strong></div>
          </div>
        </section>
      </aside>
    </div>
  </main>

  @include('layouts.footer')
@endsection
