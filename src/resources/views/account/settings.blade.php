@extends('layouts.base')

@section('title', 'アカウント設定 | DigitalAssetPort')

@section('body')
  @include('layouts.header')

  <main class="app-main">
    <section class="panel">
      <h1 class="section-title">アカウント設定</h1>
      @include('partials.flash')
      <form method="POST" action="{{ route('settings.update') }}" style="margin-top: 18px;">
        @csrf
        <div class="form-grid form-grid--single">
          <label class="toggle-row">
            <span>通知をON</span>
            <input type="hidden" name="notifications_enabled" value="0">
            <input class="toggle-input" type="checkbox" name="notifications_enabled" value="1" {{ $user->notifications_enabled ? 'checked' : '' }}>
            <span class="toggle-visual" aria-hidden="true"></span>
          </label>
          <label class="toggle-row">
            <span>プロフィールに自分のフォロワー数を表示</span>
            <input type="hidden" name="show_follower_count" value="0">
            <input class="toggle-input" type="checkbox" name="show_follower_count" value="1" {{ $user->show_follower_count ? 'checked' : '' }}>
            <span class="toggle-visual" aria-hidden="true"></span>
          </label>
          <label class="toggle-row">
            <span>プロフィールに自分のフォロー数を表示</span>
            <input type="hidden" name="show_following_count" value="0">
            <input class="toggle-input" type="checkbox" name="show_following_count" value="1" {{ $user->show_following_count ? 'checked' : '' }}>
            <span class="toggle-visual" aria-hidden="true"></span>
          </label>
        </div>
        <div class="settings-actions">
          <button class="button button--primary" type="submit">保存</button>
          <button class="button button--danger" type="button" data-confirm-open="deleteAccountModal">アカウントを削除</button>
        </div>
      </form>
    </section>
  </main>

  <div class="modal-backdrop" id="deleteAccountModal" hidden>
    <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="deleteAccountTitle">
      <h2 id="deleteAccountTitle">本当にアカウントを削除しても良いですか？</h2>
      <p>投稿コンテンツ、コメント、フォロー、お気に入りなど、このアカウントに紐づくデータも削除されます。</p>
      <div class="form-actions" style="margin-top: 18px;">
        <form method="POST" action="{{ route('settings.destroy') }}">
          @csrf
          @method('DELETE')
          <button class="button button--danger" type="submit">削除します</button>
        </form>
        <button class="button button--ghost" type="button" data-confirm-close>戻る</button>
      </div>
    </div>
  </div>

  @include('layouts.footer')
@endsection
