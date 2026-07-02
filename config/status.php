<?php
// Konfigurasi dan utilitas status permohonan KTP

function ktp_status_stages(){
    return ['Diajukan','Diverifikasi','Disetujui','Dicetak','Selesai'];
}

function ktp_status_next($current){
    $list = ktp_status_stages();
    $idx = array_search($current, $list, true);
    if($idx === false) return $list[0];
    return $list[min($idx+1, count($list)-1)];
}

function ensure_status_schema(mysqli $koneksi){
    // Tambah kolom status_permohonan jika belum ada
    $res = $koneksi->query("SHOW COLUMNS FROM formulir LIKE 'status_permohonan'");
    if($res && $res->num_rows === 0){
        $koneksi->query("ALTER TABLE formulir ADD COLUMN status_permohonan VARCHAR(32) DEFAULT 'Diajukan'");
    }
    if($res) $res->close();

    // Tambah kolom RT/RW jika belum ada (untuk selaras dengan output KTP)
    $res = $koneksi->query("SHOW COLUMNS FROM formulir LIKE 'rt'");
    if($res && $res->num_rows === 0){
        $koneksi->query("ALTER TABLE formulir ADD COLUMN rt VARCHAR(3) NULL AFTER alamat");
    }
    if($res) $res->close();
    $res = $koneksi->query("SHOW COLUMNS FROM formulir LIKE 'rw'");
    if($res && $res->num_rows === 0){
        $koneksi->query("ALTER TABLE formulir ADD COLUMN rw VARCHAR(3) NULL AFTER rt");
    }
    if($res) $res->close();

    // Tambah kolom ttd (file tanda tangan) jika belum ada
    $res = $koneksi->query("SHOW COLUMNS FROM formulir LIKE 'ttd'");
    if($res && $res->num_rows === 0){
        $koneksi->query("ALTER TABLE formulir ADD COLUMN ttd VARCHAR(255) NULL AFTER foto");
    }
    if($res) $res->close();

    // Buat tabel status_history kalau belum ada
    $koneksi->query("CREATE TABLE IF NOT EXISTS status_history (
        id INT AUTO_INCREMENT PRIMARY KEY,
        id_formulir INT NOT NULL,
        status VARCHAR(32) NOT NULL,
        catatan TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_formulir (id_formulir),
        CONSTRAINT fk_status_formulir FOREIGN KEY (id_formulir) REFERENCES formulir(id) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
}

?>
