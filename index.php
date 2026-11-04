<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

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

    // In-update ang orders table para magkaroon ng user_id para matrack kung kaninong customer ang order
    $pdo->exec("CREATE TABLE IF NOT EXISTS orders (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER DEFAULT 0,
        customer_name TEXT NOT NULL,
        order_details TEXT NOT NULL,
        total_price REAL NOT NULL,
        deposit_amount REAL DEFAULT 0,
        payment_ref TEXT DEFAULT '',
        status TEXT DEFAULT 'Pending Verification',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    $stmtProd = $pdo->query("SELECT COUNT(*) FROM products");
    if ($stmtProd->fetchColumn() == 0) {
        $pdo->exec("INSERT INTO products (name, category, price, image) VALUES 
            ('Dark Choco Milk Tea', 'Milk Tea', 29.00, 'https://images.unsplash.com/photo-1541658016709-82535e94bc69?w=500&q=80'),
            ('Matcha Milk Tea', 'Milk Tea', 29.00, 'https://images.unsplash.com/photo-1536256263959-770b48d82b0a?w=500&q=80'),
            ('Cheesecake Milk Tea', 'Milk Tea', 29.00, 'https://images.unsplash.com/photo-1558857563-b3c10f877d95?w=500&q=80'),
            ('Cookies & Cream Milk Tea', 'Milk Tea', 29.00, 'https://images.unsplash.com/photo-1558857563-b3c10f877d95?w=500&q=80'),
            ('Wintermelon Milk Tea', 'Milk Tea', 29.00, 'images/wintermelon fruit tea.jpg'),
            ('Okinawa Milk Tea', 'Milk Tea', 29.00, 'https://images.unsplash.com/photo-1525385133512-2f3bdd039a54?w=500&q=80'),
            ('Red Velvet Milk Tea', 'Milk Tea', 29.00, 'https://images.unsplash.com/photo-1541658016709-82535e94bc69?w=500&q=80'),
            ('Chocolate Milk Tea', 'Milk Tea', 29.00, 'https://images.unsplash.com/photo-1544787219-7f47ccb76574?w=500&q=80'),
            ('Salted Caramel Milk Tea', 'Milk Tea', 29.00, 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=500&q=80'),
            ('Taro Milk Tea', 'Milk Tea', 29.00, 'https://images.unsplash.com/photo-1576092768241-dec231879fc3?w=500&q=80'),
            ('Strawberry Milk Tea', 'Milk Tea', 29.00, 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=500&q=80'),
            ('Very Rocky Road Milk Tea', 'Milk Tea', 29.00, 'https://images.unsplash.com/photo-1541658016709-82535e94bc69?w=500&q=80'),
            ('Choco Berry Milk Tea', 'Milk Tea', 29.00, 'https://images.unsplash.com/photo-1544787219-7f47ccb76574?w=500&q=80'),
            ('Lychee Fruit Tea', 'Fruit Tea', 29.00, 'images/lychee fruit tea.jpg'),
            ('Kiwi Fruit Tea', 'Fruit Tea', 29.00, 'https://images.unsplash.com/photo-1536256263959-770b48d82b0a?w=500&q=80'),
            ('Lemon Fruit Tea', 'Fruit Tea', 29.00, 'images/lemon fruit tea.jpg'),
            ('Green Apple Fruit Tea', 'Fruit Tea', 29.00, 'images/green apple fruit tea.jpg'),
            ('Blueberry Fruit Tea', 'Fruit Tea', 29.00, 'images/blueberry fruit tea.jpg'),
            ('Strawberry Fruit Tea', 'Fruit Tea', 29.00, 'images/strawberry fruit tea.jpg'),
            ('Mango Fruit Tea', 'Fruit Tea', 29.00, 'images/mango fruit tea.jpg'),
            ('Watermelon Fruit Tea', 'Fruit Tea', 29.00, 'images/wintermelon fruit tea.jpg'),
            ('Mixed Berries Fruit Tea', 'Fruit Tea', 29.00, 'images/mixed berries fruit tea.jpg'),
            ('Caffe Macchiato', 'Iced Coffee', 29.00, 'https://images.unsplash.com/photo-1485808190672-d0511bd3ef8c?w=500&q=80'),
            ('Caffe Mocha', 'Iced Coffee', 29.00, 'https://images.unsplash.com/photo-1572490122747-3968b75cc699?w=500&q=80'),
            ('Caffe Vanilla', 'Iced Coffee', 29.00, 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=500&q=80'),
            ('Caffe Fudge', 'Iced Coffee', 29.00, 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=500&q=80'),
            ('Caffe Matcha', 'Iced Coffee', 29.00, 'https://images.unsplash.com/photo-1536256263959-770b48d82b0a?w=500&q=80'),
            ('Caffe Hazelnut Latte', 'Iced Coffee', 29.00, 'https://images.unsplash.com/photo-1485808190672-d0511bd3ef8c?w=500&q=80'),
            ('Caffe Caramel', 'Iced Coffee', 29.00, 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=500&q=80'),
            ('Caffe Cappuccino', 'Iced Coffee', 29.00, 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=500&q=80'),
            ('Caffe White Mochaccino', 'Iced Coffee', 29.00, 'https://images.unsplash.com/photo-1485808190672-d0511bd3ef8c?w=500&q=80'),
            ('Caffe Butterscotch', 'Iced Coffee', 29.00, 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=500&q=80'),
            ('Caffe White Chocolate', 'Iced Coffee', 29.00, 'https://images.unsplash.com/photo-1485808190672-d0511bd3ef8c?w=500&q=80'),
            ('Mocha Frappe', 'Frappe', 49.00, 'images/mocha frappe.jpg'),
            ('Coffee Jelly Frappe', 'Frappe', 49.00, 'images/coffee jelly frappe.jpg'),
            ('Caramel Macchiato Frappe', 'Frappe', 49.00, 'images/caramel macchiato frappe.jpg'),
            ('Vanilla Coffee Frappe', 'Frappe', 49.00, 'images/vanilla coffee frappe.jpg'),
            ('Caramel Cappuccino Frappe', 'Frappe', 49.00, 'images/caramel cappuccinno frappe.jpg'),
            ('Cookies & Cream Frappe', 'Frappe', 59.00, 'images/cookies and cream frappe.jpg'),
            ('Cheesecake Frappe', 'Frappe', 59.00, 'images/cheesecake frappe.jpg'),
            ('Taro Frappe', 'Frappe', 59.00, 'images/taro frappe.jpg'),
            ('Matcha Frappe', 'Frappe', 59.00, 'images/matcha frappe.jpg'),
            ('Chocolate Frappe', 'Frappe', 59.00, 'images/chocolate frappe.jpg'),
            ('Java Chip Frappe', 'Frappe', 59.00, 'images/java chips frappe.jpg'),
            ('Strawberry Frappe', 'Frappe', 59.00, 'images/strawberry frappe.jpg'),
            ('Avocado Frappe', 'Frappe', 59.00, 'images/avocado frappe.jpg'),
            ('Strawberry Vanilla Frappe', 'Frappe', 59.00, 'images/strawberry vanilla frappe.jpg'),
            ('Hot Choco', 'Hot Cups', 35.00, 'https://images.unsplash.com/photo-1544787219-7f47ccb76574?w=500&q=80'),
            ('Hot Cappuccino', 'Hot Cups', 35.00, 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=500&q=80'),
            ('Hot Mocha', 'Hot Cups', 35.00, 'https://images.unsplash.com/photo-1572490122747-3968b75cc699?w=500&q=80'),
            ('Hot Caramel', 'Hot Cups', 35.00, 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=500&q=80'),
            ('Hot Matcha', 'Hot Cups', 35.00, 'https://images.unsplash.com/photo-1536256263959-770b48d82b0a?w=500&q=80'),
            ('Mix and Match Set', 'Specials', 75.00, 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=500&q=80'),
            ('Iskrambol Special', 'Iskrambol', 76.00, 'https://images.unsplash.com/photo-1572490122747-3968b75cc699?w=500&q=80'),
            ('Fruit Fizz', 'Fruit Fizz', 59.00, 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?w=500&q=80'),
            ('Choco Lava Special Drink', 'Special Drinks', 49.00, 'https://images.unsplash.com/photo-1544787219-7f47ccb76574?w=500&q=80'),
            ('Cheese Fries (G-Snacks)', 'Snacks', 35.00, 'images/fries.jpg'),
            ('Chicken Nuggets', 'Snacks', 50.00, 'https://images.unsplash.com/photo-1562967914-608f82629710?w=500&q=80'),
            ('Milky Donuts', 'Snacks', 25.00, 'https://images.unsplash.com/photo-1551024709-8f23befc6f87?w=500&q=80'),
            ('G-Cookies', 'Snacks', 15.00, 'https://images.unsplash.com/photo-1499636136210-6f4ee915583e?w=500&q=80')");
    }

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $message = "";

    // Pag-cancel ng order ng customer (Pwede lang kung 'Pending Verification')
    if (isset($_GET['cancel_order'])) {
        $cancel_id = (int)$_GET['cancel_order'];
        $checkOrder = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
        $checkOrder->execute([$cancel_id, $_SESSION['user_id']]);
        $ordToCancel = $checkOrder->fetch(PDO::FETCH_ASSOC);

        if ($ordToCancel && $ordToCancel['status'] === 'Pending Verification') {
            $upd = $pdo->prepare("UPDATE orders SET status = 'Cancelled' WHERE id = ?");
            $upd->execute([$cancel_id]);
            $message = "Matagumpay na na-kansela ang iyong order (#{$cancel_id}).";
        } else {
            $message = "Hindi maaring kanselahin ang order na ito dahil pinoproseso na o tapos na.";
        }
        header("Location: index.php");
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_to_cart') {
        $product_id = $_POST['product_id'];
        $size = $_POST['size'];
        $sugar = $_POST['sugar'];
        $quantity = (int)$_POST['quantity'];

        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $prod = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($prod) {
            $size_extra = ($size === '22oz') ? 10 : 0;
            $base_price = $prod['price'] + $size_extra;
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
            $message = "Naidagdag na sa cart ang " . htmlspecialchars($prod['name']) . "!";
        }
    }

    if (isset($_GET['remove'])) {
        $index = $_GET['remove'];
        if (isset($_SESSION['cart'][$index])) {
            unset($_SESSION['cart'][$index]);
            $_SESSION['cart'] = array_values($_SESSION['cart']);
        }
        header("Location: index.php");
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'checkout') {
        $customer_name = trim($_POST['customer_name']);
        $payment_ref = trim($_POST['payment_ref']);

        if (!empty($customer_name) && !empty($payment_ref) && !empty($_SESSION['cart'])) {
            $details_arr = [];
            $grand_total = 0;

            foreach ($_SESSION['cart'] as $item) {
                $details_arr[] = "{$item['quantity']}x {$item['name']} (Size: {$item['size']}, Sugar: {$item['sugar']}%)";
                $grand_total += $item['total'];
            }

            $deposit_amount = $grand_total * 0.10;
            $order_details_str = implode(", ", $details_arr);

            $insert = $pdo->prepare("INSERT INTO orders (user_id, customer_name, order_details, total_price, deposit_amount, payment_ref, status) VALUES (?, ?, ?, ?, ?, ?, 'Pending Verification')");
            $insert->execute([$_SESSION['user_id'], $customer_name, $order_details_str, $grand_total, $deposit_amount, $payment_ref]);

            $_SESSION['cart'] = [];
            $message = "Salamat, " . htmlspecialchars($customer_name) . "! Na-submit ang order. Hintayin ang verification ng iyong 10% deposit.";
        }
    }

    $products = $pdo->query("SELECT * FROM products")->fetchAll(PDO::FETCH_ASSOC);

    // Kunin ang kasaysayan ng orders ng naka-login na customer
    $myOrdersStmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
    $myOrdersStmt->execute([$_SESSION['user_id']]);
    $myOrders = $myOrdersStmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GRAVITY SpecialTea - Modern Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg-main: #0d0907;
            --card-bg: rgba(28, 20, 14, 0.75);
            --card-border: rgba(217, 119, 6, 0.18);
            --accent-amber: #f59e0b;
            --accent-glow: rgba(245, 158, 11, 0.35);
            --text-muted: #9ca3af;
        }

        body { 
            background-color: var(--bg-main); 
            color: #f3f4f6; 
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(245, 158, 11, 0.05) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(180, 83, 9, 0.05) 0%, transparent 40%);
            background-attachment: fixed;
            min-height: 100vh;
        }

        .navbar-custom { 
            background: rgba(18, 12, 8, 0.85); 
            backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08); 
        }

        .banner-hero {
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.15) 0%, rgba(120, 53, 15, 0.25) 100%);
            border: 1px solid var(--card-border);
            border-radius: 20px;
            padding: 24px;
            backdrop-filter: blur(10px);
        }

        .category-pill {
            color: var(--text-muted);
            background: rgba(38, 23, 11, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 30px;
            padding: 8px 18px;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
        }

        .category-pill.active, .category-pill:hover {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #fff !important;
            border-color: #f59e0b;
            box-shadow: 0 4px 15px var(--accent-glow);
            transform: translateY(-2px);
        }

        .product-card { 
            background: var(--card-bg); 
            border: 1px solid var(--card-border); 
            border-radius: 18px; 
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1); 
            overflow: hidden;
            backdrop-filter: blur(12px);
        }

        .product-card:hover { 
            transform: translateY(-8px) scale(1.01); 
            border-color: var(--accent-amber); 
            box-shadow: 0 14px 30px rgba(0, 0, 0, 0.6), 0 0 20px var(--accent-glow);
        }

        .img-wrapper {
            height: 150px;
            overflow: hidden;
            position: relative;
        }

        .product-img { 
            width: 100%;
            height: 100%;
            object-fit: cover; 
            transition: transform 0.6s ease;
        }

        .product-card:hover .product-img {
            transform: scale(1.12);
        }

        .cart-box { 
            background: var(--card-bg); 
            border: 1px solid var(--card-border); 
            border-radius: 20px; 
            backdrop-filter: blur(16px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        }

        .btn-amber { 
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #fff; 
            font-weight: 700; 
            border: none;
            border-radius: 12px;
            transition: all 0.25s ease;
        }

        .btn-amber:hover { 
            background: linear-gradient(135deg, #fbbf24 0%, #b45309 100%);
            color: #fff; 
            box-shadow: 0 6px 18px var(--accent-glow);
            transform: translateY(-1px);
        }

        .glass-input {
            background: rgba(15, 10, 6, 0.7) !important;
            border: 1px solid rgba(255, 255, 255, 0.12) !important;
            color: #fff !important;
            border-radius: 10px;
        }

        .glass-input:focus {
            border-color: var(--accent-amber) !important;
            box-shadow: 0 0 10px var(--accent-glow) !important;
        }

        .deposit-card {
            background: rgba(245, 158, 11, 0.08);
            border: 1px dashed rgba(245, 158, 11, 0.4);
            border-radius: 14px;
            padding: 14px;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-dark navbar-custom px-4 sticky-top">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <a class="navbar-brand fw-extrabold text-warning fs-4 d-flex align-items-center gap-2" href="#">
                <i class="bi bi-cup-hot-fill text-warning"></i> GRAVITY <span class="badge bg-warning text-dark fs-6 rounded-pill">SpecialTea</span>
            </a>
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-outline-warning btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#ordersModal">
                    <i class="bi bi-clock-history"></i> My Orders & History
                </button>
                <div class="d-none d-md-flex align-items-center gap-2 bg-dark bg-opacity-50 px-3 py-1.5 rounded-pill border border-secondary border-opacity-25">
                    <i class="bi bi-person-circle text-warning"></i>
                    <span class="text-white small fw-semibold"><?= htmlspecialchars($_SESSION['fullname']); ?></span>
                </div>
                <a href="logout.php" class="btn btn-outline-danger btn-sm rounded-pill px-3"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </div>
        </div>
    </nav>

    <div class="container my-4">
        
        <!-- Top Hero Banner -->
        <div class="banner-hero mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h4 class="fw-bold text-white mb-1"><i class="bi bi-stars text-warning"></i> Welcome back, <?= htmlspecialchars($_SESSION['fullname']); ?>!</h4>
                <p class="text-muted small mb-0">Pumili ng inumin o pagkain. May 10% GCash deposit policy para sa bawat order.</p>
            </div>
            <div class="d-flex gap-2">
                <span class="badge bg-dark border border-warning text-warning px-3 py-2 rounded-pill"><i class="bi bi-shield-check"></i> Anti-Bogus Protected</span>
            </div>
        </div>

        <?php if (!empty($message)): ?>
            <div class="alert alert-success alert-dismissible fade show border-0 rounded-4 shadow-sm mb-4" role="alert" style="background: rgba(16, 185, 129, 0.2); color: #34d399; border: 1px solid rgba(52, 211, 153, 0.3);">
                <i class="bi bi-check-circle-fill me-2"></i> <?= $message; ?>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <!-- Left Side: Product Menu -->
            <div class="col-lg-8">
                
                <!-- Category Filters -->
                <div class="d-flex gap-2 overflow-auto pb-3 mb-2" id="categoryTabs">
                    <button class="category-pill active" onclick="filterCategory('all', this)"><i class="bi bi-grid-fill"></i> Lahat</button>
                    <button class="category-pill" onclick="filterCategory('Milk Tea', this)">🧋 Milk Tea</button>
                    <button class="category-pill" onclick="filterCategory('Fruit Tea', this)">🍹 Fruit Tea</button>
                    <button class="category-pill" onclick="filterCategory('Iced Coffee', this)">☕ Iced Coffee</button>
                    <button class="category-pill" onclick="filterCategory('Frappe', this)">🥤 Frappe</button>
                    <button class="category-pill" onclick="filterCategory('Hot Cups', this)">☕ Hot Cups</button>
                    <button class="category-pill" onclick="filterCategory('Snacks', this)">🍟 Snacks</button>
                </div>

                <!-- Products Grid -->
                <div class="row g-3" id="productsGrid">
                    <?php foreach ($products as $p): ?>
                        <div class="col-6 col-md-4 product-item" data-category="<?= htmlspecialchars($p['category']); ?>">
                            <div class="card product-card h-100">
                                <div class="img-wrapper">
                                    <img src="<?= htmlspecialchars($p['image']); ?>" class="product-img" alt="<?= htmlspecialchars($p['name']); ?>">
                                </div>
                                <div class="card-body d-flex flex-column justify-content-between p-3">
                                    <div>
                                        <span class="badge bg-dark text-warning border border-warning border-opacity-25 rounded-pill mb-1" style="font-size: 0.65rem;"><?= htmlspecialchars($p['category']); ?></span>
                                        <h6 class="fw-bold mt-1 mb-1 text-white text-truncate" style="font-size: 0.9rem;"><?= htmlspecialchars($p['name']); ?></h6>
                                    </div>
                                    <div class="mt-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-warning fw-extrabold fs-6">₱<?= number_format($p['price'], 2); ?></span>
                                            <span class="text-muted small" style="font-size: 0.7rem;">Base</span>
                                        </div>
                                        <button class="btn btn-amber btn-sm w-100 py-1.5" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#orderModal" 
                                                onclick="selectProduct(<?= $p['id']; ?>, '<?= addslashes($p['name']); ?>', <?= $p['price']; ?>)">
                                            <i class="bi bi-cart-plus me-1"></i> Add to Cart
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Right Side: Sticky Cart Box -->
            <div class="col-lg-4">
                <div class="card cart-box p-4 text-white sticky-top" style="top: 90px;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold text-warning mb-0"><i class="bi bi-bag-check-fill me-2"></i> Shopping Cart</h5>
                        <span class="badge bg-warning text-dark rounded-pill fw-bold"><?= count($_SESSION['cart']); ?> items</span>
                    </div>
                    
                    <?php if (empty($_SESSION['cart'])): ?>
                        <div class="text-center my-5 py-4">
                            <i class="bi bi-cart-x text-muted display-4"></i>
                            <p class="text-muted small mt-2">Wala pang laman ang iyong cart.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive mb-3 pe-1" style="max-height: 220px; overflow-y: auto;">
                            <table class="table table-dark table-sm align-middle mb-0" style="font-size: 0.85rem; background: transparent;">
                                <tbody>
                                    <?php 
                                    $cartTotal = 0;
                                    foreach ($_SESSION['cart'] as $index => $item): 
                                        $cartTotal += $item['total'];
                                    ?>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                            <td class="py-2">
                                                <strong class="text-white"><?= $item['quantity']; ?>x <?= htmlspecialchars($item['name']); ?></strong><br>
                                                <span class="text-muted" style="font-size: 0.75rem;"><?= $item['size']; ?> • <?= $item['sugar']; ?>% Sugar</span>
                                            </td>
                                            <td class="text-end py-2 text-warning fw-semibold">₱<?= number_format($item['total'], 2); ?></td>
                                            <td class="text-end py-2">
                                                <a href="index.php?remove=<?= $index; ?>" class="text-danger opacity-75 ms-2"><i class="bi bi-trash"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <?php $requiredDeposit = $cartTotal * 0.10; ?>

                        <div class="border-top border-secondary border-opacity-25 pt-3 mb-3">
                            <div class="d-flex justify-content-between text-muted small">
                                <span>Subtotal:</span>
                                <span>₱<?= number_format($cartTotal, 2); ?></span>
                            </div>
                            <div class="d-flex justify-content-between fs-6 fw-bold text-warning mt-1">
                                <span>10% Required Deposit:</span>
                                <span>₱<?= number_format($requiredDeposit, 2); ?></span>
                            </div>
                        </div>

                        <div class="deposit-card mb-3">
                            <div class="d-flex align-items-center gap-2 mb-1 text-warning fw-bold small">
                                <i class="bi bi-qr-code-scan"></i> GCash Payment Info
                            </div>
                            <p class="text-muted mb-0" style="font-size: 0.78rem;">
                                Send 10% deposit (<strong>₱<?= number_format($requiredDeposit, 2); ?></strong>) to GCash:<br>
                                <span class="text-white fw-bold fs-6">0912-345-6789</span> (GRAVITY POS)
                            </p>
                        </div>

                        <form method="POST">
                            <input type="hidden" name="action" value="checkout">
                            <div class="mb-2">
                                <label class="form-label text-muted small mb-1">Customer Name</label>
                                <input type="text" class="form-control glass-input form-control-sm" name="customer_name" required value="<?= htmlspecialchars($_SESSION['fullname']); ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-warning small mb-1">GCash Reference No.</label>
                                <input type="text" class="form-control glass-input form-control-sm" name="payment_ref" required placeholder="e.g. 10023456789">
                            </div>
                            <button type="submit" class="btn btn-amber w-100 py-2.5">
                                <i class="bi bi-lock-fill me-1"></i> Submit & Deposit ₱<?= number_format($requiredDeposit, 2); ?>
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para sa Order History at Pag-cancel ng Order -->
    <div class="modal fade" id="ordersModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content text-white" style="background: #18110b; border: 1px solid var(--card-border); border-radius: 20px;">
                <div class="modal-header border-bottom border-secondary border-opacity-25 px-4">
                    <h5 class="modal-title fw-bold text-warning d-flex align-items-center gap-2">
                        <i class="bi bi-clock-history"></i> My Orders & Status Tracker
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <?php if (empty($myOrders)): ?>
                        <p class="text-muted text-center my-4">Wala ka pa ring naisusumiteng order.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-dark table-hover align-middle mb-0" style="font-size: 0.85rem; background: transparent;">
                                <thead>
                                    <tr class="text-warning">
                                        <th>#ID</th>
                                        <th>Details</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Oras</th>
                                        <th>Aksyon</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($myOrders as $ord): ?>
                                        <tr>
                                            <td><?= $ord['id']; ?></td>
                                            <td style="max-width: 200px;"><?= htmlspecialchars($ord['order_details']); ?></td>
                                            <td class="text-warning fw-bold">₱<?= number_format($ord['total_price'], 2); ?></td>
                                            <td>
                                                <?php 
                                                    $stClass = 'bg-secondary';
                                                    if ($ord['status'] === 'Completed') $stClass = 'bg-success';
                                                    elseif ($ord['status'] === 'Preparing') $stClass = 'bg-info text-dark';
                                                    elseif ($ord['status'] === 'Pending Verification') $stClass = 'bg-warning text-dark';
                                                    elseif ($ord['status'] === 'Cancelled') $stClass = 'bg-danger';
                                                ?>
                                                <span class="badge <?= $stClass; ?>"><?= $ord['status']; ?></span>
                                            </td>
                                            <td class="text-muted small"><?= $ord['created_at']; ?></td>
                                            <td>
                                                <?php if ($ord['status'] === 'Pending Verification'): ?>
                                                    <a href="index.php?cancel_order=<?= $ord['id']; ?>" class="btn btn-outline-danger btn-sm py-0 px-2" onclick="return confirm('Sigurado ka bang gusto mong kanselahin ang order na ito?');">
                                                        <i class="bi bi-x-circle"></i> Cancel
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted small font-italic">Locked</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer border-top border-secondary border-opacity-25 px-4">
                    <button type="button" class="btn btn-outline-light btn-sm rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Configure Item Modal -->
    <div class="modal fade" id="orderModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-white" style="background: #18110b; border: 1px solid var(--card-border); border-radius: 20px;">
                <form method="POST">
                    <div class="modal-header border-bottom border-secondary border-opacity-25 px-4">
                        <h5 class="modal-title fw-bold text-warning d-flex align-items-center gap-2" id="modalProductName">
                            <i class="bi bi-sliders"></i> Configure Item
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body px-4 py-3">
                        <input type="hidden" name="action" value="add_to_cart">
                        <input type="hidden" name="product_id" id="modalProductId">

                        <div class="mb-3">
                            <label class="form-label text-muted small">Cup Size</label>
                            <select class="form-select glass-input" name="size">
                                <option value="16oz">16oz (Standard)</option>
                                <option value="22oz">22oz (+₱10.00)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small">Sugar Level</label>
                            <select class="form-select glass-input" name="sugar">
                                <option value="100">100% Normal Sugar</option>
                                <option value="75">75% Less Sugar</option>
                                <option value="50">50% Half Sugar</option>
                                <option value="25">25% Low Sugar</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small">Quantity</label>
                            <input type="number" class="form-control glass-input" name="quantity" value="1" min="1" max="20" required>
                        </div>
                    </div>
                    <div class="modal-footer border-top border-secondary border-opacity-25 px-4">
                        <button type="button" class="btn btn-outline-light btn-sm rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-amber btn-sm rounded-pill px-4">Add to Cart</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function selectProduct(id, name, price) {
            document.getElementById('modalProductId').value = id;
            document.getElementById('modalProductName').innerHTML = '<i class="bi bi-cup-straw text-warning"></i> Add ' + name;
        }

        function filterCategory(category, btnElement) {
            const navLinks = document.querySelectorAll('#categoryTabs .category-pill');
            navLinks.forEach(link => link.classList.remove('active'));
            btnElement.classList.add('active');

            const items = document.querySelectorAll('#productsGrid .product-item');
            items.forEach(item => {
                const itemCategory = item.getAttribute('data-category');
                if (category === 'all' || itemCategory === category) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>