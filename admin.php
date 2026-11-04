<?php
// admin.php - GRAVITY SpecialTea Admin Dashboard
session_start();

// Siguraduhing naka-login at admin ang user
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: login.php");
    exit();
}

try {
    $db = new PDO('sqlite:gravity_pos.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Kunin ang lahat ng orders para ma-manage ng admin
    $orders = $db->query("SELECT * FROM orders ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

    // Pagpapalit ng status ng order (hal. galing Pending patungong Preparing o Completed)
    if (isset($_POST['update_status'])) {
        $order_id = (int)$_POST['order_id'];
        $new_status = $_POST['status'];
        
        $updateStmt = $db->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $updateStmt->execute([$new_status, $order_id]);
        
        header("Location: admin.php");
        exit();
    }
} catch (PDOException $e) {
    $db_error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - GRAVITY POS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { background-color: #121212; color: #fff; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .admin-card { background: #1e1e1e; border: 1px solid #333; border-radius: 12px; padding: 20px; }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark bg-dark px-4 mb-4 border-bottom border-secondary">
        <a class="navbar-brand fw-bold text-warning" href="#"><i class="bi bi-speedometer2"></i> GRAVITY Admin Dashboard</a>
        <div>
            <span class="text-muted me-3">Admin: <?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?></span>
            <a href="index.php" class="btn btn-outline-light btn-sm">Pumunta sa POS</a>
            <a href="logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
        </div>
    </nav>

    <div class="container my-4">
        <h3 class="mb-4 text-warning"><i class="bi bi-list-check"></i> Pamamahala ng mga Order</h3>

        <?php if (isset($db_error)): ?>
            <div class="alert alert-danger">Error: <?php echo htmlspecialchars($db_error); ?></div>
        <?php endif; ?>

        <div class="admin-card">
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0">
                    <thead>
                        <tr class="text-warning">
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Order Details</th>
                            <th>Total Price</th>
                            <th>Deposit (10%)</th>
                            <th>Reference No.</th>
                            <th>Status</th>
                            <th>Aksyon</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($orders)): ?>
                            <tr>
                               <td colspan="8" class="text-center text-muted py-4">Wala pang pumasok na order.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($orders as $ord): ?>
                                <tr>
                                    <td>#<?php echo $ord['id']; ?></td>
                                    <td><?php echo htmlspecialchars($ord['customer_name']); ?></td>
                                    <td><?php echo htmlspecialchars($ord['order_details']); ?></td>
                                    <td class="text-warning fw-bold">₱<?php echo number_format($ord['total_price'], 2); ?></td>
                                    <td>₱<?php echo number_format($ord['deposit_amount'], 2); ?></td>
                                    <td><code><?php echo htmlspecialchars($ord['payment_ref']); ?></code></td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            echo $ord['status'] === 'Completed' ? 'success' : 
                                                ($ord['status'] === 'Preparing' ? 'info' : 
                                                ($ord['status'] === 'Cancelled' ? 'danger' : 'warning text-dark')); 
                                        ?>">
                                            <?php echo $ord['status']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <form method="POST" class="d-flex gap-1">
                                            <input type="hidden" name="order_id" value="<?php echo $ord['id']; ?>">
                                            <select name="status" class="form-select form-select-sm bg-dark text-white border-secondary" style="width: 130px;">
                                                <option value="Pending Verification" <?php echo $ord['status'] === 'Pending Verification' ? 'selected' : ''; ?>>Pending</option>
                                                <option value="Preparing" <?php echo $ord['status'] === 'Preparing' ? 'selected' : ''; ?>>Preparing</option>
                                                <option value="Completed" <?php echo $ord['status'] === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                                                <option value="Cancelled" <?php echo $ord['status'] === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                            </select>
                                            <button type="submit" name="update_status" class="btn btn-warning btn-sm fw-bold">I-update</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>