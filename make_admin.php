<?php
// make_admin.php - Automatic Admin Role Upgrader
try {
    $db = new PDO('sqlite:gravity_pos.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Ilagay dito ang eksaktong username na ginamit mo sa pag-login/register
    $target_user = 'admin'; // Pwede mong palitan kung iba ang username mo

    $stmt = $db->prepare("UPDATE users SET role = 'admin' WHERE username = ?");
    $stmt->execute([$target_user]);

    if ($stmt->rowCount() > 0) {
        echo "<h2 style='color: green; font-family: sans-serif;'>✅ Tagumpay! Ang account na '$target_user' ay isa nang ADMIN.</h2>";
        echo "<p><a href='login.php'>Mag-login na ulit dito para makita ang Admin Dashboard</a></p>";
    } else {
        echo "<h2 style='color: red; font-family: sans-serif;'>❌ Hindi makita ang username na '$target_user'. Siguraduhing naka-register na ito muna.</h2>";
    }

} catch (PDOException $e) {
    echo "<h2 style='color: red;'>Error: " . $e->getMessage() . "</h2>";
}
?>