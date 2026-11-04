<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

try {
    $pdo = new PDO("sqlite:gravity_pos.db");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_POST['update_status'])) {
        $order_id = $_POST['order_id'];
        $status = $_POST['status'];
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$status, $order_id]);
    }

    $orders = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - GRAVITY SpecialTea</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #1a120b; color: #f3f4f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .navbar-custom { background-color: #26170b; border-bottom: 1px solid #3d2614; }
        .card-custom { background-color: #26170b; border: 1px solid #3d2614; border-radius: 12px; }
    </style>
</head>
<body>

    <nav class="navbar navbar-dark navbar-custom px-4 shadow-sm mb-4">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <a class="navbar-brand fw-bold text-warning fs-4" href="#">👑 Admin Verification & Orders Dashboard</a>
            <a href="logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
        </div>
    </nav>

    <div class="container-fluid px-4">
        <h3 class="fw-bold text-warning mb-4">📋 Orders & Deposit Monitoring</h3>

        <div class="card card-custom p-3 shadow-sm">
            <?php if (empty($orders)): ?>
                <p class="text-secondary text-center my-3">Wala pang orders na nakatala.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-dark table-hover align-middle mb-0" style="font-size: 0.9rem;">
                        <thead>
                            <tr class="text-warning">
                                <th>#</th>
                                <th>Customer</th>
                                <th>Order Details</th>
                                <th>Total</th>
                                <th>10% Deposit</th>
                                <th>GCash Ref No.</th>
                                <th>Status</th>
                                <th>Oras</th>
                                <th>Aksyon</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $o): ?>
                                <tr>
                                    <td><?= $o['id']; ?></td>
                                    <td class="fw-bold"><?= htmlspecialchars($o['customer_name']); ?></td>
                                    <td style="max-width: 250px;"><?= htmlspecialchars($o['order_details']); ?></td>
                                    <td class="text-white fw-bold">₱<?= number_format($o['total_price'], 2); ?></td>
                                    <td class="text-warning fw-bold">₱<?= number_format($o['deposit_amount'] ?? ($o['total_price'] * 0.10), 2); ?></td>
                                    <td>
                                        <span class="badge bg-dark border border-warning text-warning px-2 py-1">
                                            <?= htmlspecialchars($o['payment_ref'] ?? 'N/A'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php 
                                            $badgeClass = 'bg-secondary';
                                            if ($o['status'] === 'Completed') $badgeClass = 'bg-success';
                                            else if ($o['status'] === 'Preparing') $badgeClass = 'bg-info text-dark';
                                            else if ($o['status'] === 'Pending Verification') $badgeClass = 'bg-warning text-dark';
                                            else if ($o['status'] === 'Cancelled') $badgeClass = 'bg-danger';
                                        ?>
                                        <span class="badge <?= $badgeClass; ?>">
                                            <?= $o['status']; ?>
                                        </span>
                                    </td>
                                    <td class="small text-secondary"><?= $o['created_at']; ?></td>
                                    <td>
                                        <form method="POST" class="d-flex gap-1">
                                            <input type="hidden" name="order_id" value="<?= $o['id']; ?>">
                                            <input type="hidden" name="update_status" value="1">
                                            <select name="status" class="form-select form-select-sm bg-dark text-white border-secondary" style="width: 140px;">
                                                <option value="Pending Verification" <?= $o['status'] === 'Pending Verification' ? 'selected' : ''; ?>>Pending Verif.</option>
                                                <option value="Preparing" <?= $o['status'] === 'Preparing' ? 'selected' : ''; ?>>Preparing</option>
                                                <option value="Completed" <?= $o['status'] === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                                                <option value="Cancelled" <?= $o['status'] === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                            </select>
                                            <button type="submit" class="btn btn-warning btn-sm text-dark fw-bold">Save</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>