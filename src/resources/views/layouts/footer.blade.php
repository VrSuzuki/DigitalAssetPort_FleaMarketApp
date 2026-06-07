@php
  $homeUrl = Route::has('home') ? route('home') : url('/');
@endphp

<footer class="site-footer">
  <div class="site-footer__inner">
    <div class="site-footer__grid">
      <section>
        <a class="brand brand--footer" href="{{ $homeUrl }}">
          <img class="brand__logo" src="{{ asset('assets/dap-logo.svg') }}" alt="">
          <span class="brand__name">DigitalAssetPort</span>
        </a>
        <p class="site-footer__lead">
          DigitalAssetPortはあらゆるデジタルデータが共有・販売されるプラットフォームです。
        </p>
        <div class="footer-actions">
          <a class="button button--ghost" href="{{ route('about') }}">DigitalAssetPortとは</a>
          <a class="icon-button" href="https://x.com/iwa_vr" target="_blank" rel="noopener" aria-label="X">
            <span class="material-symbols-outlined" aria-hidden="true">alternate_email</span>
          </a>
        </div>
      </section>

      <section>
        <h2 class="site-footer__heading">ご利用について</h2>
        <ul class="site-footer__links">
          <li><a href="#">利用規約</a></li>
          <li><a href="#">ガイドライン</a></li>
          <li><a href="#">プライバシーポリシー</a></li>
          <li><a href="#">お問い合わせ</a></li>
        </ul>
      </section>

      <section>
        <h2 class="site-footer__heading">探す</h2>
        <ul class="site-footer__links">
          <li><a href="{{ route('home') }}">トップページ</a></li>
          <li><a href="{{ route('search.advanced') }}">詳細検索</a></li>
          <li><a href="{{ route('about') }}">このサイトについて</a></li>
        </ul>
      </section>

      <section>
        <h2 class="site-footer__heading">アカウント</h2>
        <ul class="site-footer__links">
          @guest
            <li><a href="{{ route('login') }}">ログイン</a></li>
            <li><a href="{{ route('register') }}">会員登録</a></li>
          @else
            <li><a href="{{ route('contents.create') }}">アップロード</a></li>
            <li><a href="{{ route('library.index') }}">ライブラリ</a></li>
          @endguest
        </ul>
      </section>
    </div>

    <div class="site-footer__bottom">
      <small>&copy; {{ date('Y') }} DigitalAssetPort</small>
      <a href="{{ $homeUrl }}">トップへ戻る</a>
    </div>
  </div>
</footer>
