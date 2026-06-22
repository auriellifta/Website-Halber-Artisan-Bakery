<?php
require '../backend/koneksi.php';
require '../backend/auth.php';
cek_login_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);
    if ($id > 0) {
        $stmt = mysqli_prepare($koneksi, "DELETE FROM ulasan WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

header('Location: dashboard.php?status=deleted');
exit;
?>
