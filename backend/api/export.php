<?php
// CORSヘッダー設定
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// エラーログ出力を有効にする
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ini_set('log_errors', 1);
ini_set('error_log', 'http://www.ochiponchi.sakura.ne.jp/CTR_Survey/backend/php_error.log');  // サーバーのエラーログのパスに変更


include '../includes/db.php';  // データベース接続
require '../vendor/autoload.php';  // PhpSpreadsheetの自動読み込み

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// フロントエンドからの検索キーワードと日付範囲を取得
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

// PhpSpreadsheetによるExcelファイルの作成
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// ヘッダー行の設定
$sheet->setCellValue('A1', 'UMIN試験ID');
$sheet->setCellValue('B1', '科学的試験名');
$sheet->setCellValue('C1', '対象疾患名');
$sheet->setCellValue('D1', '一般公開日');

// データの取得とExcelファイルへの書き込み
$rowNumber = 2;
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($data) {
    foreach ($data as $row) {
        $sheet->setCellValue('A' . $rowNumber, $row['umin_id']);
        $sheet->setCellValue('B' . $rowNumber, $row['scientific_title']);
        $sheet->setCellValue('C' . $rowNumber, $row['condition']);
        $sheet->setCellValue('D' . $rowNumber, $row['date_of_disclosure']);
        $rowNumber++;
    }
}

// Excelファイルの出力
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="trials.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
