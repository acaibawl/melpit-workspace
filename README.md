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
