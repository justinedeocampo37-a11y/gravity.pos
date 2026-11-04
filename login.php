<?php
// login.php - GRAVITY SpecialTea POS Login
session_start();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    try {
        $db = new PDO('sqlite:gravity_pos.db');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Linisin ang lumang session para maiwasan ang account overwrite
            session_unset();
            session_destroy();
            
            // Magsimula ng bagong malinis na session
            session_start();
            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'] ?? 'user';

            header("Location: index.php");
            exit();
        } else {
            $error = "Maling username o password!";
        }
    } catch (PDOException $e) {
        $error = "Database Error: " . $e->getMessage();
    }
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
        body { background-color: #121212; color: #fff; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card { background: #1e1e1e; border: 1px solid #333; border-radius: 12px; padding: 30px; width: 100%; max-width: 400px; box-shadow: 0 4px 20px rgba(0,0,0,0.5); }
        .form-control { background-color: #2a2a2a; border: 1px solid #444; color: #fff; }
        .form-control:focus { background-color: #2a2a2a; color: #fff; border-color: #ffc107; box-shadow: none; }
    </style>
</head>
<body>
    <div class="login-card">
        <h3 class="text-center mb-4 text-warning">☕ GRAVITY POS</h3>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger py-2"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST" action="login.php">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-warning w-100 fw-bold">Login</button>
        </form>
        <div class="text-center mt-3">
            <small class="text-muted">Wala pang account? <a href="register.php" class="text-warning text-decoration-none">Mag-register</a></small>
        </div>
    </div>
</body>
</html>