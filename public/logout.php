<?php
session_start();
require_once __DIR__ . '/../includes/logger.php';

if (isset($_SESSION['user_id'])) {
    log_event("Logout", $_SESSION['user_id'], "User logged out");
}

session_unset();
session_destroy();

header("Location: index.php");
exit;
?>
