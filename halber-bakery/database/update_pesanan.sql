-- 1. Tabel pesanan (form order yang dikirim ke WA)
CREATE TABLE IF NOT EXISTS pesanan (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  nama          VARCHAR(100) NOT NULL,
  no_wa         VARCHAR(20)  NOT NULL,
  produk        TEXT         NOT NULL,
  catatan       TEXT         DEFAULT NULL,
  tanggal_pesan TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
  status        ENUM('pending','diproses','selesai') NOT NULL DEFAULT 'pending'
);

-- 2. Tambahkan kolom no_wa ke tabel ulasan (jika belum ada)
--    no_wa NULL agar data ulasan lama tetap valid
ALTER TABLE ulasan
  ADD COLUMN IF NOT EXISTS no_wa VARCHAR(20) DEFAULT NULL AFTER nama;

-- Index untuk mempercepat pengecekan no_wa saat submit ulasan
CREATE INDEX IF NOT EXISTS idx_pesanan_no_wa ON pesanan(no_wa);
