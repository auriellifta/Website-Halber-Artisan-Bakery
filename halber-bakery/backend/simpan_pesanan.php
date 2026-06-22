<?php

header('Content-Type: application/json');
require 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['sukses' => false, 'pesan' => 'Metode tidak diizinkan.']);
    exit;
}

$nama    = trim($_POST['nama']    ?? '');
$no_wa   = preg_replace('/\D/', '', $_POST['no_wa'] ?? '');
$produk  = trim($_POST['produk']  ?? '');
$catatan = trim($_POST['catatan'] ?? '');

// Validasi
$errors = [];
if ($nama === '')                             $errors[] = 'Nama wajib diisi.';
if (strlen($no_wa) < 9 || strlen($no_wa) > 15) $errors[] = 'Nomor WhatsApp tidak valid.';
if ($produk === '')                           $errors[] = 'Data produk tidak boleh kosong.';

if ($errors) {
    echo json_encode(['sukses' => false, 'pesan' => implode(' ', $errors)]);
    exit;
}

// Normalise: 08xx → 628xx
if (str_starts_with($no_wa, '0')) {
    $no_wa = '62' . substr($no_wa, 1);
}

// Simpan ke tabel pesanan
$stmt = mysqli_prepare($koneksi,
    "INSERT INTO pesanan (nama, no_wa, produk, catatan) VALUES (?, ?, ?, ?)"
);
mysqli_stmt_bind_param($stmt, 'ssss', $nama, $no_wa, $produk, $catatan);
$berhasil = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

if ($berhasil) {
    echo json_encode(['sukses' => true, 'pesan' => 'Pesanan berhasil dicatat.']);
} else {
    echo json_encode(['sukses' => false, 'pesan' => 'Gagal menyimpan ke database.']);
}
