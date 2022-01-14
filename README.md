# Dockerの準備

## リポジトリを落とす
```git clone -b melpit https://github.com/t-kuni/laradock-melpit.git laradock```

## 環境ファイルをコピー
```cp melpit/env-example melpit/.env```

## DBがDockerで動くように非同期IO無効化設定
docker-compose.yml を開いて448行目付近に`command: --innodb-use-native-aio=0`を追加する

## アプリケーションの起動
Laradockが提供するコンテナの内、5つを指定して起動する
```docker-compose up -d php-fpm nginx mysql phpmyadmin workspace```

# Laravelプロジェクトの準備

## workspaceのコンテナに入る
```docker-compose exec workspace bash```

## プロジェクト作成
```composer create-project --prefer-dist laravel/laravel="7.25.*" .```

## .envの設定

```
APP_NAME=Melpit
DB_HOST=mysql
DB_DATABASE=melpit
DB_PASSWORD=root
```

## ログファイルへの書き込み権限設定
workspaceコンテナで以下コマンドを実行
```chown -R www-data:www-data /var/www/storage```
```chmod -R 777 /var/www/storage```

## 開発DB接続情報
127.0.0.1  
root  
root  
一旦てきとうなデータベース（スキーマ）に接続してからmelpitスキーマを作成する

## ER図

[dbdiagram.io](https://dbdiagram.io/d/61dfb7a44c9a8944ec92fc72)
```
Table users {
  id int [pk]
}

Table items {
  id int [pk]
  seller_id int [ref: > users.id]
  buyer_id int [ref: > users.id]
  secondary_category_id int [ref: > secondary_categories.id]
  item_condition_id int [ref: > item_conditions.id]
}

Table item_conditions {
  id int [pk]
}

Table primary_categories {
  id int [pk]
}

Table secondary_categories {
  id int [pk]
  primary_category_id int [ref: > primary_categories.id]
}
```

## Eloquent Model 作成コマンド
```
php artisan make:model Models/PrimaryCategory
php artisan make:model Models/SecondaryCategory
php artisan make:model Models/ItemCondition
php artisan make:model Models/Item
# UserモデルもModelsフォルダに移動
mv app/User.php app/Models/
```

## nodeの準備
workspaceコンテナで実行
```npm install```
```npm run dev```

## 決済
開発環境では以下のカードで動作を確認  
[PAY.JP](https://pay.jp/docs/testcard)
