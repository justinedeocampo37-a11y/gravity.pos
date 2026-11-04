<?php
session_start();

try {
    $pdo = new PDO("sqlite:gravity_pos.db");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Gawin ang table para sa mga users kung wala pa
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        fullname TEXT NOT NULL,
        username TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL,
        role TEXT DEFAULT 'customer'
    )");

    // I-default ang admin account kung wala pa
    $stmtAdmin = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role = 'admin'");
    $stmtAdmin->execute();
    if ($stmtAdmin->fetchColumn() == 0) {
        $adminPass = password_hash('admin123', PASSWORD_DEFAULT);
        $pdo->exec("INSERT INTO users (fullname, username, password, role) VALUES ('Administrator', 'admin', '$adminPass', 'admin')");
    }

    $error = "";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username']);
        $password = $_POST['password'];

        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] === 'admin') {
                header("Location: admin.php");
                exit;
            } else {
                header("Location: index.php");
                exit;
            }
        } else {
            $error = "Mali ang username o password!";
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
    <title>Login - GRAVITY SpecialTea</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #1a120b; color: #f3f4f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card { background-color: #26170b; border: 1px solid #3d2614; border-radius: 12px; padding: 30px; width: 100%; max-width: 400px; box-shadow: 0 10px 25px rgba(0,0,0,0.5); }
        .btn-amber { background-color: #d97706; color: #fff; font-weight: bold; width: 100%; }
        .btn-amber:hover { background-color: #b45309; color: #fff; }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="text-center mb-4">
            <h3 class="fw-bold text-warning">🧋 GRAVITY SpecialTea</h3>
            <p class="text-secondary small">Mag-login para makapag-order</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger py-2 small" role="alert">
                <?= $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label text-secondary small">Username</label>
                <input type="text" class="form-control bg-dark text-white border-secondary" name="username" required>
            </div>
            <div class="mb-3">
                <label class="form-label text-secondary small">Password</label>
                <input type="password" class="form-control bg-dark text-white border-secondary" name="password" required>
            </div>
            <button type="submit" class="btn btn-amber py-2 mb-3">Mag-login</button>
            
            <div class="text-center">
                <small class="text-secondary">Wala ka pang account? <a href="register.php" class="text-warning text-decoration-none">Mag-register dito</a></small>
            </div>
        </form>
    </div>

</body>
</html>