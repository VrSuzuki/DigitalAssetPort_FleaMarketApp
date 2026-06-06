@extends('layouts.base')

@section('title', 'ライブラリ | DigitalAssetPort')

@section('body')
  @include('layouts.header')

  <main class="app-main">
    @include('partials.flash')
    <div class="section-heading">
      <div>
        <p class="section-eyebrow">Library</p>
        <h1 class="section-title">ライブラリ</h1>
      </div>
    </div>
    <div class="record-list">
      @forelse($contents as $content)
        <article class="record">
          <img src="{{ $content->thumbnail_url }}" alt="{{ $content->title }}">
          <div>
            <h2><a href="{{ route('contents.show', $content) }}">{{ $content->title }}</a></h2>
            <p style="color: var(--muted);">
              <img class="avatar-sm" src="{{ $content->author->avatar_url }}" alt="" style="display:inline-block; vertical-align:middle;">
              <a href="{{ route('profiles.show', $content->author) }}">{{ $content->author->display_name }}</a>
            </p>
          </div>
          <a class="button button--primary" href="{{ route('downloads.show', $content) }}">ダウンロード</a>
        </article>
      @empty
        <div class="empty-state">ライブラリにコンテンツはありません。</div>
      @endforelse
    </div>
    <div class="pagination">{{ $contents->links() }}</div>
  </main>

  @include('layouts.footer')
@endsection
