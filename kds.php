<?php
try {
    $pdo = new PDO("sqlite:gravity_pos.db");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$_POST['status'], $_POST['order_id']]);
    }

    $orders = $pdo->query("SELECT * FROM orders ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $orders = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>GRAVITY - Kitchen Display System (KDS)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark mb-4 shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold text-danger" href="#">🧋 GRAVITY KDS (Admin Panel)</a>
            <a href="index.php" class="btn btn-outline-light btn-sm">Bumalik sa Customer Ordering</a>
        </div>
    </nav>
    <div class="container mb-5">
        <h2 class="mb-3 text-secondary">Kitchen Display & Order Tracker</h2>
        <div class="card p-4 shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Order ID</th>
                            <th>Customer Name</th>
                            <th>Order Details</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($orders)): ?>
                            <?php foreach ($orders as $o): ?>
                                <tr>
                                    <td>#<?= $o['id']; ?></td>
                                    <td><strong><?= htmlspecialchars($o['customer_name']); ?></strong></td>
                                    <td><?= htmlspecialchars($o['order_details']); ?></td>
                                    <td>
                                        <span class="badge bg-<?= $o['status'] === 'Preparing' ? 'warning text-dark' : ($o['status'] === 'Ready' ? 'primary' : 'success'); ?>">
                                            <?= htmlspecialchars($o['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <form method="POST" class="d-flex gap-2">
                                            <input type="hidden" name="action" value="update_status">
                                            <input type="hidden" name="order_id" value="<?= $o['id']; ?>">
                                            <select name="status" class="form-select form-select-sm" style="width: 130px;">
                                                <option value="Preparing" <?= $o['status'] == 'Preparing' ? 'selected' : ''; ?>>Preparing</option>
                                                <option value="Ready" <?= $o['status'] == 'Ready' ? 'selected' : ''; ?>>Ready</option>
                                                <option value="Completed" <?= $o['status'] == 'Completed' ? 'selected' : ''; ?>>Completed</option>
                                            </select>
                                            <button type="submit" class="btn btn-sm btn-dark">Update</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center text-muted py-4">Wala pang pumasok na order.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>