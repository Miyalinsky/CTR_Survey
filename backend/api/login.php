<?php
session_start();
include '../includes/db.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

// POSTリクエストからデータを取得
$data = json_decode(file_get_contents('php://input'), true);
$username = isset($data['username']) ? $data['username'] : '';
$password = isset($data['password']) ? $data['password'] : '';

if (empty($username) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'ユーザー名またはパスワードが空です']);
    exit();
}

// ユーザーをデータベースで検索
$sql = "SELECT * FROM users WHERE username = :username";
$stmt = $pdo->prepare($sql);
$stmt->execute([':username' => $username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['password'])) {

    error_log(password_hash($password, PASSWORD_DEFAULT));
    error_log("保存されたハッシュ: " . $user['password']);
    
    // パスワードが正しい場合
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['is_admin'] = $user['is_admin'];  // 管理者フラグをセッションに保存
    echo json_encode(['success' => true, 'is_admin' => $user['is_admin']]);
} else {
    // ユーザー名またはパスワードが間違っている場合
    echo json_encode(['success' => false, 'message' => '無効なユーザー名またはパスワードです']);
}
