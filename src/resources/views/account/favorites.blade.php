@extends('layouts.base')

@section('title', 'お気に入り | DigitalAssetPort')

@section('body')
  @include('layouts.header')

  <main class="app-main">
    @include('partials.flash')
    <div class="section-heading">
      <div>
        <p class="section-eyebrow">Favorites</p>
        <h1 class="section-title">お気に入りコンテンツ</h1>
        <p style="color: var(--muted);">{{ number_format($contents->total()) }}件</p>
      </div>
    </div>
    @if($contents->count())
      <div class="content-grid">
        @foreach($contents as $content)
          @include('partials.content-card', ['content' => $content])
        @endforeach
      </div>
      <div class="pagination">{{ $contents->links() }}</div>
    @else
      <div class="empty-state">お気に入りに入れたコンテンツはありません。</div>
    @endif
  </main>

  @include('layouts.footer')
@endsection
