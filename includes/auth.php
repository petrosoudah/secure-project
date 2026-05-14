<?php
session_start();
require_once __DIR__ . '/logger.php';



// add headers to stop xss and clickjacking
header("X-XSS-Protection: 1; mode=block");
header("X-Frame-Options: SAMEORIGIN");
header("X-Content-Type-Options: nosniff");

function require_login()
{
    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php?error=unauthorized");
        exit;
    }

    global $pdo;
    if (!isset($pdo)) {
        require_once __DIR__ . '/../config/db.php';
    }

    // check for concurrent login
    if (isset($_SESSION['session_token'])) {
        $stmt = $pdo->prepare("SELECT session_token FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $current_token = $stmt->fetchColumn();

        if ($current_token && $current_token !== $_SESSION['session_token']) {
            session_unset();
            session_destroy();

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

            header("Location: index.php?error=concurrent_login");
            exit;
        }
    }
}

// session expiration (5 mins)
$timeout_duration = 300;
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
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

    header("Location: index.php?error=session_expired");
    exit;
}
$_SESSION['LAST_ACTIVITY'] = time();

// function to clean output safely
function sanitize_output($data)
{
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}
?>