<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_login();

$record_id = $_GET['id'] ?? null;

if ($record_id) {
    // delete record safely
    $stmt = $pdo->prepare("DELETE FROM employees WHERE id = ?");
    if ($stmt->execute([$record_id])) {
        log_event("Delete Record Success", $_SESSION['user_id'], "Deleted employee ID: $record_id");
    } else {
        log_event("Delete Record Failed", $_SESSION['user_id'], "Failed to delete employee ID: $record_id");
    }
}

header("Location: dashboard.php?msg=deleted");
exit;
?>