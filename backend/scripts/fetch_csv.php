<?php
// backend/scripts/fetch_csv.php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// メモリ制限を512MBに設定
ini_set('memory_limit', '512M');

include '../includes/db.php';  // データベース接続

$csvUrl = 'https://upload.umin.ac.jp/ctr_csv/ctr_data_j.csv.gz';
$localGz = '../data/ctr_data_j.csv.gz';  // 圧縮ファイルを保存するパス
$localCsv = '../data/ctr_data_j.csv';  // 解凍後のCSVファイルのパス

// 日付のバリデーション関数
function validateDate($date, $format = 'Y/m/d')
{
    // null や空の値を処理
    if ($date === null || $date === '') {
        return false;  // 不正な日付として扱う
    }

    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}


// .gzファイルをダウンロード
$gzData = file_get_contents($csvUrl);
file_put_contents($localGz, $gzData);

// .gzファイルを解凍してCSVファイルに変換
$gzFile = gzopen($localGz, 'rb');
$csvFile = fopen($localCsv, 'wb');
while (!gzeof($gzFile)) {
    fwrite($csvFile, gzread($gzFile, 4096));
}
gzclose($gzFile);
fclose($csvFile);

// CSVファイルをパースしてデータベースに保存
if (($handle = fopen($localCsv, 'r')) !== FALSE) {
    // ヘッダー行をスキップ
    fgetcsv($handle);
    fgetcsv($handle);

    // データの準備
    $stmt = $pdo->prepare("INSERT INTO trials (
        umin_id, scientific_title, `condition`, narrative_objectives1, basic_objectives2, 
        basic_objectives_others, developmental_phase, primary_outcomes, key_secondary_outcomes, 
        study_type, basic_design, randomization, randomization_unit, blinding, control, 
        age_lower_limit, age_upper_limit, gender, key_inclusion_criteria, key_exclusion_criteria, 
        target_sample_size, institute, institute_org, organization, organization_org, irb_organization, institutions, 
        date_of_disclosure, url_japanese
    ) VALUES (
        :umin_id, :scientific_title, :condition, :narrative_objectives1, :basic_objectives2, 
        :basic_objectives_others, :developmental_phase, :primary_outcomes, :key_secondary_outcomes, 
        :study_type, :basic_design, :randomization, :randomization_unit, :blinding, :control, 
        :age_lower_limit, :age_upper_limit, :gender, :key_inclusion_criteria, :key_exclusion_criteria, 
        :target_sample_size, :institute, :institute_org, :organization, :organization_org, :irb_organization, :institutions, 
        :date_of_disclosure, :url_japanese
    ) ON DUPLICATE KEY UPDATE 
        scientific_title = VALUES(scientific_title), `condition` = VALUES(`condition`), 
        narrative_objectives1 = VALUES(narrative_objectives1), basic_objectives2 = VALUES(basic_objectives2), 
        basic_objectives_others = VALUES(basic_objectives_others), developmental_phase = VALUES(developmental_phase), 
        primary_outcomes = VALUES(primary_outcomes), key_secondary_outcomes = VALUES(key_secondary_outcomes), 
        study_type = VALUES(study_type), basic_design = VALUES(basic_design), 
        randomization = VALUES(randomization), randomization_unit = VALUES(randomization_unit), 
        blinding = VALUES(blinding), control = VALUES(control), age_lower_limit = VALUES(age_lower_limit), 
        age_upper_limit = VALUES(age_upper_limit), gender = VALUES(gender), 
        key_inclusion_criteria = VALUES(key_inclusion_criteria), key_exclusion_criteria = VALUES(key_exclusion_criteria), 
        target_sample_size = VALUES(target_sample_size), institute = VALUES(institute), 
        institute_org = VALUES(institute_org), organization = VALUES(organization), 
        organization_org = VALUES(organization_org), irb_organization = VALUES(irb_organization), institutions = VALUES(institutions), 
        date_of_disclosure = VALUES(date_of_disclosure), url_japanese = VALUES(url_japanese)
    ");

    // CSVの各行をデータベースに挿入
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        // // 日付のバリデーション: 正しくない場合は null を設定
        // $date_of_disclosure = validateDate($data[100]) ? $data[100] : null;

        // インデックスが存在するかを確認し、存在しない場合はデフォルト値を使用
        $umin_id = isset($data[1]) ? $data[1] : null;
        $scientific_title = isset($data[6]) ? $data[6] : null;
        $condition = isset($data[11]) ? $data[11] : null;
        $narrative_objectives1 = isset($data[16]) ? $data[16] : null;
        $basic_objectives2 = isset($data[18]) ? $data[18] : null;
        $basic_objectives_others = isset($data[19]) ? $data[19] : null;
        $developmental_phase = isset($data[23]) ? $data[23] : null;
        $primary_outcomes = isset($data[24]) ? $data[24] : null;
        $key_secondary_outcomes = isset($data[26]) ? $data[26] : null;
        $study_type = isset($data[28]) ? $data[28] : null;
        $basic_design = isset($data[29]) ? $data[29] : null;
        $randomization = isset($data[30]) ? $data[30] : null;
        $randomization_unit = isset($data[31]) ? $data[31] : null;
        $blinding = isset($data[32]) ? $data[32] : null;
        $control = isset($data[33]) ? $data[33] : null;
        $age_lower_limit = isset($data[62]) ? $data[62] : null;
        $age_upper_limit = isset($data[63]) ? $data[63] : null;
        $gender = isset($data[64]) ? $data[64] : null;
        $key_inclusion_criteria = isset($data[65]) ? $data[65] : null;
        $key_exclusion_criteria = isset($data[67]) ? $data[67] : null;
        $target_sample_size = isset($data[69]) ? $data[69] : null;
        $institute = isset($data[70]) ? $data[70] : null;
        $institute_org = isset($data[71]) ? $data[71] : null;
        $organization = isset($data[74]) ? $data[74] : null;
        $organization_org = isset($data[75]) ? $data[75] : null;
        $irb_organization = isset($data[85]) ? $data[85] : null;
        $institutions = isset($data[99]) ? $data[99] : null;
        $date_of_disclosure = isset($data[100]) ? $data[100] : null;
        $url_japanese = isset($data[136]) ? $data[136] : null;

        // 日付のバリデーション: 正しくない場合は null を設定
        $date_of_disclosure = validateDate($date_of_disclosure) ? $date_of_disclosure : null;

        $target_sample_size = is_numeric($target_sample_size) && $target_sample_size > 0 && $target_sample_size <= 10000000 ? (int)$target_sample_size : null;

        //var_dump($data[11]);
        $stmt->execute([
        // var_dump([
            ':umin_id' => $umin_id,
            ':scientific_title' => $scientific_title,
            ':condition' => $condition,
            ':narrative_objectives1' => $narrative_objectives1,
            ':basic_objectives2' => $basic_objectives2,
            ':basic_objectives_others' => $basic_objectives_others,
            ':developmental_phase' => $developmental_phase,
            ':primary_outcomes' => $primary_outcomes,
            ':key_secondary_outcomes' => $key_secondary_outcomes,
            ':study_type' => $study_type,
            ':basic_design' => $basic_design,
            ':randomization' => $randomization,
            ':randomization_unit' => $randomization_unit,
            ':blinding' => $blinding,
            ':control' => $control,
            ':age_lower_limit' => $age_lower_limit,
            ':age_upper_limit' => $age_upper_limit,
            ':gender' => $gender,
            ':key_inclusion_criteria' => $key_inclusion_criteria,
            ':key_exclusion_criteria' => $key_exclusion_criteria,
            ':target_sample_size' => $target_sample_size,
            ':institute' => $institute,
            ':institute_org' => $institute_org,
            ':organization' => $organization,
            ':organization_org' => $organization_org,
            ':irb_organization' => $irb_organization,
            ':institutions' => $institutions,
            ':date_of_disclosure' => $date_of_disclosure,
            ':url_japanese' => $url_japanese
        ]);
    }
    fclose($handle);
    //exit();
}

$pdo = null;

// 更新完了のレスポンスを返す
echo json_encode(["message" => "データベースの更新が完了しました。"]);
?>