<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../config/db.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid CSRF token.';
        log_event("Login Failed", "Guest", "Invalid CSRF token used");
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $error = 'Please fill in all fields.';
        } else {
            // secure query using prepared statement (prevents sql injection)
            $stmt = $pdo->prepare("SELECT id, username, password_hash FROM users WHERE username = ? LIMIT 1");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password_hash'])) {
                // regenerate session id to prevent session fixation
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];

                log_event("Login Success", $user['id'], "User logged in successfully");
                header("Location: dashboard.php");
                exit;
            } else {
                $error = 'Invalid credentials.';
                log_event("Login Failed", "Guest", "Invalid credentials for username: $username");
            }
        }
    }
}
$csrf_token = generate_csrf_token();
require_once __DIR__ . '/../includes/header.php';
?>

<div class="auth-form">
    <h2>Login</h2>
    <?php if ($error): ?>
        <div class="alert error"><?php echo sanitize_output($error); ?></div>
    <?php endif; ?>
    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'registered'): ?>
        <div class="alert success">Registration successful. Please login.</div>
    <?php endif; ?>
    <form action="index.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required autocomplete="off">
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="btn">Login</button>
    </form>
    <p style="text-align: center; margin-top: 15px;"><a href="register.php">Register</a></p>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>