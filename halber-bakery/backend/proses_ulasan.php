<?php

header('Content-Type: application/json');
require 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['sukses' => false, 'pesan' => 'Metode tidak diizinkan.']);
    exit;
}

$action = $_POST['action'] ?? '';
$no_wa  = preg_replace('/\D/', '', $_POST['no_wa'] ?? '');

if (str_starts_with($no_wa, '0')) {
    $no_wa = '62' . substr($no_wa, 1);
}

// =====================================================
// STEP 1 — Cek apakah no_wa pernah memesan
// =====================================================
if ($action === 'cek') {
    if (strlen($no_wa) < 9) {
        echo json_encode(['sukses' => false, 'pesan' => 'Nomor WhatsApp tidak valid.']);
        exit;
    }

    $stmt = mysqli_prepare($koneksi,
        "SELECT nama FROM pesanan WHERE no_wa = ? LIMIT 1"
    );
    mysqli_stmt_bind_param($stmt, 's', $no_wa);
    mysqli_stmt_execute($stmt);
    $baris = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);

    if ($baris) {
        echo json_encode([
            'sukses' => true,
            'pesan'  => 'Nomor terverifikasi.',
            'nama'   => $baris['nama'],   // prefill nama di form ulasan
        ]);
    } else {
        echo json_encode([
            'sukses' => false,
            'pesan'  => 'Nomor WhatsApp ini belum terdaftar sebagai pembeli. Silakan pesan terlebih dahulu.',
        ]);
    }
    exit;
}

// =====================================================
// STEP 2 — Simpan ulasan (setelah no_wa terverifikasi)
// =====================================================
if ($action === 'kirim') {
    $nama     = trim($_POST['nama']     ?? '');
    $rating   = intval($_POST['rating'] ?? 0);
    $komentar = trim($_POST['komentar'] ?? '');

    $errors = [];
    if (strlen($no_wa) < 9)   $errors[] = 'Nomor WhatsApp tidak valid.';
    if ($nama === '')          $errors[] = 'Nama wajib diisi.';
    if ($rating < 1 || $rating > 5) $errors[] = 'Rating harus antara 1–5.';
    if ($komentar === '')      $errors[] = 'Ulasan tidak boleh kosong.';

    if ($errors) {
        echo json_encode(['sukses' => false, 'pesan' => implode(' ', $errors)]);
        exit;
    }

    // Pastikan no_wa masih ada di pesanan (double-check, anti-bypass)
    $cek = mysqli_prepare($koneksi, "SELECT id FROM pesanan WHERE no_wa = ? LIMIT 1");
    mysqli_stmt_bind_param($cek, 's', $no_wa);
    mysqli_stmt_execute($cek);
    $ada = mysqli_fetch_assoc(mysqli_stmt_get_result($cek));
    mysqli_stmt_close($cek);

    if (!$ada) {
        echo json_encode(['sukses' => false, 'pesan' => 'Verifikasi gagal. Nomor tidak terdaftar.']);
        exit;
    }

    // Simpan ulasan
    $stmt = mysqli_prepare($koneksi,
        "INSERT INTO ulasan (no_wa, nama, rating, komentar) VALUES (?, ?, ?, ?)"
    );
    mysqli_stmt_bind_param($stmt, 'ssis', $no_wa, $nama, $rating, $komentar);
    $berhasil = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    if ($berhasil) {
        echo json_encode(['sukses' => true, 'pesan' => 'Ulasan berhasil dikirim! Terima kasih.']);
    } else {
        echo json_encode(['sukses' => false, 'pesan' => 'Gagal menyimpan ulasan. Coba lagi.']);
    }
    exit;
}

echo json_encode(['sukses' => false, 'pesan' => 'Aksi tidak dikenali.']);
?>
