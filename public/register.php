<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../config/db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid CSRF token.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        // check input data
        if (!preg_match('/^[a-zA-Z0-9_]{4,20}$/', $username)) {
            $error = "Username must be 4-20 characters and contain only letters, numbers, and underscores.";
        } elseif (strlen($password) < 8) {
            $error = "Password must be at least 8 characters long.";
        } else {
            // check if user exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                $error = "Username already exists.";
            } else {
                // hash the password using bcrypt
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);

                $stmt = $pdo->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
                if ($stmt->execute([$username, $hashed_password])) {
                    log_event("User Registered", "System", "New user registered: $username");
                    header("Location: index.php?msg=registered");
                    exit;
                } else {
                    $error = "An error occurred during registration.";
                }
            }
        }
    }
}
$csrf_token = generate_csrf_token();
require_once __DIR__ . '/../includes/header.php';
?>

<div class="auth-form">
    <h2>Register</h2>
    <?php if ($error): ?>
        <div class="alert error"><?php echo sanitize_output($error); ?></div>
    <?php endif; ?>
    <form action="register.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required autocomplete="off">
        </div>
        <div class="form-group">
            <label for="password">Password (Min 8 chars)</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="btn">Register</button>
    </form>
    <p style="text-align: center; margin-top: 15px;"><a href="index.php">Back to Login</a></p>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>