<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$file_koneksi = '../backend/koneksi.php';

// 1. DETEKSI FILE KONEKSI
if (!file_exists($file_koneksi)) {
    die("<div style='background:#fff5f5; padding:24px; border:2px solid #c53030; border-radius:12px; margin:40px auto; max-width:600px; font-family:sans-serif; text-align:center;'>
            <h2 style='color:#c53030; margin-top:0;'>🚨 ERROR: File Koneksi Tidak Ditemukan!</h2>
            <p style='color:#333; line-height:1.5;'>Sistem tidak bisa menemukan file koneksi di folder <b>backend</b>.</p>
            <p style='color:#333; line-height:1.5;'>Pastikan letak folder Anda seperti ini:<br>
            📂 <b>halber-v3 (2)</b> <br>
            ┣ 📂 <b>frontend</b> ➔ (tempat file ulasan.php ini berada)<br>
            ┗ 📂 <b>backend</b> ➔ (tempat file koneksi.php berada)</p>
         </div>");
}

include $file_koneksi;

// 2. DETEKSI DATABASE & TABEL
$ambil_ulasan = null;
try {
    $ambil_ulasan = mysqli_query($koneksi, "SELECT * FROM ulasan ORDER BY tanggal DESC");
    if (!$ambil_ulasan) {
        throw new Exception(mysqli_error($koneksi));
    }
} catch (Exception $e) {
    die("<div style='background:#fff5f5; padding:24px; border:2px solid #c53030; border-radius:12px; margin:40px auto; max-width:600px; font-family:sans-serif; text-align:center;'>
            <h2 style='color:#c53030; margin-top:0;'>🚨 ERROR: Database Bermasalah!</h2>
            <p style='color:#333; line-height:1.5;'>Pesan dari sistem: <b>" . $e->getMessage() . "</b></p>
            <p style='color:#333; line-height:1.5;'>Tabel <b>ulasan</b> sepertinya belum ada. Silakan buka phpMyAdmin dan import file SQL Anda terlebih dahulu.</p>
         </div>");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ulasan Pelanggan – Halber Artisan Bakery</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;0,900;1,700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
  <style>
    /* ===== Langkah verifikasi ===== */
    .step-indicator {
      display: flex;
      align-items: center;
      gap: 0;
      margin-bottom: 28px;
    }
    .step-dot {
      width: 28px; height: 28px;
      border-radius: 50%;
      background: var(--cream);
      border: 2px solid var(--cream-dark);
      display: flex; align-items: center; justify-content: center;
      font-size: 12px; font-weight: 700; color: var(--text-mid);
      flex-shrink: 0;
      transition: all 0.3s;
    }
    .step-dot.aktif  { background: var(--gold); border-color: var(--gold); color: white; }
    .step-dot.selesai { background: var(--blue); border-color: var(--blue); color: white; }
    .step-line {
      flex: 1; height: 2px;
      background: var(--cream-dark);
      transition: background 0.3s;
    }
    .step-line.selesai { background: var(--blue); }
    .step-label {
      font-size: 11px; color: var(--text-mid);
      text-align: center; margin-top: 4px; display: block;
    }
    .step-wrap { display: flex; flex-direction: column; align-items: center; }

    .form-step { display: none; }
    .form-step.aktif { display: block; }

    .wa-hint {
      font-size: 12.5px; color: var(--text-mid);
      margin-top: 6px; line-height: 1.5;
    }
    .wa-hint a { color: var(--blue); }

    .verif-info {
      background: #f0fdf4; 
      border: 1px solid #bbf7d0; 
      border-radius: 12px;
      padding: 16px;
      margin-bottom: 24px;
      display: none;
    }
    .verif-info.show {
      display: flex;
      align-items: flex-start;
      gap: 14px;
    }
    .verif-icon {
      flex-shrink: 0;
      width: 28px;
      height: 28px;
      background: #22c55e;
      color: white;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .verif-icon svg {
      width: 16px;
      height: 16px;
    }
    .verif-text-wrap {
      display: flex;
      flex-direction: column;
      gap: 4px;
    }
    .verif-title {
      font-size: 14.5px;
      font-weight: 700;
      color: #166534; 
    }
    .verif-desc {
      font-size: 13.5px;
      color: #15803d; 
      line-height: 1.5;
    }

    .alert-ulasan {
      padding: 14px 18px; border-radius: 12px;
      font-size: 14px; font-weight: 600;
      margin-bottom: 20px; display: none;
    }
    .alert-ulasan.sukses {
      background: rgba(201,168,76,0.10);
      border: 1px solid rgba(201,168,76,0.4);
      color: #7a5c00;
    }
    .alert-ulasan.error {
      background: #fff5f5; border: 1px solid #fcc; color: #c53030;
    }
    .alert-ulasan.show { display: block; }

    .form-group select {
      width: 100%;
      padding: 12px 16px;
      border: 1.5px solid var(--cream-dark);
      border-radius: 10px;
      font-family: 'DM Sans', sans-serif;
      font-size: 14px;
      color: var(--text-dark);
      background-color: var(--white);
      cursor: pointer;
      -webkit-appearance: none;
      -moz-appearance: none;
      appearance: none;
      background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%234a4a6a' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
      background-repeat: no-repeat;
      background-position: right 14px center;
      background-size: 16px;
      margin-top: 6px;
      box-sizing: border-box;
      transition: border-color 0.3s;
    }
    .form-group select:focus {
      outline: none;
      border-color: var(--gold);
    }

    .form-group {
      margin-bottom: 18px;
    }
    .form-group label {
      font-size: 13.5px;
      font-weight: 600;
      color: var(--blue-dark);
      display: block;
    }
    .form-group input,
    .form-group textarea {
      width: 100%;
      padding: 12px 16px;
      border: 1.5px solid var(--cream-dark);
      border-radius: 10px;
      font-family: 'DM Sans', sans-serif;
      font-size: 14px;
      color: var(--text-dark);
      background-color: var(--white);
      transition: all 0.3s;
      box-sizing: border-box;
      margin-top: 6px;
    }
    .form-group input:focus,
    .form-group textarea:focus {
      outline: none;
      border-color: var(--blue);
      box-shadow: 0 0 0 3px rgba(26, 63, 160, 0.1);
    }

    .btn-submit {
      background: var(--blue);
      color: var(--white);
      border: none;
      padding: 13px 24px;
      border-radius: 10px;
      font-family: 'DM Sans', sans-serif;
      font-size: 14.5px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      width: 100%;
      margin-top: 8px;
    }
    .btn-submit:hover {
      background: var(--blue-dark);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(26, 63, 160, 0.15);
    }

    .btn-submit-outline {
      background: transparent;
      border: 2px solid var(--blue);
      color: var(--blue);
      padding: 11px 20px;
      border-radius: 10px;
      font-family: 'DM Sans', sans-serif;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      margin-top: 8px;
      transition: all 0.3s;
      width: 100%;
    }
    .btn-submit-outline:hover { 
      background: var(--blue); 
      color: white; 
    }
  </style>
</head>
<body>

<!-- NAVBAR -->
<nav id="navbar" class="scrolled">
  <div class="nav-logo">
    <svg viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M9 24 Q9 32 18 32 Q27 32 27 24" stroke="#f0e8d0" stroke-width="2.5" fill="none" stroke-linecap="round"/>
      <path d="M13 22 Q13 14 18 13 Q23 14 23 22" fill="#f0e8d0"/>
      <path d="M14 10 Q14 6 18 6 Q22 6 22 10" stroke="#f0e8d0" stroke-width="1.5" fill="none"/>
      <line x1="13" y1="5" x2="12" y2="2" stroke="#c9a84c" stroke-width="1.5" stroke-linecap="round"/>
      <line x1="18" y1="4" x2="18" y2="1" stroke="#c9a84c" stroke-width="1.5" stroke-linecap="round"/>
      <line x1="23" y1="5" x2="24" y2="2" stroke="#c9a84c" stroke-width="1.5" stroke-linecap="round"/>
    </svg>
    <div>
      <div class="nav-logo-text"><a href="index.html" style="text-decoration:none;color:inherit">HALBER</a></div>
      <span class="nav-logo-sub">Artisan Bakery</span>
    </div>
  </div>
  <div class="nav-links">
    <a href="index.html">Beranda</a>
    <a href="menu.html">Menu</a>
    <a href="ulasan.php" class="active">Ulasan</a>
    <a href="tentang.html">Tentang</a>
    <a href="lokasi.html">Lokasi</a>
  </div>
  <a href="menu.html" class="nav-order-btn">Pesan Sekarang</a>
  <button class="hamburger" id="hamburger" onclick="toggleMobileMenu()">
    <span></span><span></span><span></span>
  </button>
</nav>

<div class="mobile-menu" id="mobileMenu">
  <a href="index.html" onclick="toggleMobileMenu()">Beranda</a>
  <a href="menu.html" onclick="toggleMobileMenu()">Menu</a>
  <a href="ulasan.php" onclick="toggleMobileMenu()">Ulasan</a>
  <a href="tentang.html" onclick="toggleMobileMenu()">Tentang</a>
  <a href="lokasi.html" onclick="toggleMobileMenu()">Lokasi</a>
  <a href="menu.html" class="mobile-order-btn" onclick="toggleMobileMenu()">Pesan Sekarang</a>
</div>

<!-- PAGE HEADER -->
<div class="page-header">
  <div class="page-header-content">
    <div class="hero-badge">Testimoni</div>
    <h1 class="page-title">Ulasan <span>Pelanggan</span></h1>
    <p>Bagikan pengalaman berharga Anda bersama kelembutan produk premium kami.</p>
  </div>
</div>

<!-- MAIN CONTENT -->
<div class="review-container">

  <div class="alert-ulasan" id="alertUlasan"></div>

  <!-- FORM ULASAN dengan 2 langkah -->
  <div class="ulasan-form-box">
    <h2>Tulis Ulasan Anda</h2>
    <p>Ulasan hanya dapat diberikan oleh pembeli yang telah memesan. Masukkan nomor WhatsApp yang digunakan saat memesan untuk verifikasi.</p>

    <!-- Step indicator -->
    <div class="step-indicator" style="margin-top:20px;">
      <div class="step-wrap">
        <div class="step-dot aktif" id="dot1">1</div>
        <span class="step-label">Verifikasi</span>
      </div>
      <div class="step-line" id="line1"></div>
      <div class="step-wrap">
        <div class="step-dot" id="dot2">2</div>
        <span class="step-label">Tulis Ulasan</span>
      </div>
    </div>

    <!-- STEP 1: masukkan no_wa -->
    <div class="form-step aktif" id="step1">
      <div class="form-group">
        <label for="input_wa">Nomor WhatsApp Anda *</label>
        <input type="tel" id="input_wa" placeholder="Contoh: 08123456789" maxlength="16">
        <div class="wa-hint">
          Gunakan nomor yang sama saat memesan.
          Belum pernah memesan? <a href="menu.html">Pesan dulu di sini</a>
        </div>
      </div>
      <button class="btn-submit" id="btnVerif" onclick="verifikasiWA()">Verifikasi Nomor</button>
    </div>

    <!-- STEP 2: form ulasan -->
    <div class="form-step" id="step2">
      
      <!-- DESAIN KARTU VERIFIKASI BARU -->
      <div class="verif-info" id="verifInfo">
        <div class="verif-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"></polyline>
          </svg>
        </div>
        <div class="verif-text-wrap">
          <div class="verif-title">Verifikasi Berhasil</div>
          <div class="verif-desc" id="verifNama">Nomor WhatsApp Anda telah terdaftar sebagai pembeli.</div>
        </div>
      </div>

      <form id="formUlasan">
        <input type="hidden" id="hidden_wa" name="no_wa">
        <div class="form-group">
          <label for="nama">Nama Lengkap *</label>
          <input type="text" id="nama" name="nama" placeholder="Contoh: Siti Rahayu" required maxlength="100">
        </div>
        <div class="form-group">
          <label for="rating">Berikan Nilai Bintang *</label>
          <select id="rating" name="rating" required>
            <option value="5">⭐⭐⭐⭐⭐ Luar Biasa</option>
            <option value="4">⭐⭐⭐⭐ Sangat Bagus</option>
            <option value="3">⭐⭐⭐ Cukup Baik</option>
            <option value="2">⭐⭐ Kurang Memuaskan</option>
            <option value="1">⭐ Mengecewakan</option>
          </select>
        </div>
        <div class="form-group">
          <label for="komentar">Ulasan Anda *</label>
          <textarea id="komentar" name="komentar" rows="4" placeholder="Ceritakan pengalaman Anda menikmati produk Halber..." required></textarea>
        </div>
        <div style="display:flex;gap:12px;flex-wrap:wrap;">
          <button type="submit" class="btn-submit" style="flex:1; min-width: 150px;">Kirim Ulasan</button>
          <button type="button" class="btn-submit-outline" onclick="resetForm()" style="flex:1; min-width: 150px;">Ganti Nomor</button>
        </div>
      </form>
    </div>
  </div>

  <!-- DAFTAR ULASAN -->
  <h3 class="ulasan-list-title">Ulasan Terbaru Pembeli</h3>

  <?php if ($ambil_ulasan && mysqli_num_rows($ambil_ulasan) > 0): ?>
    <?php while ($row = mysqli_fetch_assoc($ambil_ulasan)): ?>
      <?php $bintang = intval($row['rating']); ?>
      <div class="ulasan-card">
        <div class="ulasan-star">
          <?php echo str_repeat('&#9733;', $bintang) . str_repeat('&#9734;', 5 - $bintang); ?>
        </div>
        <p class="ulasan-text">"<?php echo nl2br(htmlspecialchars($row['komentar'])); ?>"</p>
        <div class="ulasan-name">&mdash; <?php echo htmlspecialchars($row['nama']); ?></div>
        <div class="ulasan-date">Diposting: <?php echo date('d M Y, H:i', strtotime($row['tanggal'])); ?> WIB</div>
      </div>
    <?php endwhile; ?>
  <?php else: ?>
    <div class="no-ulasan">Belum ada ulasan. Jadilah yang pertama!</div>
  <?php endif; ?>

</div>

<!-- FOOTER -->
<footer class="footer-v1">
    <div class="footer-v1-container">
        <!-- Kolom 1: Brand -->
        <div class="footer-v1-brand">
            <div class="brand-name">HALBER</div>
            <p>Menyajikan roti artisan buatan tangan dengan bahan berkualitas premium. Dipanggang dengan cinta setiap hari untuk menemani momen spesial Anda.</p>
            <div class="footer-v1-social">
                <a href="https://www.instagram.com/halber.bakery?igsh=eXBrZzNwNzhwYWN6" aria-label="Instagram">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
                </a>
                <a href="javascript:void(0)" aria-label="Facebook">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>
                </a>
            </div>
        </div>

        <!-- Kolom 2: Tautan Cepat -->
        <div class="footer-v1-col footer-col-middle">
            <h4>Jelajahi</h4>
            <ul class="footer-v1-links">
                <li><a href="index.html">Beranda</a></li>
                <li><a href="menu.html">Menu</a></li>
                <li><a href="ulasan.php">Ulasan</a></li>
                <li><a href="tentang.html">Tentang</a></li>
                <li><a href="lokasi.html">Lokasi</a></li>
            </ul>
        </div>

        <!-- Kolom 3: Kontak & Info -->
        <div class="footer-v1-col">
            <h4>Kunjungi Kami</h4>
            <div class="footer-v1-info">
                <svg viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                <p>Tanjungpinang, Kepulauan Riau<br>Indonesia</p>
            </div>
            <div class="footer-v1-info">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                <p>Setiap Hari 06:00 – 22:00 WIB</p>
            </div>
            <div class="footer-v1-info">
                <svg viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                <p>085156986696</p>
            </div>
        </div>
    </div>

    <div class="footer-v1-bottom">
        <p>Copyright &copy; 2026 Halber Artisan Bakery. All rights reserved.</p>
    </div>
</footer>

<script src="script.js"></script>
<script>
let noWaTerverifikasi = '';

function tampilAlert(tipe, pesan) {
  const el = document.getElementById('alertUlasan');
  el.className = 'alert-ulasan ' + tipe + ' show';
  el.textContent = pesan;
  el.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

async function verifikasiWA() {
  const noWa = document.getElementById('input_wa').value.trim();
  if (!noWa) { tampilAlert('error', '⚠ Nomor WhatsApp wajib diisi.'); return; }

  const btn = document.getElementById('btnVerif');
  btn.disabled = true;
  btn.textContent = 'Memeriksa...';

  const data = new FormData();
  data.append('action', 'cek');
  data.append('no_wa', noWa);

  try {
    const res  = await fetch('../backend/proses_ulasan.php', { method: 'POST', body: data });
    const json = await res.json();

    if (json.sukses) {
      noWaTerverifikasi = noWa;
      document.getElementById('hidden_wa').value = noWa;
      // prefill nama dari data pesanan
      if (json.nama) document.getElementById('nama').value = json.nama;
      
      document.getElementById('verifNama').textContent = json.nama
        ? 'Halo ' + json.nama + ', nomor WhatsApp Anda telah terdaftar sebagai pembeli.'
        : 'Nomor WhatsApp Anda telah terdaftar sebagai pembeli.';

      document.getElementById('step1').classList.remove('aktif');
      document.getElementById('step2').classList.add('aktif');
      document.getElementById('dot1').classList.replace('aktif', 'selesai');
      document.getElementById('line1').classList.add('selesai');
      document.getElementById('dot2').classList.add('aktif');

      document.getElementById('verifInfo').classList.add('show');

      document.getElementById('alertUlasan').classList.remove('show');
    } else {
      tampilAlert('error', '⚠ ' + json.pesan);
    }
  } catch {
    tampilAlert('error', '⚠ Terjadi kesalahan koneksi. Coba lagi.');
  }

  btn.disabled = false;
  btn.textContent = 'Verifikasi Nomor';
}

function resetForm() {
  noWaTerverifikasi = '';
  document.getElementById('step2').classList.remove('aktif');
  document.getElementById('step1').classList.add('aktif');
  document.getElementById('dot1').classList.replace('selesai', 'aktif');
  document.getElementById('line1').classList.remove('selesai');
  document.getElementById('dot2').classList.remove('aktif');
  document.getElementById('formUlasan').reset();
  document.getElementById('alertUlasan').classList.remove('show');
  document.getElementById('verifInfo').classList.remove('show');
}

document.getElementById('formUlasan').addEventListener('submit', async function(e) {
  e.preventDefault();

  const btn = this.querySelector('button[type="submit"]');
  btn.disabled = true;
  btn.textContent = 'Mengirim...';

  const data = new FormData(this);
  data.append('action', 'kirim');

  try {
    const res  = await fetch('../backend/proses_ulasan.php', { method: 'POST', body: data });
    const json = await res.json();

    if (json.sukses) {
      tampilAlert('sukses', '✓ ' + json.pesan);
      this.reset();
      resetForm();
      setTimeout(() => location.reload(), 2000);
    } else {
      tampilAlert('error', '⚠ ' + json.pesan);
    }
  } catch {
    tampilAlert('error', '⚠ Terjadi kesalahan koneksi. Coba lagi.');
  }

  btn.disabled = false;
  btn.textContent = 'Kirim Ulasan';
});

document.getElementById('input_wa').addEventListener('keydown', function(e) {
  if (e.key === 'Enter') { e.preventDefault(); verifikasiWA(); }
});
</script>

</body>
</html>