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
          <label class="field">
            <span>通知をON</span>
            <input type="hidden" name="notifications_enabled" value="0">
            <input type="checkbox" name="notifications_enabled" value="1" {{ $user->notifications_enabled ? 'checked' : '' }}>
          </label>
          <label class="field">
            <span>プロフィールに自分のフォロワー数を表示</span>
            <input type="hidden" name="show_follower_count" value="0">
            <input type="checkbox" name="show_follower_count" value="1" {{ $user->show_follower_count ? 'checked' : '' }}>
          </label>
          <label class="field">
            <span>プロフィールに自分のフォロー数を表示</span>
            <input type="hidden" name="show_following_count" value="0">
            <input type="checkbox" name="show_following_count" value="1" {{ $user->show_following_count ? 'checked' : '' }}>
          </label>
        </div>
        <div class="form-actions" style="margin-top: 18px;">
          <button class="button button--primary" type="submit">保存</button>
          <button class="button button--danger" type="button">アカウントを削除</button>
        </div>
      </form>
    </section>
  </main>

  @include('layouts.footer')
@endsection
