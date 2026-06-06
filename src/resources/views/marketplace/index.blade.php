@extends('layouts.base')

@section('title', 'DigitalAssetPort | デジタルデータのマーケット')
@section('body_class', 'page-marketplace')

@section('body')
  @include('layouts.header')

  <main class="app-main">
    @include('partials.flash')

    <section class="hero hero--image" aria-labelledby="hero-title">
      <div class="hero__content">
        <p class="eyebrow">Digital data marketplace</p>
        <h1 id="hero-title">ファイルにできる価値を、気兼ねなく売る。</h1>
        <p>Excel、Word、Notion、動画教材、システム一式、店舗マニュアルまで。すぐ使えるデジタルアセットを探して、投稿して、ライブラリに積み上げる場所です。</p>
        <div class="hero__actions">
          <a class="button button--primary" href="{{ route('search.advanced') }}">詳細検索</a>
          <a class="button button--secondary" href="{{ auth()->check() ? route('contents.create') : route('login') }}">アップロード</a>
          <a class="button button--ghost" href="{{ route('about') }}">DigitalAssetPortとは</a>
        </div>
      </div>
    </section>

    <div class="toolbar">
      <a class="button button--ghost" href="{{ route('home') }}">
        <span class="material-symbols-outlined" aria-hidden="true">restart_alt</span>
        検索条件をリセット
      </a>
      <form class="toolbar__group" action="{{ route('home') }}" method="GET">
        <input type="hidden" name="keyword" value="{{ request('keyword') }}">
        <select class="select" name="sort" aria-label="表示順序" onchange="this.form.submit()">
          @foreach($sorts as $key => $label)
            <option value="{{ $key }}" {{ request('sort', 'newest') === $key ? 'selected' : '' }}>{{ $label }}</option>
          @endforeach
        </select>
        <select class="select" name="per_page" aria-label="表示件数" onchange="this.form.submit()">
          @foreach([20, 50, 100] as $count)
            <option value="{{ $count }}" {{ (int) request('per_page', 20) === $count ? 'selected' : '' }}>{{ $count }}件</option>
          @endforeach
        </select>
      </form>
    </div>

    <div class="layout-grid">
      <aside class="sidebar" aria-label="サイドメニュー">
        <h2>ジャンル</h2>
        <div class="side-list">
          @foreach($genres as $genre)
            <a href="{{ route('home', ['genre' => $genre->id]) }}">
              <span>{{ $genre->name }}</span>
              <strong>{{ $genre->contents_count }}</strong>
            </a>
          @endforeach
        </div>

        <h2 style="margin-top: 24px;">フォロー中</h2>
        <div class="side-list">
          @auth
            @forelse(auth()->user()->following()->take(5)->get() as $following)
              <a href="{{ route('profiles.show', $following) }}">{{ $following->display_name }}</a>
            @empty
              <span>まだフォローはありません。</span>
            @endforelse
          @else
            <a href="{{ route('login') }}">ログインして表示</a>
          @endauth
        </div>

        <h2 style="margin-top: 24px;">投稿者一覧</h2>
        <div class="side-list">
          @foreach($authors as $author)
            @if($author->handle)
              <a href="{{ route('profiles.show', $author) }}">
                <span>{{ $author->display_name }}</span>
                <strong>{{ $author->contents_count }}</strong>
              </a>
            @endif
          @endforeach
        </div>
      </aside>

      <section aria-labelledby="items-title">
        <div class="section-heading">
          <div>
            <p class="section-eyebrow">Assets</p>
            <h2 class="section-title" id="items-title">投稿されたコンテンツ</h2>
          </div>
          <a class="nav-link" href="{{ route('search.advanced') }}">詳細検索へ</a>
        </div>

        @if($contents->count())
          <div class="content-grid">
            @foreach($contents as $content)
              @include('partials.content-card', ['content' => $content])
            @endforeach
          </div>
          <div class="pagination">{{ $contents->links() }}</div>
        @else
          <div class="empty-state">条件に合うコンテンツがありません。</div>
        @endif
      </section>
    </div>
  </main>

  @include('layouts.footer')
@endsection
