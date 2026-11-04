<?php
session_start();

try {
    $pdo = new PDO("sqlite:gravity_pos.db");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        fullname TEXT NOT NULL,
        username TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL,
        role TEXT DEFAULT 'customer'
    )");

    $error = "";
    $success = "";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $fullname = trim($_POST['fullname']);
        $username = trim($_POST['username']);
        $password = $_POST['password'];

        if (!empty($fullname) && !empty($username) && !empty($password)) {
            // Suriin kung may kaparehong username na
            $check = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
            $check->execute([$username]);
            
            if ($check->fetchColumn() > 0) {
                $error = "Gamit na ang username na '{$username}'. Pumili ng iba.";
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (fullname, username, password, role) VALUES (?, ?, ?, 'customer')");
                $stmt->execute([$fullname, $username, $hashedPassword]);
                $success = "Matagumpay ang registration! Maaari ka nang mag-login.";
            }
        } else {
            $error = "Kumpletuhin ang lahat ng fields!";
        }
    }
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - GRAVITY SpecialTea</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #1a120b; color: #f3f4f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .register-card { background-color: #26170b; border: 1px solid #3d2614; border-radius: 12px; padding: 30px; width: 100%; max-width: 400px; box-shadow: 0 10px 25px rgba(0,0,0,0.5); }
        .btn-amber { background-color: #d97706; color: #fff; font-weight: bold; width: 100%; }
        .btn-amber:hover { background-color: #b45309; color: #fff; }
    </style>
</head>
<body>

    <div class="register-card">
        <div class="text-center mb-4">
            <h3 class="fw-bold text-warning">🧋 GRAVITY SpecialTea</h3>
            <p class="text-secondary small">Gumawa ng Customer Account</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger py-2 small" role="alert">
                <?= $error; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success py-2 small" role="alert">
                <?= $success; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label text-secondary small">Buong Pangalan (Full Name)</label>
                <input type="text" class="form-control bg-dark text-white border-secondary" name="fullname" required placeholder="Juan Dela Cruz">
            </div>
            <div class="mb-3">
                <label class="form-label text-secondary small">Username</label>
                <input type="text" class="form-control bg-dark text-white border-secondary" name="username" required>
            </div>
            <div class="mb-3">
                <label class="form-label text-secondary small">Password</label>
                <input type="password" class="form-control bg-dark text-white border-secondary" name="password" required>
            </div>
            <button type="submit" class="btn btn-amber py-2 mb-3">Mag-register</button>
            
            <div class="text-center">
                <small class="text-secondary">May account ka na? <a href="login.php" class="text-warning text-decoration-none">Mag-login dito</a></small>
            </div>
        </form>
    </div>

</body>
</html>