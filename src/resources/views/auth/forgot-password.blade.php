@extends('layouts.base')

@section('title', 'パスワード再設定 | DigitalAssetPort')

@section('body')
  @include('layouts.header')

  <main class="auth-main">
    <section class="auth-card">
      <h1>パスワード再設定</h1>
      @include('partials.flash')
      @include('partials.errors')
      <form method="POST" action="{{ route('password.email') }}" novalidate>
        @csrf
        <div class="field">
          <label for="email">メールアドレス</label>
          <input class="input" id="email" type="email" name="email" value="{{ old('email') }}">
        </div>
        <div class="form-actions" style="margin-top: 18px;">
          <button class="button button--primary" type="submit">再設定メールを送る</button>
        </div>
      </form>
    </section>
  </main>
@endsection
