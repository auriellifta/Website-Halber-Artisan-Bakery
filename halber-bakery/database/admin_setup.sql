CREATE TABLE IF NOT EXISTS admin (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(100) NOT NULL,
  nama_lengkap VARCHAR(100) DEFAULT NULL,
  dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Akun admin default:
--   username : admin
--   password  : admin123
INSERT INTO admin (username, password, nama_lengkap)
VALUES ('admin', 'admin123', 'Administrator Halber')
ON DUPLICATE KEY UPDATE username = username;
