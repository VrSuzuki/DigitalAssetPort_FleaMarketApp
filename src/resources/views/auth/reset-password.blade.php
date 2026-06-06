@extends('layouts.base')

@section('title', '新しいパスワード | DigitalAssetPort')

@section('body')
  @include('layouts.header')

  <main class="auth-main">
    <section class="auth-card">
      <h1>新しいパスワード</h1>
      @include('partials.errors')
      <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        <div class="form-grid form-grid--single">
          <div class="field">
            <label for="email">メールアドレス</label>
            <input class="input" id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required>
          </div>
          <div class="field">
            <label for="password">パスワード</label>
            <input class="input" id="password" type="password" name="password" required>
          </div>
          <div class="field">
            <label for="password_confirmation">パスワード確認</label>
            <input class="input" id="password_confirmation" type="password" name="password_confirmation" required>
          </div>
        </div>
        <div class="form-actions" style="margin-top: 18px;">
          <button class="button button--primary" type="submit">更新する</button>
        </div>
      </form>
    </section>
  </main>
@endsection
