# DigitalAssetPort

DigitalAssetPort は、Excel / Word / Notion テンプレート、生活ノウハウ、学習教材、コード演習セット、動画素材、3Dモデルなどのデジタルデータを販売・配布できる Laravel 製ポートフォリオアプリです。

## ローカル起動

```bash
docker compose up -d
docker compose exec php composer install
docker compose exec php php artisan storage:link
docker compose exec php php artisan migrate --seed
```

- アプリ: `http://localhost`
- MailHog: `http://localhost:8025`
- サンプルログイン: `admin@example.com` / `password`
- Seeder は 12ユーザー、36コンテンツ、購入履歴、通知、フォロー、お気に入りを作成します。

## 主な機能

- Laravel Fortify による登録、ログイン、メール認証
- デジタルコンテンツの投稿、編集、検索、詳細表示
- お気に入り、コメント、フォロー、通知
- カート、Stripe Checkout、ローカル決済フォールバック
- 購入履歴、ライブラリ、ダウンロード
- 売上管理

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
    CONTENTS ||--o{ CONTENT_IMAGES : shows
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
        string nickname
        string email UK
        text bio
    }
    GENRES {
        bigint id PK
        string name
        string slug UK
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
        int price
        string status
    }
    TAGS {
        bigint id PK
        string name UK
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
        boolean is_recommended
    }
    CONTENT_IMAGES {
        bigint id PK
        bigint content_id FK
        string path
        tinyint sort_order
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
    }
    APP_NOTIFICATIONS {
        bigint id PK
        bigint user_id FK
        bigint actor_id FK
        string type
        string message
    }
```
