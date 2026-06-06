@extends('layouts.base')

@section('title', '通知 | DigitalAssetPort')

@section('body')
  @include('layouts.header')

  <main class="app-main">
    <div class="section-heading">
      <div>
        <p class="section-eyebrow">Notifications</p>
        <h1 class="section-title">通知</h1>
      </div>
    </div>
    <div class="record-list">
      @forelse($notifications as $notification)
        <a class="record" href="{{ $notification->url ?: '#' }}">
          <img src="{{ optional($notification->actor)->avatar_url ?: auth()->user()->avatar_url }}" alt="">
          <div>
            <h2>{{ $notification->message }}</h2>
            <p style="color: var(--muted);">{{ $notification->created_at->format('Y/m/d H:i') }}</p>
          </div>
          <span class="material-symbols-outlined" aria-hidden="true">chevron_right</span>
        </a>
      @empty
        <div class="empty-state">通知はありません。</div>
      @endforelse
    </div>
    <div class="pagination">{{ $notifications->links() }}</div>
  </main>

  @include('layouts.footer')
@endsection
