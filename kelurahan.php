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
<section class="hero hero-kelurahan" style="background-image:url('<?=htmlspecialchars($bg)?>')">
  <div class="container">
    <div class="breadcrumbs"><a href="index.php">Beranda</a> / <span>Kelurahan Munjul</span></div>
    <h1 class="title">Kelurahan Munjul</h1>
    <p class="subtitle" style="color:#e5e7eb; max-width:70ch;">Informasi singkat, data wilayah, dan sejarah Kelurahan Munjul, Kecamatan Cipayung, Jakarta Timur.</p>
  </div>
</section>

<div class="container">
  <div class="card">
    <h2>Kelurahan Munjul &mdash; Cipayung, Jakarta Timur</h2>
    <?php if($r): ?>
      <div class="profile-grid mt-3">
        <div class="item"><b>Kelurahan</b> <?=htmlspecialchars($r['nama_kelurahan'])?></div>
        <div class="item"><b>Kecamatan</b> <?=htmlspecialchars($r['kecamatan'])?></div>
        <div class="item"><b>Kota</b> <?=htmlspecialchars($r['kota'])?></div>
        <div class="item"><b>Provinsi</b> <?=htmlspecialchars($r['provinsi'])?></div>
        <div class="item"><b>Kode Pos</b> <?=htmlspecialchars($r['kode_pos'])?></div>
        <div class="item"><b>Luas Wilayah</b> <?=htmlspecialchars($r['luas_wilayah'])?> km&sup2;</div>
        <div class="item"><b>RW</b> <?=htmlspecialchars($r['jumlah_rw'])?></div>
        <div class="item"><b>RT</b> <?=htmlspecialchars($r['jumlah_rt'])?></div>
        <div class="item"><b>Kepala Kelurahan</b> <?=htmlspecialchars($r['kepala_kelurahan'] ?: '-')?></div>
      </div>
      <div class="section">
        <h3 class="section-title">Sejarah</h3>
        <p><?=nl2br(htmlspecialchars($r['sejarah']))?></p>
      </div>
      <?php if(!empty($r['lokasi'])): ?>
        <p class="section"><b>Website:</b> <a target="_blank" href="<?=htmlspecialchars($r['lokasi'])?>"><?=htmlspecialchars($r['lokasi'])?></a></p>
      <?php endif; ?>
    <?php else: ?>
      <p class="note">Data kelurahan belum tersedia.</p>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
