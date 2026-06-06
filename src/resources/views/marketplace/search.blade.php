@extends('layouts.base')

@section('title', '詳細検索 | DigitalAssetPort')

@section('body')
  @include('layouts.header')

  <main class="app-main">
    <div class="section-heading">
      <div>
        <p class="section-eyebrow">Advanced search</p>
        <h1 class="section-title">詳細検索</h1>
      </div>
    </div>

    <form class="panel" action="{{ route('search.advanced') }}" method="GET">
      <div class="form-grid">
        <div class="field">
          <label for="keyword">キーワード</label>
          <input class="input" id="keyword" name="keyword" value="{{ request('keyword') }}">
        </div>
        <div class="field">
          <label for="exclude_keyword">除外キーワード</label>
          <input class="input" id="exclude_keyword" name="exclude_keyword" value="{{ request('exclude_keyword') }}">
        </div>
        <div class="field">
          <label for="tag">タグ</label>
          <input class="input" id="tag" name="tag" value="{{ request('tag') }}">
        </div>
        <div class="field">
          <label for="format">形式</label>
          <select class="select" id="format" name="format">
            <option value="">すべて</option>
            @foreach($formats as $key => $label)
              <option value="{{ $key }}" {{ request('format') === $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
          </select>
        </div>
        <div class="field">
          <label for="genre">ジャンル</label>
          <select class="select" id="genre" name="genre">
            <option value="">すべて</option>
            @foreach($genres as $genre)
              <option value="{{ $genre->id }}" {{ (int) request('genre') === $genre->id ? 'selected' : '' }}>{{ $genre->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="field">
          <label for="sub_genre">サブジャンル</label>
          <select class="select" id="sub_genre" name="sub_genre">
            <option value="">すべて</option>
            @foreach($subGenres as $subGenre)
              <option value="{{ $subGenre->id }}" {{ (int) request('sub_genre') === $subGenre->id ? 'selected' : '' }}>{{ $subGenre->genre->name }} / {{ $subGenre->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="field">
          <label for="max_price">価格 0〜￥10000+</label>
          <input class="input" id="max_price" type="range" name="max_price" min="0" max="10000" step="500" value="{{ request('max_price', 10000) }}" oninput="document.getElementById('priceLabel').textContent = '￥' + Number(this.value).toLocaleString()">
          <strong id="priceLabel">￥{{ number_format(request('max_price', 10000)) }}</strong>
        </div>
        <div class="field">
          <label for="sort">表示順序</label>
          <select class="select" id="sort" name="sort">
            @foreach($sorts as $key => $label)
              <option value="{{ $key }}" {{ request('sort', 'newest') === $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
          </select>
        </div>
        <div class="field">
          <label for="per_page">表示件数</label>
          <select class="select" id="per_page" name="per_page">
            @foreach([20, 50, 100] as $count)
              <option value="{{ $count }}" {{ (int) request('per_page', 20) === $count ? 'selected' : '' }}>{{ $count }}件</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="form-actions" style="margin-top: 18px;">
        <a class="button button--ghost" href="{{ route('search.advanced') }}">条件をリセット</a>
        <button class="button button--primary" type="submit">検索</button>
      </div>
    </form>

    <section class="section" aria-labelledby="search-results">
      <div class="section-heading">
        <h2 class="section-title" id="search-results">検索結果</h2>
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
  </main>

  @include('layouts.footer')
@endsection
