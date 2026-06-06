@extends('layouts.base')

@section('title', 'ユーザー情報登録 | DigitalAssetPort')

@section('body')
  @include('layouts.header')

  <main class="app-main">
    <section class="panel">
      <div class="section-heading">
        <div>
          <p class="section-eyebrow">Profile</p>
          <h1 class="section-title">ユーザー情報登録画面</h1>
        </div>
      </div>
      @include('partials.flash')
      @include('partials.errors')

      <form method="POST" action="{{ route('profiles.update') }}" enctype="multipart/form-data">
        @csrf
        <div class="form-grid">
          <div class="field">
            <label for="nickname">ニックネーム</label>
            <input class="input" id="nickname" name="nickname" value="{{ old('nickname', $user->nickname ?: $user->name) }}" required>
          </div>
          <div class="field">
            <label for="handle">ユーザーID</label>
            <input class="input" id="handle" name="handle" value="{{ old('handle', $user->handle) }}" required>
          </div>
          <div class="field field--full">
            <label for="avatar">プロフィールアイコン</label>
            <input class="input" id="avatar" type="file" name="avatar" accept="image/*">
          </div>
          <div class="field field--full">
            <label for="bio">自己紹介文</label>
            <textarea class="textarea" id="bio" name="bio">{{ old('bio', $user->bio) }}</textarea>
          </div>
        </div>
        <div class="form-actions" style="margin-top: 18px;">
          <button class="button button--primary" type="submit">保存</button>
          <a class="button button--ghost" href="{{ route('profiles.show', $user) }}">戻る</a>
        </div>
      </form>
    </section>
  </main>

  @include('layouts.footer')
@endsection
