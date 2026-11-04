<?php
// update_images.php - Automatic image path updater for gravity_pos.db
try {
    $db = new PDO('sqlite:gravity_pos.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Listahan ng mga produkto at ang kanilang tamang local image filename
    $updates = [
        'Avocado Frappe'          => 'images/avocado frappe.jpg',
        'Strawberry Vanilla Frappe' => 'images/strawberry vanilla frappe.jpg',
        'Cheese Fries (G-Snacks)' => 'images/cheese fries.jpg',
        'Chicken Nuggets'         => 'images/crispy chicken nuggets.jpg',
        'Milky Donuts'            => 'images/milky donuts.jpg',
        'G-Cookies'               => 'images/g cookies.jpg',
        'Caffe Caramel'           => 'images/caffe caramel.jpg',
        'Caffe Fudge'             => 'images/caffe fudge.jpg',
        'Caffe Hazelnut Latte'    => 'images/caffe hazelnut latte.jpg',
        'Caffe Macchiato'         => 'images/caffe macchiato.jpg',
        'Caffe Mocha'             => 'images/caffe mocha.jpg',
        'Caffe Vanilla'           => 'images/caffe vanilla.jpg',
        'Chocolate Frappe'        => 'images/chocolate frappe.jpg',
        'Coffee Jelly Frappe'     => 'images/coffee jelly frappe.jpg',
        'Cookies and Cream Frappe'=> 'images/cookies and cream frappe.jpg',
        'Hot Matcha'              => 'images/hot matcha.jpg',
        'Hot Mocha'               => 'images/hot mocha.jpg',
        'Java Chips Frappe'       => 'images/java chips frappe.jpg',
        'Lemon Fruit Tea'         => 'images/lemon fruit tea.jpg',
        'Lychee Fruit Tea'        => 'images/lychee fruit tea.jpg',
        'Mango Fruit Tea'         => 'images/mango fruit tea.jpg',
        'Matcha Frappe'           => 'images/matcha frappe.jpg',
        'Milky Donuts'            => 'images/milky donuts.jpg',
        'Strawberry Frappe'       => 'images/strawberry frappe.jpg',
        'Taro Frappe'             => 'images/taro frappe.jpg',
        'Vanilla Coffee Frappe'   => 'images/vanilla coffee frappe.jpg',
        'Wintermelon Fruit Tea'   => 'images/wintermelon fruit tea.jpg',
        'Cheese fries (G-Snacks)  => 'images/fries.jpg'
        ];

    $stmt = $db->prepare("UPDATE products SET image = :image WHERE name = :name");
    
    $count = 0;
    foreach ($updates as $name => $image) {
        $stmt->execute([':image' => $image, ':name' => $name]);
        $count++;
    }

    echo "<h2 style='color: green; font-family: sans-serif;'>✅ Matagumpay na na-update ang $count na produkto sa database!</h2>";
    echo "<p><a href='index.php'>Pumunta na sa POS Menu (index.php)</a></p>";

} catch (PDOException $e) {
    echo "<h2 style='color: red;'>Error: " . $e->getMessage() . "</h2>";
}
?>