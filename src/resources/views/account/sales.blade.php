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

      <div class="feature-grid">
        <section>
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
        <section>
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
        <section>
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
      </div>
    </section>
  </main>

  @include('layouts.footer')
@endsection
