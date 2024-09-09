<?php
// エラー表示を有効にする（デバッグ用）
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// CORSヘッダー設定
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// レスポンスがJSON形式で返されるように設定
header('Content-Type: application/json');

// データベース接続ファイルをインクルード
include '../includes/db.php';

// フロントエンドから検索キーワードと日付範囲を取得
$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
$startDate = isset($_GET['startDate']) ? $_GET['startDate'] : '1970-01-01';
$endDate = isset($_GET['endDate']) ? $_GET['endDate'] : date('Y-m-d');

// SQLクエリを実行するための準備
$sql = "SELECT * FROM trials WHERE scientific_title LIKE :keyword AND date_of_disclosure BETWEEN :startDate AND :endDate";

// クエリの準備と実行
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':keyword' => "%$keyword%",
    ':startDate' => $startDate,
    ':endDate' => $endDate
]);

// データを取得してJSON形式で返す
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($data) > 0) {
    // 正常なデータを返す
    echo json_encode($data);
} else {
    // データがない場合のメッセージ
    echo json_encode(["message" => "該当するデータがありません"]);
}
