@extends('layouts.base')

@section('title', 'カート | DigitalAssetPort')

@section('body')
  @include('layouts.header')

  <main class="app-main">
    @include('partials.flash')
    <div class="detail-layout">
      <section>
        <div class="section-heading">
          <div>
            <p class="section-eyebrow">Cart</p>
            <h1 class="section-title">カートに入っているコンテンツ</h1>
          </div>
        </div>
        <div class="record-list">
          @forelse($cart->items as $item)
            <article class="record">
              <img src="{{ $item->content->thumbnail_url }}" alt="{{ $item->content->title }}">
              <div>
                <h2><a href="{{ route('contents.show', $item->content) }}">{{ $item->content->title }}</a></h2>
                <p style="color: var(--muted);">{{ $item->content->formatted_price }}</p>
              </div>
              <form method="POST" action="{{ route('cart.destroy', $item) }}">
                @csrf
                @method('DELETE')
                <button class="icon-button" type="submit" aria-label="削除">
                  <span class="material-symbols-outlined" aria-hidden="true">delete</span>
                </button>
              </form>
            </article>
          @empty
            <div class="empty-state">カートは空です。</div>
          @endforelse
        </div>
      </section>

      <aside class="detail-side">
        <section class="panel">
          <h2>合計</h2>
          <div class="spec-list">
            <div><span>合計品数</span><strong>{{ number_format($cart->items->count()) }}</strong></div>
            <div><span>合計金額</span><strong>¥{{ number_format($cart->items->sum(fn($item) => $item->content->price)) }}</strong></div>
          </div>
          <form method="POST" action="{{ route('checkout.start') }}" style="margin-top: 18px;">
            @csrf
            <button class="button button--primary" type="submit">決済へ進む</button>
          </form>
        </section>
      </aside>
    </div>
  </main>

  @include('layouts.footer')
@endsection
