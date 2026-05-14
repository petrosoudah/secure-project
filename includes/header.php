<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StaffVault - Secure Employee Directory</title>
    <link rel="stylesheet" href="css/style.css">
    <?php if(isset($_SESSION['user_id'])): ?>
    <script>
        // automatic session timeout redirect
        // time is slightly higher than 5 minutes to ensure php timeout triggers
        setTimeout(function() {
            window.location.href = 'index.php';
        }, 301000);

        // check if session is still valid (e.g. concurrent login) every 3 seconds
        setInterval(function() {
            fetch('check_session.php')
                .then(response => response.json())
                .then(data => {
                    if (!data.valid) {
                        window.location.href = 'index.php' + (data.reason ? '?error=' + data.reason : '');
                    }
                })
                .catch(err => console.error(err));
        }, 3000);
    </script>
    <?php endif; ?>
</head>
<body>
    <div class="container">
        <header>
            <h1>StaffVault</h1>
            <?php if(isset($_SESSION['user_id'])): ?>
                <nav>
                    <a href="dashboard.php">Dashboard</a>
                    <a href="logout.php">Logout</a>
                </nav>
            <?php endif; ?>
        </header>
        <main>
