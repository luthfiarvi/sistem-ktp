<?php

function ensure_core_schema(mysqli $koneksi){
    $koneksi->query("CREATE TABLE IF NOT EXISTS formulir (
        id INT(11) NOT NULL AUTO_INCREMENT,
        nik VARCHAR(20) NOT NULL,
        nama VARCHAR(100) NOT NULL,
        tempat_lahir VARCHAR(50) DEFAULT NULL,
        tanggal_lahir DATE DEFAULT NULL,
        jenis_kelamin ENUM('L','P') DEFAULT 'L',
        alamat TEXT DEFAULT NULL,
        rt VARCHAR(3) DEFAULT NULL,
        rw VARCHAR(3) DEFAULT NULL,
        ttd VARCHAR(255) DEFAULT NULL,
        agama VARCHAR(50) DEFAULT NULL,
        pekerjaan VARCHAR(100) DEFAULT NULL,
        status VARCHAR(50) DEFAULT NULL,
        foto VARCHAR(255) DEFAULT NULL,
        status_permohonan VARCHAR(32) DEFAULT 'Diajukan',
        PRIMARY KEY (id),
        UNIQUE KEY nik (nik)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

    $koneksi->query("CREATE TABLE IF NOT EXISTS kelurahan (
        id_kelurahan INT(11) NOT NULL AUTO_INCREMENT,
        nama_kelurahan VARCHAR(100) DEFAULT 'Munjul',
        kecamatan VARCHAR(100) DEFAULT 'Cipayung',
        kota VARCHAR(100) DEFAULT 'Jakarta Timur',
        provinsi VARCHAR(100) DEFAULT 'DKI Jakarta',
        kode_pos VARCHAR(10) DEFAULT '13850',
        luas_wilayah DECIMAL(5,2) DEFAULT 4.85,
        jumlah_rw INT(11) DEFAULT 9,
        jumlah_rt INT(11) DEFAULT 78,
        kepala_kelurahan VARCHAR(100) DEFAULT NULL,
        sejarah TEXT DEFAULT NULL,
        lokasi VARCHAR(255) DEFAULT NULL,
        foto_url VARCHAR(255) DEFAULT NULL,
        PRIMARY KEY (id_kelurahan)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

    $koneksi->query("INSERT INTO kelurahan
        (id_kelurahan, nama_kelurahan, kecamatan, kota, provinsi, kode_pos, luas_wilayah, jumlah_rw, jumlah_rt, kepala_kelurahan, sejarah, lokasi, foto_url)
        SELECT 1, 'Munjul', 'Cipayung', 'Jakarta Timur', 'DKI Jakarta', '13850', 4.85, 9, 78,
               'Tari Djutari, S.E., M.Si.',
               'Kelurahan Munjul dahulu bagian dari Desa Setu yang kemudian dimekarkan untuk meningkatkan pelayanan masyarakat. Wilayah ini dikenal dengan karakter semi-perkotaan, memiliki area hijau luas dan komunitas aktif dalam kegiatan sosial serta keagamaan.',
               'https://timur.jakarta.go.id/kelurahan/munjul', 'https://timur.jakarta.go.id/storage/kelurahan/9HzDjPtxOOxdta0xEPA790hkwNS1SFCW41wXpbn8.jpeg'
        WHERE NOT EXISTS (SELECT 1 FROM kelurahan LIMIT 1)");

    $koneksi->query("CREATE TABLE IF NOT EXISTS kk (
        id_kk INT(11) NOT NULL AUTO_INCREMENT,
        id_formulir INT(11) DEFAULT NULL,
        file_kk VARCHAR(255) DEFAULT NULL,
        PRIMARY KEY (id_kk),
        KEY id_formulir (id_formulir),
        CONSTRAINT kk_ibfk_1 FOREIGN KEY (id_formulir) REFERENCES formulir(id) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

    $koneksi->query("CREATE TABLE IF NOT EXISTS akte (
        id_akte INT(11) NOT NULL AUTO_INCREMENT,
        id_formulir INT(11) DEFAULT NULL,
        file_akte VARCHAR(255) DEFAULT NULL,
        PRIMARY KEY (id_akte),
        KEY id_formulir (id_formulir),
        CONSTRAINT akte_ibfk_1 FOREIGN KEY (id_formulir) REFERENCES formulir(id) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

    $koneksi->query("CREATE OR REPLACE VIEW view_ktp_lengkap AS
        SELECT
            f.id AS id_formulir,
            f.nik AS nik,
            f.nama AS nama,
            f.tempat_lahir AS tempat_lahir,
            f.tanggal_lahir AS tanggal_lahir,
            f.jenis_kelamin AS jenis_kelamin,
            f.alamat AS alamat,
            f.rt AS rt,
            f.rw AS rw,
            f.ttd AS ttd,
            f.agama AS agama,
            f.pekerjaan AS pekerjaan,
            f.status AS status,
            f.foto AS foto,
            k.file_kk AS file_kk,
            a.file_akte AS file_akte,
            kel.nama_kelurahan AS nama_kelurahan,
            kel.kecamatan AS kecamatan,
            kel.kota AS kota,
            kel.provinsi AS provinsi,
            kel.kode_pos AS kode_pos,
            kel.kepala_kelurahan AS kepala_kelurahan,
            kel.sejarah AS sejarah,
            kel.lokasi AS lokasi,
            f.status_permohonan AS status_permohonan
        FROM formulir f
        LEFT JOIN kk k ON f.id = k.id_formulir
        LEFT JOIN akte a ON f.id = a.id_formulir
        LEFT JOIN kelurahan kel ON 1 = 1");
}

?>
