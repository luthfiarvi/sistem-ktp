<?php
require_once __DIR__.'/config/koneksi.php';
$r = $koneksi->query("SELECT * FROM kelurahan LIMIT 1")->fetch_assoc();
// Siapkan URL foto kelurahan (dari DB kolom `foto_url` jika ada, atau query ?foto=)
$fotoUrl = null;
if (is_array($r)) {
    $fotoUrl = $r['foto_url'] ?? null; // aman jika kolom belum ada
}
// Override via parameter URL jika diberikan (contoh: kelurahan.php?foto=https://... atau data:image/...)
if (isset($_GET['foto']) && is_string($_GET['foto'])) {
    $candidate = trim($_GET['foto']);
    if ($candidate !== '') {
        if (preg_match('/^data:image\/(png|jpe?g|webp|gif);base64,/i', $candidate)) {
            $fotoUrl = $candidate;
        } elseif (filter_var($candidate, FILTER_VALIDATE_URL)) {
            $scheme = parse_url($candidate, PHP_URL_SCHEME);
            if (in_array(strtolower((string)$scheme), ['http','https'], true)) {
                $fotoUrl = $candidate;
            }
        }
    }
}
if (empty($fotoUrl)) {
    $localPath = __DIR__.'/assets/img/kelurahan-munjul.jpg';
    if (is_file($localPath)) {
        $fotoUrl = 'assets/img/kelurahan-munjul.jpg';
    }
}
if (empty($fotoUrl)) {
    $ph = __DIR__.'/assets/img/placeholder.svg';
    if (is_file($ph)) {
        $fotoUrl = 'assets/img/placeholder.svg';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Profil Kelurahan Munjul</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<nav class="site-header">
  <div class="inner">
    <a class="brand" href="index.php">
      <img src="https://timur.jakarta.go.id/frontend3/images24/jt.png" alt="logo">
      <span class="title">KELURAHAN <span class="accent">MUNJUL</span></span>
    </a>
    <div class="mainnav">
      <a class="nav-link" href="index.php">Beranda</a>
      <a class="nav-link active" href="kelurahan.php">Profil</a>
      <a class="nav-link" href="formulir.php">Input</a>
      <a class="nav-link" href="lihat.php">Daftar</a>
    </div>
  </div>
</nav>

<?php $bg = !empty($fotoUrl) ? $fotoUrl : 'assets/img/kelurahan-munjul.jpg'; ?>
<section class="hero-kelurahan" style="background-image:url('<?=htmlspecialchars($bg)?>')">
  <div class="container">
    <div class="breadcrumbs"><a href="index.php">Beranda</a> / <span>Kelurahan Munjul</span></div>
    <h1 class="title">Profil Kelurahan Munjul</h1>
    <p class="subtitle">Informasi singkat, data wilayah, sejarah, dan kontak Kelurahan Munjul, Kecamatan Cipayung, Jakarta Timur.</p>
  </div>
</section>

<div class="container">
  <div class="card">
    <h2 class="section-title">Detail Wilayah</h2>
    <?php if($r): ?>
      <div class="profile-grid mt-3">
        <div class="profile-item">
          <b>Nama Kelurahan</b>
          <span><?=htmlspecialchars($r['nama_kelurahan'])?></span>
        </div>
        <div class="profile-item">
          <b>Kecamatan</b>
          <span><?=htmlspecialchars($r['kecamatan'])?></span>
        </div>
        <div class="profile-item">
          <b>Kota Administrasi</b>
          <span><?=htmlspecialchars($r['kota'])?></span>
        </div>
        <div class="profile-item">
          <b>Provinsi</b>
          <span><?=htmlspecialchars($r['provinsi'])?></span>
        </div>
        <div class="profile-item">
          <b>Kode Pos</b>
          <span><?=htmlspecialchars($r['kode_pos'])?></span>
        </div>
        <div class="profile-item">
          <b>Luas Wilayah</b>
          <span><?=htmlspecialchars($r['luas_wilayah'])?> km&sup2;</span>
        </div>
        <div class="profile-item">
          <b>Jumlah RW</b>
          <span><?=htmlspecialchars($r['jumlah_rw'])?> RW</span>
        </div>
        <div class="profile-item">
          <b>Jumlah RT</b>
          <span><?=htmlspecialchars($r['jumlah_rt'])?> RT</span>
        </div>
        <div class="profile-item" style="grid-column: span 2;">
          <b>Kepala Kelurahan (Lurah)</b>
          <span><i class="fa-solid fa-user-tie"></i> <?=htmlspecialchars($r['kepala_kelurahan'] ?: '-')?></span>
        </div>
      </div>
      
      <div class="mt-4" style="border-top: 1px solid var(--border); padding-top: 1.5rem;">
        <h3 class="section-title">Sejarah</h3>
        <p style="text-align: justify; line-height: 1.8; color: var(--muted); margin-bottom: 1.5rem;"><?=nl2br(htmlspecialchars($r['sejarah']))?></p>
      </div>

      <?php if(!empty($r['lokasi'])): ?>
        <div class="note mt-3">
          <i class="fa-solid fa-globe"></i> <b>Website Resmi Kelurahan:</b> <a target="_blank" href="<?=htmlspecialchars($r['lokasi'])?>"><?=htmlspecialchars($r['lokasi'])?></a>
        </div>
      <?php endif; ?>
    <?php else: ?>
      <div class="alert danger">
        <i class="fa-solid fa-triangle-exclamation"></i> Data Kelurahan belum tersedia di database.
      </div>
    <?php endif; ?>
  </div>
</div>

<footer class="site-footer">
  <div class="inner">
    <span><i class="fa-solid fa-building"></i> Kelurahan Munjul &mdash; Jakarta Timur</span>
    <span>&copy; <?=date('Y')?> Sistem Pelayanan KTP</span>
  </div>
</footer>
</body>
</html>
