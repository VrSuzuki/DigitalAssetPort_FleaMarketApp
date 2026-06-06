# DigitalAssetPort

DigitalAssetPort は、Excel / Word / Notion テンプレート、店舗運営マニュアル、学習教材、コード演習セット、動画素材、3Dモデルなどのデジタルデータを販売・配布できる Laravel 製ポートフォリオアプリです。

## 使用技術

- PHP 8.1 / Laravel 8
- Laravel Fortify
- MySQL 8
- Docker / Docker Compose
- MailHog
- Stripe Checkout
- HTML / CSS / JavaScript

## ローカル起動

```bash
cd /home/ubuntuiwa/e/c/DigitalAssetPort
docker compose up -d
docker compose exec php composer install
docker compose exec php php artisan storage:link
docker compose exec php php artisan migrate --seed
```

ブラウザで `http://localhost` を開きます。
MailHog は `http://localhost:8025` で確認できます。

## サンプルアカウント

全アカウントのパスワードは `password` です。

- `admin@example.com`
- `office@example.com`
- `code@example.com`
- `study@example.com`
- `creative@example.com`

## 実装済み画面

- ルートページ
- DigitalAssetPort紹介画面
- 詳細検索画面
- コンテンツ詳細画面
- ログイン画面
- アカウント登録画面
- 認証メール送付済みメッセージ画面
- ユーザー情報登録画面
- ユーザープロフィール画面
- アカウント設定画面
- お気に入り一覧画面
- コンテンツ投稿 / 編集画面
- 売上管理画面
- カート内商品一覧画面
- 購入履歴一覧画面
- 購入履歴詳細画面
- ライブラリ画面
- フォロー一覧画面
- フォロワー一覧画面
- 通知一覧画面

## Stripe

`.env` に `STRIPE_KEY` と `STRIPE_SECRET` を設定すると Stripe Checkout に遷移します。
未設定の場合は、ローカルポートフォリオ用に「ローカル決済完了」として購入履歴とライブラリを作成します。

## ER図

```mermaid
erDiagram
    USERS ||--o{ CONTENTS : posts
    USERS ||--o{ COMMENTS : writes
    USERS ||--o{ FAVORITES : marks
    USERS ||--o{ CARTS : owns
    USERS ||--o{ ORDERS : places
    USERS ||--o{ LIBRARY_ITEMS : stores
    USERS ||--o{ APP_NOTIFICATIONS : receives
    USERS ||--o{ FOLLOWS : follower
    USERS ||--o{ FOLLOWS : following

    GENRES ||--o{ SUB_GENRES : contains
    GENRES ||--o{ CONTENTS : classifies
    SUB_GENRES ||--o{ CONTENTS : narrows

    CONTENTS ||--o{ COMMENTS : has
    CONTENTS ||--o{ FAVORITES : has
    CONTENTS ||--o{ CART_ITEMS : added
    CONTENTS ||--o{ ORDER_ITEMS : sold
    CONTENTS ||--o{ LIBRARY_ITEMS : available
    CONTENTS }o--o{ TAGS : tagged

    CARTS ||--o{ CART_ITEMS : includes
    ORDERS ||--o{ ORDER_ITEMS : includes
    ORDER_ITEMS ||--o| LIBRARY_ITEMS : unlocks

    USERS {
        bigint id PK
        string handle UK
        string name
        string nickname
        string email UK
        text bio
        string avatar_path
        timestamp email_verified_at
    }

    GENRES {
        bigint id PK
        string name
        string slug UK
        text description
    }

    SUB_GENRES {
        bigint id PK
        bigint genre_id FK
        string name
        string slug UK
    }

    CONTENTS {
        bigint id PK
        bigint user_id FK
        bigint genre_id FK
        bigint sub_genre_id FK
        string title
        string slug UK
        string format
        text description
        int price
        string thumbnail_path
        string file_path
        string license_type
        string environment
        decimal file_size_mb
        int rating_rate
        int ratings_count
        string status
        timestamp published_at
    }

    TAGS {
        bigint id PK
        string name UK
        string slug UK
    }

    FAVORITES {
        bigint id PK
        bigint user_id FK
        bigint content_id FK
    }

    FOLLOWS {
        bigint id PK
        bigint follower_id FK
        bigint following_id FK
    }

    COMMENTS {
        bigint id PK
        bigint user_id FK
        bigint content_id FK
        text message
    }

    CARTS {
        bigint id PK
        bigint user_id FK
        boolean active
    }

    CART_ITEMS {
        bigint id PK
        bigint cart_id FK
        bigint content_id FK
    }

    ORDERS {
        bigint id PK
        bigint user_id FK
        string order_number UK
        int total_amount
        string stripe_session_id
        string status
        timestamp purchased_at
    }

    ORDER_ITEMS {
        bigint id PK
        bigint order_id FK
        bigint content_id FK
        int price
    }

    LIBRARY_ITEMS {
        bigint id PK
        bigint user_id FK
        bigint content_id FK
        bigint order_item_id FK
        string added_type
    }

    APP_NOTIFICATIONS {
        bigint id PK
        bigint user_id FK
        bigint actor_id FK
        string type
        string message
        string url
        timestamp read_at
    }
```
