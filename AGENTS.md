# 今日のアラカルト — Agent Instructions

飲食店向けデイリーメニュー予約・在庫管理システム。Laravel 12 + Filament 3 + Laravel Sail (Docker)。

## 環境

**必須**: すべてのコマンドは **Laravel Sail** (`./vendor/bin/sail`) 経由で実行する。ホスト上の `php`/`artisan`/`npm` を直接使わない。

| サービス                 | 用途                                            |
| ------------------------ | ----------------------------------------------- |
| `laravel.test` (PHP 8.5) | アプリケーション本体                            |
| `mysql:8.4`              | メインDB (`DB_DATABASE` 環境変数)               |
| `redis:alpine`           | キャッシュ・セッション・キュー                  |
| `mailpit`                | ローカルメール受信 (SMTP :1025, WebUI :8025)    |
| `minio`                  | S3互換ストレージ (商品画像, :9000, WebUI :8900) |

## コマンド早見表

```bash
make up          # sail up -d（バックグラウンド起動）
make do          # sail down（停止）
make dev         # sail npm run dev（Vite HMR）
make migrate     # sail artisan migrate
make migratelist # sail artisan migrate:status
make seed        # sail artisan db:seed
make clear       # sail artisan optimize:clear

# make にないコマンドは直接実行
./vendor/bin/sail artisan <command>
./vendor/bin/sail npm run build   # 本番ビルド
./vendor/bin/sail composer <command>
```

## テスト実行

```bash
./vendor/bin/sail artisan test                        # 全テスト
./vendor/bin/sail artisan test --testsuite=Feature    # Feature のみ
./vendor/bin/sail artisan test --testsuite=Unit       # Unit のみ
```

テスト環境 (`phpunit.xml`): SQLite in-memory、`DB_DATABASE=testing`、`BCRYPT_ROUNDS=4`、`SESSION_DRIVER=array`

## アーキテクチャ

```
/admin      → Filament v3 管理パネル（is_admin=true のユーザーのみ）
/alacarte   → 顧客向けメニュー一覧（認証必須）
/reserve/…  → ゲスト予約フロー
/mypage     → 会員マイページ
```

### 主要コンポーネント

| パス                                             | 役割                                                              |
| ------------------------------------------------ | ----------------------------------------------------------------- |
| `app/Filament/Resources/`                        | Product / Inventory / Reservation / BusinessHour の4リソース      |
| `app/Filament/Pages/ReservationOverview.php`     | カスタム予約概覧ページ                                            |
| `app/Livewire/ProductInventoryStatus.php`        | 在庫状況リアルタイム表示（`InventoryUpdated` イベントをリッスン） |
| `app/Livewire/MemberReservationModal.php`        | 会員向け予約モーダル（JSON API 経由）                             |
| `app/Models/Inventory.php`                       | 更新時に `InventoryUpdated` イベントを自動発火                    |
| `app/Http/Controllers/ReservationController.php` | ゲスト予約 (`store`) と会員予約 (`memberStore`)                   |

### モデルとリレーション

| モデル         | 主要リレーション                                          | 主要カラム                                               |
| -------------- | --------------------------------------------------------- | -------------------------------------------------------- |
| `User`         | `hasMany(Reservation)`                                    | `is_admin`(bool), `role`(enum), `nickname`, `phone`      |
| `Product`      | `hasOne(Inventory)`                                       | `is_active`, `sale_date`(nullable)                       |
| `Inventory`    | `belongsTo(Product)`                                      | `quantity`, `status`(in_stock/limited/sold_out)          |
| `Reservation`  | `belongsTo(User)` nullable, `belongsTo(Product)` nullable | `user_id`, `customer_name`, `reservation_date`, `status` |
| `BusinessHour` | なし                                                      | `day_of_week`, `open_time`, `close_time`, `is_closed`    |

## 予約フロー（2パターン）

**ゲスト予約**: `GET/POST /reserve/{product}` → `ReservationController@store` → `StoreReservationRequest` でバリデーション

**会員予約**: `POST /member/reserve/{product}` → `ReservationController@memberStore` → Livewire モーダル経由（JSON レスポンス）

**共通**: 必ず `DB::transaction() + lockForUpdate()` で在庫競合を防止すること。

### 在庫状態の自動遷移ルール

```
quantity === 0  → status = 'sold_out'
quantity <= 3   → status = 'limited'
quantity > 3    → status = 'in_stock'
```

`Inventory` 更新後に `InventoryUpdated` イベントが発火 → `ProductInventoryStatus` Livewire が `#[On('inventory-updated')]` でリッスンして UI 更新。

### 商品表示フィルタ（/alacarte）

本日分: `whereNull('sale_date')->orWhere('sale_date', today())`  
将来分: 別途 `orderBy('sale_date')` で表示

## 規約・落とし穴

### 管理者認証

- Filament アクセスは `User::canAccessPanel()` で `is_admin = true` をチェック
- **`is_admin`（bool）と `role`（UserRole enum）は別フィールド**。Filament 認可は `is_admin`、アプリロジックは `role` を使用
- 管理者ユーザーは `AdminSetupSeeder` が `.env` の `ADMIN_*` 変数から作成（`config/admin.php` 参照）
- `updateOrCreate` でシードするため既存アカウントが上書きされる

### フロントエンド CSS / JS

- `tailwind.config.js` の `content` に `resources/views/filament/` を **含めない**（フロント CSS が Filament 管理画面に誤適用される）
- `resources/js/app.js` の Alpine.js 起動と Filament の Alpine.js が競合するリスクあり
- Filament のスタイルは `@vite` ではなく Filament 独自の asset injection で読み込まれる

### ストレージ

- 商品画像は MinIO（S3互換）に保存。`./vendor/bin/sail artisan storage:link` が必要
- 画像 URL の取得は `$product->image_url` アクセサを使用（手動で URL を組み立てない）

### テスト環境

- `sessions` テーブルは `0001_01_01_000000_create_users_table.php` で作成される（未作成エラーはマイグレーション未実行）
- テストで管理者が必要な場合: `User::factory()->create(['is_admin' => true])`
- テストで在庫操作の場合: `RefreshDatabase` トレイトを使用し、毎回クリーンな状態で開始

### よくある間違い

| 間違い                                        | 正しい対応                                        |
| --------------------------------------------- | ------------------------------------------------- |
| 在庫操作に transaction なし                   | 必ず `DB::transaction() + lockForUpdate()`        |
| Sail を使わずホスト php を実行                | `./vendor/bin/sail artisan` か `make` コマンド    |
| `$product->image` で画像 URL を組み立て       | `$product->image_url` アクセサを使用              |
| `role` で Filament 認可を判定                 | `is_admin` boolean で判定                         |
| `sale_date` の null チェック漏れ              | `whereNull('sale_date')->orWhere(...)`            |
| Livewire で `InventoryUpdated` のリッスン漏れ | `#[On('inventory-updated')]` アトリビュートを付与 |
| `config('app.admin.*')` でシーダー設定を取得  | `config('admin.*')` を使用                        |
