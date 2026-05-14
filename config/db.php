<?php
// db config and connection
define('DB_HOST', 'localhost');
define('DB_NAME', 'staffvaultdb');
define('DB_USER', 'root');
define('DB_PASS', ''); // default wamp pass

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);

    // throw exceptions for errors so it's secure
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // turn off emulated prepared statements
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    // don't show real error to users
    die("Database connection failed. Please check your configuration.");
}

// ensure session_token column exists for concurrent login checks
try {
    $pdo->exec("ALTER TABLE users ADD COLUMN session_token VARCHAR(255) DEFAULT NULL");
} catch (PDOException $e) {
    // ignore error if column already exists
}
?>