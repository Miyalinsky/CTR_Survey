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
$sql = "
SELECT umin_id, CONCAT(umin_id, '\n', date_of_disclosure) AS umin_with_date, scientific_title, `condition`, narrative_objectives1, basic_objectives2, basic_objectives_others, developmental_phase, primary_outcomes, key_secondary_outcomes, study_type,
CONCAT(basic_design, '\n', randomization, '\n', randomization_unit, '\n', blinding, '\n', control) AS study_design, age_lower_limit, age_upper_limit, gender, key_inclusion_criteria, key_exclusion_criteria, target_sample_size,
CONCAT(institute, '\n', institute_org) AS institute, CONCAT(organization, '\n', organization_org) AS organization, irb_organization, institutions,
url_japanese
FROM trials 
WHERE scientific_title LIKE :keyword 
AND date_of_disclosure BETWEEN :startDate AND :endDate
";

// クエリの準備と実行
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':keyword' => "%$keyword%",
    ':startDate' => $startDate,
    ':endDate' => $endDate
]);

// データを取得してJSON形式で返す
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

if (count($data) > 0) {
    // 正常なデータを返す
    echo json_encode($data);
} else {
    // データがない場合のメッセージ
    echo json_encode(["message" => "該当するデータがありません"]);
}
?>