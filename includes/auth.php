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
}

// session expiration (15 mins)
$timeout_duration = 900;
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    session_unset();
    session_destroy();
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