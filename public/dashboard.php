<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_login();

// get employees safely
$stmt = $pdo->prepare("SELECT id, first_name, last_name, email, department FROM employees ORDER BY created_at DESC");
$stmt->execute();
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/../includes/header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>Employee Directory</h2>
    <a href="add_record.php" class="btn" style="width: auto;">+ Add Employee</a>
</div>

<?php if (isset($_GET['msg']) && $_GET['msg'] === 'added'): ?>
    <div class="alert success">Record added successfully.</div>
<?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
    <div class="alert success">Record deleted successfully.</div>
<?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'updated'): ?>
    <div class="alert success">Record updated successfully.</div>
<?php endif; ?>

<table>
    <thead>
        <tr>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Email</th>
            <th>Department</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($records as $row): ?>
            <tr>
                <td><?php echo sanitize_output($row['first_name']); ?></td>
                <td><?php echo sanitize_output($row['last_name']); ?></td>
                <td><?php echo sanitize_output($row['email']); ?></td>
                <td><?php echo sanitize_output($row['department']); ?></td>
                <td class="actions">
                    <a href="edit_record.php?id=<?php echo urlencode($row['id']); ?>">Edit</a>
                    <a href="delete_record.php?id=<?php echo urlencode($row['id']); ?>" class="delete"
                        onclick="return confirm('Are you sure you want to delete this record?');">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($records)): ?>
            <tr>
                <td colspan="5" style="text-align: center;">No records found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>