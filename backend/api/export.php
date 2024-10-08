<?php
// CORSヘッダー設定
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// エラーログ出力を有効にする
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ログファイルにエラーを記録する
ini_set('log_errors', 1);
ini_set('error_log', '/Applications/XAMPP/xamppfiles/logs/php_error.log');


include '../includes/db.php';  // データベース接続
require '../vendor/autoload.php';  // PhpSpreadsheetの自動読み込み

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// フロントエンドからの検索キーワードと日付範囲を取得
$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
$startDate = isset($_GET['startDate']) ? $_GET['startDate'] : '1970-01-01';
$endDate = isset($_GET['endDate']) ? $_GET['endDate'] : date('Y-m-d');

// SQLクエリを実行するための準備
$sql = "
SELECT umin_id, CONCAT(umin_id, '\n', date_of_disclosure) AS umin_with_date, scientific_title, `condition`, narrative_objectives1, basic_objectives2, basic_objectives_others, developmental_phase, primary_outcomes, key_secondary_outcomes, study_type,
CONCAT(basic_design, '\n', randomization, '\n', randomization_unit, '\n', blinding, '\n', control) AS study_design, age_lower_limit, age_upper_limit, gender, key_inclusion_criteria, key_exclusion_criteria, target_sample_size,
CONCAT(institute, '\n', institute_org) AS institute, CONCAT(organization, '\n', organization_org) AS organization, irb_organization, institutions,
url_japanese
FROM trials 
WHERE (scientific_title LIKE :keyword
    OR public_title LIKE :keyword
    OR `condition` LIKE :keyword 
    OR narrative_objectives1 LIKE :keyword)
AND date_of_disclosure BETWEEN :startDate AND :endDate
";

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
$sheet->setCellValue('B1', '試験名');
$sheet->setCellValue('C1', '対象疾患名/Condition');
$sheet->setCellValue('D1', '目的1/Narrative objectives1');
$sheet->setCellValue('E1', '目的2/Basic objectives2');
$sheet->setCellValue('F1', '目的2 -その他詳細/Basic objectives -Others');
$sheet->setCellValue('G1', '試験のフェーズ/Developmental phase');
$sheet->setCellValue('H1', '主要アウトカム評価項目/Primary outcomes');
$sheet->setCellValue('I1', '副次アウトカム評価項目/Key secondary outcomes');
$sheet->setCellValue('J1', '試験の種類/Study type');
$sheet->setCellValue('K1', '試験デザイン/Study design');
$sheet->setCellValue('L1', '年齢（下限）/Age-lower limit');
$sheet->setCellValue('M1', '年齢（上限）/Age-upper limit');
$sheet->setCellValue('N1', '性別/Gender');
$sheet->setCellValue('O1', '選択基準/Key inclusion criteria');
$sheet->setCellValue('P1', '除外基準/Key exclusion criteria');
$sheet->setCellValue('Q1', '目標参加者数/Target sample size');
$sheet->setCellValue('R1', '責任研究者/Name of lead principal investigator');
$sheet->setCellValue('S1', '所属組織/Organization');
$sheet->setCellValue('T1', '所属部署/Division name');
$sheet->setCellValue('U1', '郵便番号/Zip code');
$sheet->setCellValue('V1', '住所/Address');
$sheet->setCellValue('W1', '電話/TEL');
$sheet->setCellValue('X1', 'Email/Email');
$sheet->setCellValue('Y1', '実施責任組織/Sponsor');
$sheet->setCellValue('Z1', '研究費提供組織/Funding Source');
$sheet->setCellValue('AA1', 'IRB等連絡先（公開）/IRB Contact (For public release)');
$sheet->setCellValue('AB1', '試験実施施設/Institutions');

// データの取得とExcelファイルへの書き込み
$rowNumber = 2;
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

//error_log(var_export($data, true));

// 研究責任者情報をスクレイピングして追加する
foreach ($data as &$record) {
    if (!empty($record['url_japanese'])) {
        $url = $record['url_japanese'];

        // url_japaneseから研究責任者情報を取得
        $html = file_get_contents($url);
        //error_log($html);

        if ($html !== false) {
            // エンコーディングがUTF-8以外の場合は変換
            $encoding = mb_detect_encoding($html, ['UTF-8', 'ISO-8859-1', 'SJIS'], true);
            if ($encoding !== 'UTF-8') {
                $html = mb_convert_encoding($html, 'UTF-8', $encoding);
            }

            // DOMDocumentでパースする前に、metaタグを追加
            $html = preg_replace('/<head>/', '<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">', $html);

            // DOMDocumentでHTMLをパース
            $doc = new DOMDocument();
            libxml_use_internal_errors(true); // パースエラーを無視
            $doc->loadHTML($html);
            libxml_clear_errors();

            // XPathで名と姓を取得
            $xpath = new DOMXPath($doc);

            // // 取得したHTML全体をログに出力
            // error_log("取得したHTML: " . $doc->saveHTML());

            $firstNameNode = $xpath->query("//tr[td/b/font[contains(text(), '名')]]/td[2]");
            $lastNameNode = $xpath->query("//tr[td/b/font[contains(text(), '姓')]]/td[2]");

            // 名と姓を連結してフルネームを作成
            $firstName = ($firstNameNode->length > 0) ? trim($firstNameNode->item(0)->nodeValue) : '';
            $lastName = ($lastNameNode->length > 0) ? trim($lastNameNode->item(0)->nodeValue) : '';
            $fullName = $firstName . ' ' . $lastName;

            // 所属組織
            $organizationNode = $xpath->query("//div[h3[contains(text(), '所属組織')]]/tr/p[1]/text()[normalize-space() != '日本語']");
            $organization = ($organizationNode->length > 0) ? trim($organizationNode->item(0)->nodeValue) : '';

            // 所属部署
            $divisionNode = $xpath->query("//div[h3[contains(text(), '所属部署')]]/tr/p[1]/text()[normalize-space() != '日本語']");
            $division = ($divisionNode->length > 0) ? trim($divisionNode->item(0)->nodeValue) : '';

            // 郵便番号
            $zipCodeNode = $xpath->query("//div[h3[contains(text(), '郵便番号')]]/tr/p");
            $zipCode = ($zipCodeNode->length > 0) ? trim($zipCodeNode->item(0)->nodeValue) : '';

            // 住所
            $addressNode = $xpath->query("//div[h3[contains(text(), '住所')]]/tr/p[1]/text()[normalize-space() != '日本語']");
            $address = ($addressNode->length > 0) ? trim($addressNode->item(0)->nodeValue) : '';

            // 電話番号
            $telNode = $xpath->query("//div[h3[contains(text(), '電話')]]/tr/p");
            $tel = ($telNode->length > 0) ? trim($telNode->item(0)->nodeValue) : '';

            // Email
            $emailNode = $xpath->query("//div[h3[contains(text(), 'Email')]]/tr/p");
            $email = ($emailNode->length > 0) ? trim($emailNode->item(0)->nodeValue) : '';

            // レコードに追加
            $record['responsible_person'] = !empty($fullName) ? $fullName : "取得できませんでした";
            $record['responsible_organization'] = !empty($organization) ? $organization : "取得できませんでした";
            $record['responsible_division'] = !empty($division) ? $division : "取得できませんでした";
            $record['responsible_zipCode'] = !empty($zipCode) ? $zipCode : "取得できませんでした";
            $record['responsible_address'] = !empty($address) ? $address : "取得できませんでした";
            $record['responsible_tel'] = !empty($tel) ? $tel : "取得できませんでした";
            $record['responsible_email'] = !empty($email) ? $email : "取得できませんでした";
        } else {
            error_log("URLにアクセスできませんでした: " . $url);
            $record['responsible_person'] = "URLにアクセスできませんでした";
        }
    }
}

if ($data) {
    foreach ($data as $row) {
        $sheet->setCellValue('A' . $rowNumber, $row['umin_with_date']);
        $sheet->setCellValue('B' . $rowNumber, $row['scientific_title']);
        $sheet->setCellValue('C' . $rowNumber, $row['condition']);
        $sheet->setCellValue('D' . $rowNumber, $row['narrative_objectives1']);
        $sheet->setCellValue('E' . $rowNumber, $row['basic_objectives2']);
        $sheet->setCellValue('F' . $rowNumber, $row['basic_objectives_others']);
        $sheet->setCellValue('G' . $rowNumber, $row['developmental_phase']);
        $sheet->setCellValue('H' . $rowNumber, $row['primary_outcomes']);
        $sheet->setCellValue('I' . $rowNumber, $row['key_secondary_outcomes']);
        $sheet->setCellValue('J' . $rowNumber, $row['study_type']);
        $sheet->setCellValue('K' . $rowNumber, $row['study_design']);
        $sheet->setCellValue('L' . $rowNumber, $row['age_lower_limit']);
        $sheet->setCellValue('M' . $rowNumber, $row['age_upper_limit']);
        $sheet->setCellValue('N' . $rowNumber, $row['gender']);
        $sheet->setCellValue('O' . $rowNumber, $row['key_inclusion_criteria']);
        $sheet->setCellValue('P' . $rowNumber, $row['key_exclusion_criteria']);
        $sheet->setCellValue('Q' . $rowNumber, $row['target_sample_size']);
        $sheet->setCellValue('R' . $rowNumber, $row['responsible_person']);
        $sheet->setCellValue('S' . $rowNumber, $row['responsible_organization']);
        $sheet->setCellValue('T' . $rowNumber, $row['responsible_division']);
        $sheet->setCellValue('U' . $rowNumber, $row['responsible_zipCode']);
        $sheet->setCellValue('V' . $rowNumber, $row['responsible_address']);
        $sheet->setCellValue('W' . $rowNumber, $row['responsible_tel']);
        $sheet->setCellValue('X' . $rowNumber, $row['responsible_email']);
        $sheet->setCellValue('Y' . $rowNumber, $row['institute']);
        $sheet->setCellValue('Z' . $rowNumber, $row['organization']);
        $sheet->setCellValue('AA' . $rowNumber, $row['irb_organization']);
        $sheet->setCellValue('AB' . $rowNumber, $row['institutions']);
        $rowNumber++;
    }
}

// ヘッダーのスタイルを設定
$headerStyle = [
    'font' => [
        'bold' => true,
        'color' => ['argb' => 'FFFFFFFF'],  // 白色
    ],
    'fill' => [
        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        'startColor' => ['argb' => 'FF4CAF50'],  // 緑色
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
        ],
    ],
    'alignment' => [
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
        'wrapText' => true,
    ],
];

$sheet->getStyle('A1:AB1')->applyFromArray($headerStyle);  // ヘッダーにスタイル適用

// 列の幅を設定
$sheet->getColumnDimension('A')->setWidth(15);
$sheet->getColumnDimension('B')->setWidth(30);
$sheet->getColumnDimension('C')->setWidth(15);
$sheet->getColumnDimension('D')->setWidth(50);
$sheet->getColumnDimension('E')->setWidth(15);
$sheet->getColumnDimension('F')->setWidth(15);
$sheet->getColumnDimension('G')->setWidth(15);
$sheet->getColumnDimension('H')->setWidth(30);
$sheet->getColumnDimension('I')->setWidth(30);
$sheet->getColumnDimension('J')->setWidth(15);
$sheet->getColumnDimension('K')->setWidth(15);
$sheet->getColumnDimension('L')->setWidth(15);
$sheet->getColumnDimension('M')->setWidth(15);
$sheet->getColumnDimension('N')->setWidth(15);
$sheet->getColumnDimension('O')->setWidth(50);
$sheet->getColumnDimension('P')->setWidth(50);
$sheet->getColumnDimension('Q')->setWidth(15);
$sheet->getColumnDimension('R')->setWidth(15);
$sheet->getColumnDimension('S')->setWidth(15);
$sheet->getColumnDimension('T')->setWidth(15);
$sheet->getColumnDimension('U')->setWidth(15);
$sheet->getColumnDimension('V')->setWidth(15);
$sheet->getColumnDimension('W')->setWidth(15);
$sheet->getColumnDimension('X')->setWidth(15);
$sheet->getColumnDimension('Y')->setWidth(15);
$sheet->getColumnDimension('Z')->setWidth(15);
$sheet->getColumnDimension('AA')->setWidth(15);
$sheet->getColumnDimension('AB')->setWidth(15);

// 行の高さを設定
$sheet->getRowDimension('1')->setRowHeight(60);  // ヘッダー行を高さ調整

// データ部分にも罫線を追加
$dataStyle = [
    'borders' => [
        'allBorders' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
        ],
    ],
    'alignment' => [
        'wrapText' => true,  // 折り返し設定
        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
    ],
];

$sheet->getStyle('A2:AB' . count($data)+1)->applyFromArray($dataStyle);

// Excelファイルの出力
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="trials.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
