<?php
session_start();

try {
    $pdo = new PDO("sqlite:gravity_pos.db");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("CREATE TABLE IF NOT EXISTS products (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        category TEXT NOT NULL,
        price REAL NOT NULL,
        image TEXT DEFAULT ''
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS orders (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        customer_name TEXT NOT NULL,
        order_details TEXT NOT NULL,
        total_price REAL NOT NULL,
        status TEXT DEFAULT 'Preparing',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    $stmtProd = $pdo->query("SELECT COUNT(*) FROM products");
    if ($stmtProd->fetchColumn() == 0) {
        $pdo->exec("INSERT INTO products (name, category, price, image) VALUES 
            ('Wintermelon Milk Tea', 'Milk Tea', 89.00, 'https://images.unsplash.com/photo-1541658016709-82535e94bc69?w=500'),
            ('Dark Chocolate Frappe', 'Frappes', 110.00, 'https://images.unsplash.com/photo-1572490122747-3968b75cc699?w=500'),
            ('Caramel Macchiato', 'Coffee', 95.00, 'https://images.unsplash.com/photo-1485808190672-d0511bd3ef8c?w=500'),
            ('Cheese Fries', 'Snacks', 65.00, 'https://images.unsplash.com/photo-1576107232684-1279f390859f?w=500')");
    }

    // Initialize Cart Session kung wala pa
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $message = "";

    // 1. ADD TO CART ACTION
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_to_cart') {
        $product_id = $_POST['product_id'];
        $size = $_POST['size'];
        $sugar = $_POST['sugar'];
        $quantity = (int)$_POST['quantity'];

        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $prod = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($prod) {
            $base_price = $prod['price'] + ($size === 'Large' ? 15 : 0);
            $item_total = $base_price * $quantity;

            $_SESSION['cart'][] = [
                'product_id' => $prod['id'],
                'name' => $prod['name'],
                'size' => $size,
                'sugar' => $sugar,
                'quantity' => $quantity,
                'price' => $base_price,
                'total' => $item_total
            ];
            $message = "Naidagdag na sa cart ang " . $prod['name'] . "!";
        }
    }

    // 2. REMOVE ITEM FROM CART
    if (isset($_GET['remove'])) {
        $index = $_GET['remove'];
        if (isset($_SESSION['cart'][$index])) {
            unset($_SESSION['cart'][$index]);
            $_SESSION['cart'] = array_values($_SESSION['cart']); // Re-index array
        }
        header("Location: index.php");
        exit;
    }

    // 3. CHECKOUT / PLACE FINAL ORDER
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'checkout') {
        $customer_name = trim($_POST['customer_name']);

        if (!empty($customer_name) && !empty($_SESSION['cart'])) {
            $details_arr = [];
            $grand_total = 0;

            foreach ($_SESSION['cart'] as $item) {
                $details_arr[] = "{$item['quantity']}x {$item['name']} (Size: {$item['size']}, Sugar: {$item['sugar']}%)";
                $grand_total += $item['total'];
            }

            $order_details_str = implode(", ", $details_arr);

            $insert = $pdo->prepare("INSERT INTO orders (customer_name, order_details, total_price) VALUES (?, ?, ?)");
            $insert->execute([$customer_name, $order_details_str, $grand_total]);

            // Linisin ang cart pagkatapos mag-order
            $_SESSION['cart'] = [];
            $message = "Maraming salamat, " . htmlspecialchars($customer_name) . "! Matagumpay na naipadala ang iyong order.";
        }
    }

    $products = $pdo->query("SELECT * FROM products")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GRAVITY - Shopping Cart Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #1a120b; color: #f3f4f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .navbar-custom { background-color: #26170b; border-bottom: 1px solid #3d2614; }
        .ticker-bar { background-color: #311e0e; color: #fbbf24; font-size: 0.85rem; padding: 8px 0; border-bottom: 1px solid #4a2f16; }
        .product-card { background-color: #26170b; border: 1px solid #3d2614; border-radius: 12px; transition: transform 0.2s; color: #fff; }
        .product-card:hover { transform: translateY(-3px); border-color: #d97706; }
        .product-img { height: 140px; object-fit: cover; border-top-left-radius: 12px; border-top-right-radius: 12px; }
        .cart-box { background-color: #26170b; border: 1px solid #3d2614; border-radius: 12px; position: sticky; top: 20px; }
        .btn-amber { background-color: #d97706; color: #fff; font-weight: bold; }
        .btn-amber:hover { background-color: #b45309; color: #fff; }
    </style>
</head>
<body>

    <div class="ticker-bar text-center fw-semibold">
        🔥 GRAVITY Online Ordering • Mamili ng marami at i-checkout nang sabay-sabay!
    </div>

    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom px-4 shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold text-warning fs-4" href="#">🧋 GRAVITY SpecialTea</a>
            <div class="d-flex align-items-center gap-3">
                <a href="kds.php" target="_blank" class="btn btn-outline-warning btn-sm">KDS Admin View</a>
                <a href="sales.php" target="_blank" class="btn btn-outline-light btn-sm">Sales Dashboard</a>
            </div>
        </div>
    </nav>

    <div class="container my-4">
        <?php if (!empty($message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Left Side: Product Menu Grid -->
            <div class="col-lg-8">
                <h3 class="fw-bold mb-4 text-warning">Pumili ng Produkto</h3>
                <div class="row g-3">
                    <?php foreach ($products as $p): ?>
                        <div class="col-6 col-md-4">
                            <div class="card product-card h-100 shadow-sm">
                                <img src="<?= htmlspecialchars($p['image']); ?>" class="card-img-top product-img" alt="...">
                                <div class="card-body d-flex flex-column justify-content-between p-3">
                                    <div>
                                        <small class="text-warning text-uppercase fw-semibold" style="font-size: 0.65rem;"><?= htmlspecialchars($p['category']); ?></small>
                                        <h6 class="card-title fw-bold mt-1 mb-1"><?= htmlspecialchars($p['name']); ?></h6>
                                    </div>
                                    <div class="mt-2">
                                        <p class="text-warning fw-bold mb-2 small">₱<?= number_format($p['price'], 2); ?></p>
                                        <button class="btn btn-amber btn-sm w-100 py-1" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#orderModal" 
                                                onclick="selectProduct(<?= $p['id']; ?>, '<?= addslashes($p['name']); ?>', <?= $p['price']; ?>)">
                                            + Add to Cart
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Right Side: Shopping Cart Summary -->
            <div class="col-lg-4 mt-4 mt-lg-0">
                <div class="card cart-box p-3 text-white shadow-sm">
                    <h4 class="fw-bold text-warning mb-3">🛒 Your Shopping Cart</h4>
                    
                    <?php if (empty($_SESSION['cart'])): ?>
                        <p class="text-secondary small text-center my-4">Wala pang laman ang iyong cart.</p>
                    <?php else: ?>
                        <div class="table-responsive mb-3" style="max-height: 250px; overflow-y: auto;">
                            <table class="table table-dark table-sm align-middle mb-0" style="font-size: 0.85rem; background: transparent;">
                                <tbody>
                                    <?php 
                                    $cartTotal = 0;
                                    foreach ($_SESSION['cart'] as $index => $item): 
                                        $cartTotal += $item['total'];
                                    ?>
                                        <tr>
                                            <td>
                                                <strong><?= $item['quantity']; ?>x <?= htmlspecialchars($item['name']); ?></strong><br>
                                                <small class="text-secondary"><?= $item['size']; ?>, <?= $item['sugar']; ?>% Sugar</small>
                                            </td>
                                            <td class="text-end">₱<?= number_format($item['total'], 2); ?></td>
                                            <td class="text-end">
                                                <a href="index.php?remove=<?= $index; ?>" class="text-danger text-decoration-none fw-bold">&times;</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="border-top border-secondary pt-3 mb-3">
                            <div class="d-flex justify-content-between fs-5 fw-bold text-warning">
                                <span>Total:</span>
                                <span>₱<?= number_format($cartTotal, 2); ?></span>
                            </div>
                        </div>

                        <!-- Checkout Form -->
                        <form method="POST">
                            <input type="hidden" name="action" value="checkout">
                            <div class="mb-3">
                                <label class="form-label text-secondary small">Pangalan ng Customer</label>
                                <input type="text" class="form-control bg-dark text-white border-secondary form-control-sm" name="customer_name" required placeholder="Juan Dela Cruz">
                            </div>
                            <button type="submit" class="btn btn-amber w-100 py-2">I-submit at Mag-checkout</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para sa pagpili ng Size at Sugar -->
    <div class="modal fade" id="orderModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-white border border-secondary">
                <form method="POST">
                    <div class="modal-header border-bottom border-secondary">
                        <h5 class="modal-title fw-bold text-warning" id="modalProductName">Configure Item</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add_to_cart">
                        <input type="hidden" name="product_id" id="modalProductId">

                        <div class="mb-3">
                            <label class="form-label text-secondary">Size</label>
                            <select class="form-select bg-secondary text-white border-0" name="size">
                                <option value="Regular">Regular</option>
                                <option value="Large">Large (+₱15.00)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-secondary">Sugar Level</label>
                            <select class="form-select bg-secondary text-white border-0" name="sugar">
                                <option value="100">100% Normal</option>
                                <option value="75">75% Less</option>
                                <option value="50">50% Half</option>
                                <option value="25">25% Micro</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-secondary">Quantity</label>
                            <input type="number" class="form-control bg-secondary text-white border-0" name="quantity" value="1" min="1" max="20" required>
                        </div>
                    </div>
                    <div class="modal-footer border-top border-secondary">
                        <button type="button" class="btn btn-outline-light btn-sm" data-bs-dismiss="modal">Kanselahin</button>
                        <button type="submit" class="btn btn-amber">Idagdag sa Cart</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function selectProduct(id, name, price) {
            document.getElementById('modalProductId').value = id;
            document.getElementById('modalProductName').innerText = 'Add ' + name + ' (Base: ₱' + price.toFixed(2) + ')';
        }
    </script>
</body>
</html>