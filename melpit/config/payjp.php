<?php

// env関数を使うのはconfig内のみにすること
// configをキャッシュした場合にnullを返すようになるので、本番環境で動かなくなることがある為
return [
    'public_key' => env('PAYJP_PUBLIC_KEY'),
    'secret_key' => env('PAYJP_SECRET_KEY'),
];
