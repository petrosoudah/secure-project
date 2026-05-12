<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../config/db.php';
require_login();

$error = '';
$record_id = $_GET['id'] ?? null;

if (!$record_id) {
    header("Location: dashboard.php");
    exit;
}

// get the employee info
$stmt = $pdo->prepare("SELECT * FROM employees WHERE id = ?");
$stmt->execute([$record_id]);
$record = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$record) {
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid CSRF token.';
        log_event("Edit Record Failed", $_SESSION['user_id'], "CSRF Token mismatch for ID: $record_id");
    } else {
        $first_name = trim($_POST['first_name'] ?? '');
        $last_name = trim($_POST['last_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $department = trim($_POST['department'] ?? '');

        if (empty($first_name) || empty($last_name) || empty($email) || empty($department)) {
            $error = "All fields are required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Invalid email format.";
        } else {
            $stmt = $pdo->prepare("UPDATE employees SET first_name=?, last_name=?, email=?, department=? WHERE id=?");
            if ($stmt->execute([$first_name, $last_name, $email, $department, $record_id])) {
                log_event("Edit Record Success", $_SESSION['user_id'], "Updated employee ID: $record_id");
                header("Location: dashboard.php?msg=updated");
                exit;
            } else {
                $error = "Failed to update record.";
            }
        }
    }
}
$csrf_token = generate_csrf_token();
require_once __DIR__ . '/../includes/header.php';
?>

<div class="record-form">
    <h2>Edit Employee</h2>
    <?php if ($error): ?>
        <div class="alert error"><?php echo sanitize_output($error); ?></div>
    <?php endif; ?>
    <form action="edit_record.php?id=<?php echo urlencode($record_id); ?>" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        <div class="form-group">
            <label for="first_name">First Name</label>
            <input type="text" id="first_name" name="first_name"
                value="<?php echo sanitize_output($record['first_name']); ?>" required>
        </div>
        <div class="form-group">
            <label for="last_name">Last Name</label>
            <input type="text" id="last_name" name="last_name"
                value="<?php echo sanitize_output($record['last_name']); ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo sanitize_output($record['email']); ?>"
                required>
        </div>
        <div class="form-group">
            <label for="department">Department</label>
            <input type="text" id="department" name="department"
                value="<?php echo sanitize_output($record['department']); ?>" required>
        </div>
        <button type="submit" class="btn">Update Record</button>
    </form>
    <p style="text-align: center; margin-top: 15px;"><a href="dashboard.php">Cancel</a></p>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>