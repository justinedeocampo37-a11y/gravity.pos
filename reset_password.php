<?php
// Ilagay ito pansamantala sa iyong live server para palitan ang admin password
try {
    $db = new PDO('sqlite:gravity_pos.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $username = 'admin'; 
    $new_password = 'ang_gusto_mong_bagong_password'; // Palitan nito
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    $stmt = $db->prepare("UPDATE users SET password = ? WHERE username = ?");
    $stmt->execute([$hashed_password, $username]);

    if ($stmt->rowCount() > 0) {
        echo "Tagumpay! Na-update na ang password ng admin.";
    } else {
        echo "Hindi nakita ang username.";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>