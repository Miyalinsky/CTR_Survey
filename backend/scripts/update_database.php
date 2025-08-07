<?php
// backend/scripts/update_database.php

// 実行ログを確認するためのファイルパス
$logFile = '/home/ochiponchi/www/CTR_Survey/backend/scripts/fetch_log.txt';

// ログに実行時間を記録する
file_put_contents($logFile, "update_database.php executed at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

// fetch_csv.phpのAPIをcurlで呼び出す
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://ochiponchi.sakura.ne.jp/CTR_Survey/backend/scripts/fetch_csv.php");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

// ログにAPIレスポンスを記録する
file_put_contents($logFile, "API response: " . $response . "\n", FILE_APPEND);
