@extends('layouts.base')

@section('title', '売上管理 | DigitalAssetPort')

@section('body')
  @include('layouts.header')

  <main class="app-main">
    <section class="panel">
      <div class="section-heading">
        <div>
          <p class="section-eyebrow">Revenue</p>
          <h1 class="section-title">売上管理</h1>
        </div>
        <strong class="price">総売上 ¥{{ number_format($total) }}</strong>
      </div>

      <div class="segmented-tabs" role="tablist" aria-label="売上表示切り替え">
        <a class="{{ $tab === 'monthly' ? 'is-active' : '' }}" href="{{ route('sales.index', ['tab' => 'monthly']) }}">月別売上</a>
        <a class="{{ $tab === 'daily' ? 'is-active' : '' }}" href="{{ route('sales.index', ['tab' => 'daily']) }}">日別売上</a>
        <a class="{{ $tab === 'products' ? 'is-active' : '' }}" href="{{ route('sales.index', ['tab' => 'products']) }}">コンテンツ別売上</a>
        <a class="{{ $tab === 'orders' ? 'is-active' : '' }}" href="{{ route('sales.index', ['tab' => 'orders']) }}">注文一覧</a>
      </div>

      @if($tab === 'monthly')
        <section class="sales-section">
          <h2 class="card-heading">月別売上</h2>
          <div class="table-wrap">
            <table class="data-table">
              <thead><tr><th>年月</th><th>注文金額</th></tr></thead>
              <tbody>
                @forelse($monthly as $row)
                  <tr><td>{{ $row->label }}</td><td>¥{{ number_format($row->amount) }}</td></tr>
                @empty
                  <tr><td colspan="2">売上はまだありません。</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </section>
      @elseif($tab === 'daily')
        <section class="sales-section">
          <h2 class="card-heading">日別売上</h2>
          <div class="table-wrap">
            <table class="data-table">
              <thead><tr><th>日付</th><th>注文金額</th></tr></thead>
              <tbody>
                @forelse($daily as $row)
                  <tr><td>{{ $row->label }}</td><td>¥{{ number_format($row->amount) }}</td></tr>
                @empty
                  <tr><td colspan="2">売上はまだありません。</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </section>
      @elseif($tab === 'products')
        <section class="sales-section">
          <h2 class="card-heading">コンテンツ別売上</h2>
          <div class="table-wrap">
            <table class="data-table">
              <thead><tr><th>商品名</th><th>数量</th><th>小計</th></tr></thead>
              <tbody>
                @forelse($products as $row)
                  <tr><td>{{ $row->label }}</td><td>{{ $row->quantity }}</td><td>¥{{ number_format($row->amount) }}</td></tr>
                @empty
                  <tr><td colspan="3">売上はまだありません。</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </section>
      @else
        <section class="sales-section">
          <h2 class="card-heading">注文一覧</h2>
          <div class="record-list">
            @forelse($orders as $order)
              @php
                $sellerItems = $order->items->filter(function ($item) {
                    return optional($item->content)->user_id === auth()->id();
                });
              @endphp
              <article class="record record--order">
                <div>
                  <strong>{{ $order->order_number }}</strong>
                  <p style="color: var(--muted);">ユーザーID: {{ $order->user->handle }} / {{ optional($order->purchased_at)->format('Y/m/d H:i') }}</p>
                </div>
                <div class="order-items-mini">
                  @foreach($sellerItems as $item)
                    <span>
                      <img src="{{ $item->content->thumbnail_url }}" alt="">
                      {{ $item->content->title }}
                    </span>
                  @endforeach
                </div>
                <div>
                  <span>{{ $sellerItems->count() }}点</span>
                  <strong class="price">¥{{ number_format($sellerItems->sum('price')) }}</strong>
                </div>
              </article>
            @empty
              <div class="empty-state">注文はまだありません。</div>
            @endforelse
          </div>
          <div class="pagination">{{ $orders->links() }}</div>
        </section>
      @endif
    </section>
  </main>

  @include('layouts.footer')
@endsection
