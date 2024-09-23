<?php
// セッション開始
session_start();

// CORS対応：'localhost:3000'を指定
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// OPTIONSリクエストへの対応（Preflightリクエスト）
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // HTTPステータス200（OK）で応答
    http_response_code(200);
    exit();
}

// POSTメソッドのみで処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // セッションの破棄
    session_unset();
    session_destroy();

    // ログアウト成功のレスポンス
    echo json_encode(['message' => 'ログアウトしました']);
    exit();
} else {
    // その他のリクエスト方法は無効
    http_response_code(405);
    echo json_encode(['error' => '無効なリクエスト']);
}
