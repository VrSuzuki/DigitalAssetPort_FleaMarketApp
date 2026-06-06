@extends('layouts.base')

@section('title', '購入履歴 | DigitalAssetPort')

@section('body')
  @include('layouts.header')

  <main class="app-main">
    <div class="section-heading">
      <div>
        <p class="section-eyebrow">Purchases</p>
        <h1 class="section-title">購入履歴</h1>
      </div>
      <a class="button button--primary" href="{{ route('library.index') }}">ライブラリを表示</a>
    </div>

    <div class="record-list">
      @forelse($orders as $order)
        @foreach($order->items as $item)
          <article class="record">
            <img src="{{ $item->content->thumbnail_url }}" alt="{{ $item->content->title }}">
            <div>
              <h2><a href="{{ route('purchases.show', $order) }}">{{ $item->content->title }}</a></h2>
              <p style="color: var(--muted);">
                {{ optional($order->purchased_at)->format('Y/m/d H:i') }}
                / <a href="{{ route('profiles.show', $item->content->author) }}">{{ $item->content->author->display_name }}</a>
              </p>
            </div>
            <strong class="price">¥{{ number_format($item->price) }}</strong>
          </article>
        @endforeach
      @empty
        <div class="empty-state">購入履歴はありません。</div>
      @endforelse
    </div>
    <div class="pagination">{{ $orders->links() }}</div>
  </main>

  @include('layouts.footer')
@endsection
