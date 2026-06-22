<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function cek_login_admin() {
    if (!isset($_SESSION['admin_id'])) {
        header('Location: login.php');
        exit;
    }
}
?>
