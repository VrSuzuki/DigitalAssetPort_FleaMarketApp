<?php

namespace Database\Seeders;

use App\Models\AppNotification;
use App\Models\Comment;
use App\Models\Content;
use App\Models\Favorite;
use App\Models\Follow;
use App\Models\Genre;
use App\Models\LibraryItem;
use App\Models\Order;
use App\Models\SubGenre;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $genres = $this->genres();
        $genreModels = collect();

        foreach ($genres as $genreData) {
            $genre = Genre::firstOrCreate(
                ['slug' => $genreData['slug']],
                ['name' => $genreData['name'], 'description' => $genreData['description']]
            );

            foreach ($genreData['sub_genres'] as $subIndex => $subGenreName) {
                SubGenre::firstOrCreate([
                    'slug' => $genreData['slug'].'-sub-'.($subIndex + 1),
                ], [
                    'genre_id' => $genre->id,
                    'name' => $subGenreName,
                ]);
            }

            $genreModels->push($genre->fresh('subGenres'));
        }

        $users = collect([
            ['handle' => 'port_admin', 'nickname' => 'ポート管理人', 'email' => 'admin@example.com', 'bio' => 'DigitalAssetPortのサンプル管理者です。'],
            ['handle' => 'office_lab', 'nickname' => 'Office Lab', 'email' => 'office@example.com', 'bio' => '業務テンプレートと店舗資料を作っています。'],
            ['handle' => 'code_deck', 'nickname' => 'Code Deck', 'email' => 'code@example.com', 'bio' => 'LaravelとDockerの教材セットを投稿しています。'],
            ['handle' => 'study_port', 'nickname' => 'Study Port', 'email' => 'study@example.com', 'bio' => '大学レポートや演習フォーマットを配布しています。'],
            ['handle' => 'creative_bits', 'nickname' => 'Creative Bits', 'email' => 'creative@example.com', 'bio' => '動画、画像、3D素材の小さなパックを作っています。'],
        ])->map(function ($data) {
            $attributes = User::factory()->make(array_merge($data, [
                'name' => $data['handle'],
                'email_verified_at' => now(),
            ]))->getAttributes();

            return User::firstOrCreate(['email' => $data['email']], $attributes);
        });

        $contents = collect($this->contents())->map(function ($data, $index) use ($genreModels, $users) {
            $genre = $genreModels->firstWhere('slug', $data['genre']);
            $subGenre = $genre->subGenres->firstWhere('name', $data['sub']);
            $author = $users[$index % $users->count()];

            $attributes = Content::factory()->make([
                'user_id' => $author->id,
                'genre_id' => $genre->id,
                'sub_genre_id' => $subGenre->id,
                'title' => $data['title'],
                'slug' => 'asset-'.($index + 1),
                'format' => $data['format'],
                'description' => $data['description'],
                'price' => $data['price'],
                'thumbnail_path' => $data['thumbnail'],
                'environment' => $data['environment'],
                'file_size_mb' => $data['size'],
                'profile_order' => $index + 1,
            ])->getAttributes();

            $content = Content::updateOrCreate(['slug' => 'asset-'.($index + 1)], $attributes);

            $tagIds = collect($data['tags'])->map(function ($tagName) {
                return Tag::firstOrCreate(
                    ['name' => $tagName],
                    ['slug' => Str::slug($tagName) ?: Str::random(8)]
                )->id;
            });

            $content->tags()->sync($tagIds);

            return $content;
        });

        foreach ($users as $user) {
            foreach ($contents->where('user_id', '!=', $user->id)->random(4) as $content) {
                Favorite::firstOrCreate(['user_id' => $user->id, 'content_id' => $content->id]);
            }

            foreach ($users->where('id', '!=', $user->id)->random(2) as $followed) {
                Follow::firstOrCreate(['follower_id' => $user->id, 'following_id' => $followed->id]);
            }
        }

        foreach ($contents->take(10) as $content) {
            $commenter = $users->where('id', '!=', $content->user_id)->random();
            Comment::create([
                'user_id' => $commenter->id,
                'content_id' => $content->id,
                'message' => '実務で使いやすそうな構成です。サンプルデータも参考になりました。',
            ]);
            AppNotification::create([
                'user_id' => $content->user_id,
                'actor_id' => $commenter->id,
                'type' => 'comment',
                'message' => $commenter->display_name.'さんが「'.$content->title.'」にコメントしました。',
                'url' => route('contents.show', $content),
            ]);
        }

        $buyer = $users->first();
        $order = Order::firstOrCreate([
            'order_number' => 'DAP-DEMO-0001',
        ], [
            'user_id' => $buyer->id,
            'total_amount' => $contents->take(2)->sum('price'),
            'status' => 'paid',
            'purchased_at' => now()->subDays(3),
        ]);

        if (!$order->items()->exists()) {
            foreach ($contents->take(2) as $content) {
                $orderItem = $order->items()->create([
                    'content_id' => $content->id,
                    'price' => $content->price,
                ]);
                LibraryItem::firstOrCreate([
                    'user_id' => $buyer->id,
                    'content_id' => $content->id,
                ], [
                    'order_item_id' => $orderItem->id,
                    'added_type' => 'purchase',
                ]);
            }
        }
    }

    private function genres()
    {
        return [
            [
                'name' => 'ビジネス・オフィス',
                'slug' => 'business-office',
                'description' => '資料、帳票、Notion、Excelなど、日々の業務を軽くするデータ。',
                'sub_genres' => ['業務テンプレート', '企画書・提案書', '経理・分析', '大学レポート形式'],
            ],
            [
                'name' => '製造・工業系',
                'slug' => 'manufacturing',
                'description' => '工程管理、安全教育、品質管理、設備保全に使える現場向けデータ。',
                'sub_genres' => ['工程管理', '安全教育', '品質管理', '設備保全'],
            ],
            [
                'name' => '店舗',
                'slug' => 'store',
                'description' => '接客、シフト、販促、マニュアルなど店舗運営を支える素材。',
                'sub_genres' => ['接客マニュアル', 'シフト運用', 'メニュー表', '販促POP'],
            ],
            [
                'name' => 'コード・システム',
                'slug' => 'code-system',
                'description' => 'アプリ雛形、演習セット、APIサンプル、Docker構成などの開発資産。',
                'sub_genres' => ['Laravel', 'JavaScript', 'API', 'Docker'],
            ],
            [
                'name' => '教育・学習',
                'slug' => 'education',
                'description' => 'レポート、演習、動画教材、資格学習など学びを進めるセット。',
                'sub_genres' => ['大学レポート', '語学', 'プログラミング演習', '資格学習'],
            ],
            [
                'name' => 'クリエイティブ',
                'slug' => 'creative',
                'description' => '画像、音声、動画、3Dモデルなど制作のための素材。',
                'sub_genres' => ['画像素材', '動画素材', '3Dモデル', '音声素材'],
            ],
        ];
    }

    private function contents()
    {
        return [
            ['title' => '店舗オペレーション標準化マニュアル', 'genre' => 'store', 'sub' => '接客マニュアル', 'format' => 'external_tool', 'price' => 2400, 'environment' => 'Word / PDF', 'size' => 18.4, 'thumbnail' => 'assets/thumb-store.svg', 'tags' => ['店舗', 'マニュアル', '新人教育'], 'description' => '小規模店舗向けの接客、開店、閉店、クレーム対応をまとめた運営マニュアルです。Wordで編集できるため、自店のルールに合わせて差し替えできます。'],
            ['title' => 'Laravel Docker演習スターター', 'genre' => 'code-system', 'sub' => 'Laravel', 'format' => 'system', 'price' => 3980, 'environment' => 'Laravel 8 / Docker', 'size' => 92.1, 'thumbnail' => 'assets/thumb-code.svg', 'tags' => ['Laravel', 'Docker', '教材'], 'description' => 'ログイン、CRUD、テスト、Docker構築までを段階的に学べる演習セットです。動画教材を想定した章立てと課題ファイルを同梱しています。'],
            ['title' => '大学レポート構成テンプレート集', 'genre' => 'education', 'sub' => '大学レポート', 'format' => 'text', 'price' => 0, 'environment' => 'Word / Google Docs', 'size' => 6.8, 'thumbnail' => 'assets/thumb-study.svg', 'tags' => ['大学', 'レポート', '無料'], 'description' => '序論、本論、結論、参考文献の構成を崩さず書けるテンプレートです。文系、理系、調査レポートの3パターンを用意しています。'],
            ['title' => 'Excel月次売上ダッシュボード', 'genre' => 'business-office', 'sub' => '経理・分析', 'format' => 'external_tool', 'price' => 1500, 'environment' => 'Excel 2021以降', 'size' => 12.5, 'thumbnail' => 'assets/thumb-business.svg', 'tags' => ['Excel', '売上', '分析'], 'description' => '入力表から月別、日別、商品別の売上を自動集計するExcelテンプレートです。小規模ECや店舗のポートフォリオ説明にも使えます。'],
            ['title' => '3D小物モデル ミニパック', 'genre' => 'creative', 'sub' => '3Dモデル', 'format' => 'model_3d', 'price' => 980, 'environment' => 'Blender / Unity', 'size' => 140.2, 'thumbnail' => 'assets/thumb-creative.svg', 'tags' => ['3D', 'Blender', '素材'], 'description' => 'アプリやゲームのモックアップに置ける軽量3D小物モデル集です。低ポリゴンで扱いやすく、Unityへそのまま読み込めます。'],
            ['title' => '製造ライン安全教育スライド', 'genre' => 'manufacturing', 'sub' => '安全教育', 'format' => 'text', 'price' => 1800, 'environment' => 'PowerPoint / PDF', 'size' => 34.7, 'thumbnail' => 'assets/thumb-manufacturing.svg', 'tags' => ['安全教育', '製造', '研修'], 'description' => '新人向けに危険予知、保護具、ヒヤリハット共有を説明するスライド資料です。各ページに講師メモを付けています。'],
            ['title' => 'Notion案件管理ボード', 'genre' => 'business-office', 'sub' => '業務テンプレート', 'format' => 'external_tool', 'price' => 1200, 'environment' => 'Notion', 'size' => 4.3, 'thumbnail' => 'assets/thumb-business.svg', 'tags' => ['Notion', '案件管理', 'タスク'], 'description' => '案件、タスク、請求、議事録をひとつのワークスペースで見られるNotionテンプレートです。小規模チーム向けです。'],
            ['title' => 'JavaScript UI演習カード', 'genre' => 'code-system', 'sub' => 'JavaScript', 'format' => 'system', 'price' => 2000, 'environment' => 'ブラウザ / Node.js', 'size' => 28.0, 'thumbnail' => 'assets/thumb-code.svg', 'tags' => ['JavaScript', 'UI', '演習'], 'description' => 'タブ、モーダル、検索、フォーム検証などのUI演習を小さなカード形式でまとめた教材セットです。'],
            ['title' => '販促POPデザインテンプレート', 'genre' => 'store', 'sub' => '販促POP', 'format' => 'image', 'price' => 900, 'environment' => 'Canva / PNG', 'size' => 22.9, 'thumbnail' => 'assets/thumb-store.svg', 'tags' => ['POP', '店舗', 'デザイン'], 'description' => '飲食、小売、イベント向けの販促POPテンプレートです。差し替えるだけで店頭掲示に使える構成にしています。'],
            ['title' => '資格学習 進捗管理シート', 'genre' => 'education', 'sub' => '資格学習', 'format' => 'external_tool', 'price' => 500, 'environment' => 'Excel / Google Sheets', 'size' => 3.5, 'thumbnail' => 'assets/thumb-study.svg', 'tags' => ['資格', '学習管理', 'Excel'], 'description' => '学習範囲、復習日、模試結果を管理できる進捗シートです。短期集中と長期学習の両方に対応しています。'],
            ['title' => '工程チェックリスト電子化セット', 'genre' => 'manufacturing', 'sub' => '工程管理', 'format' => 'external_tool', 'price' => 3200, 'environment' => 'Excel / PDF', 'size' => 16.9, 'thumbnail' => 'assets/thumb-manufacturing.svg', 'tags' => ['工程管理', 'チェックリスト', '品質'], 'description' => '紙のチェックリストをExcelで運用するための雛形です。工程別の確認項目、承認欄、集計用シートを用意しています。'],
            ['title' => '動画教材用チャプター台本', 'genre' => 'creative', 'sub' => '動画素材', 'format' => 'video', 'price' => 1600, 'environment' => 'Word / Premiere Pro', 'size' => 8.2, 'thumbnail' => 'assets/thumb-creative.svg', 'tags' => ['動画教材', '台本', '編集'], 'description' => 'Udemy風の動画教材を作るための章立て、台本、撮影チェックリストをまとめたパックです。'],
        ];
    }
}
