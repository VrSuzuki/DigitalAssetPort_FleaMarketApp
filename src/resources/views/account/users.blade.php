@extends('layouts.base')

@section('title', $title.' | DigitalAssetPort')

@section('body')
  @include('layouts.header')

  <main class="app-main">
    <div class="section-heading">
      <div>
        <p class="section-eyebrow">Users</p>
        <h1 class="section-title">{{ $title }}</h1>
      </div>
      <a class="button button--ghost" href="{{ $switchRoute }}">{{ $switchLabel }}</a>
    </div>
    @if($users->count())
      <div class="content-grid">
        @foreach($users as $user)
          @include('partials.user-card', ['user' => $user])
        @endforeach
      </div>
      <div class="pagination">{{ $users->links() }}</div>
    @else
      <div class="empty-state">ユーザーはいません。</div>
    @endif
  </main>

  @include('layouts.footer')
@endsection
