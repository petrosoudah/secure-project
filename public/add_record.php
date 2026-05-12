<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../config/db.php';
require_login();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid CSRF token.';
        log_event("Add Record Failed", $_SESSION['user_id'], "CSRF Token mismatch");
    } else {
        // Sanitize and validate inputs
        $first_name = trim($_POST['first_name'] ?? '');
        $last_name = trim($_POST['last_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $department = trim($_POST['department'] ?? '');

        if (empty($first_name) || empty($last_name) || empty($email) || empty($department)) {
            $error = "All fields are required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Invalid email format.";
        } else {
            // Prepared statement to prevent SQL Injection
            $stmt = $pdo->prepare("INSERT INTO employees (first_name, last_name, email, department) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$first_name, $last_name, $email, $department])) {
                log_event("Add Record Success", $_SESSION['user_id'], "Added employee: $email");
                header("Location: dashboard.php?msg=added");
                exit;
            } else {
                $error = "Failed to add record.";
            }
        }
    }
}
$csrf_token = generate_csrf_token();
require_once __DIR__ . '/../includes/header.php';
?>

<div class="record-form">
    <h2>Add Employee</h2>
    <?php if ($error): ?>
        <div class="alert error"><?php echo sanitize_output($error); ?></div>
    <?php endif; ?>
    <form action="add_record.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        <div class="form-group">
            <label for="first_name">First Name</label>
            <input type="text" id="first_name" name="first_name" required>
        </div>
        <div class="form-group">
            <label for="last_name">Last Name</label>
            <input type="text" id="last_name" name="last_name" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="department">Department</label>
            <input type="text" id="department" name="department" required>
        </div>
        <button type="submit" class="btn">Add Record</button>
    </form>
    <p style="text-align: center; margin-top: 15px;"><a href="dashboard.php">Cancel</a></p>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>