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

      <form method="POST" action="{{ route('profiles.update') }}" enctype="multipart/form-data" novalidate>
        @csrf
        <div class="form-grid">
          <div class="field">
            <label for="nickname">ニックネーム</label>
            <input class="input" id="nickname" name="nickname" value="{{ old('nickname', $user->nickname ?: $user->name) }}">
          </div>
          <div class="field">
            <span class="field-label">ユーザーID</span>
            <div class="readonly-field">{{ $user->handle }}</div>
            <input type="hidden" name="handle" value="{{ $user->handle }}">
          </div>
          <div class="field field--full">
            <label for="avatar">プロフィールアイコン</label>
            <div class="image-upload" data-image-cropper data-aspect="1" data-max-width="720" data-max-height="720">
              <img class="image-upload__preview avatar-preview" src="{{ $user->avatar_url }}" alt="プロフィールアイコンのプレビュー" data-preview>
              <div>
                <label class="button button--primary image-upload__button" for="avatar">
                  <span class="material-symbols-outlined" aria-hidden="true">add_photo_alternate</span>
                  {{ $user->avatar_path ? '画像を変更' : '画像を追加' }}
                </label>
                <input class="visually-hidden" id="avatar" type="file" name="avatar" accept="image/*" data-file-input>
              </div>
            </div>
          </div>
          <div class="field field--full">
            <label for="bio">自己紹介文</label>
            <textarea class="textarea" id="bio" name="bio" maxlength="1500">{{ old('bio', $user->bio) }}</textarea>
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
