<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['valid' => false, 'reason' => 'unauthorized']);
    exit;
}

require_once __DIR__ . '/../config/db.php';

// check for concurrent login
if (isset($_SESSION['session_token'])) {
    $stmt = $pdo->prepare("SELECT session_token FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $current_token = $stmt->fetchColumn();

    if ($current_token && $current_token !== $_SESSION['session_token']) {
        session_unset();
        session_destroy();
        
        // clear session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        
        echo json_encode(['valid' => false, 'reason' => 'concurrent_login']);
        exit;
    }
}

echo json_encode(['valid' => true]);
?>
