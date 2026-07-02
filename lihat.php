<?php
require_once __DIR__.'/config/koneksi.php';
require_once __DIR__.'/config/status.php';
ensure_status_schema($koneksi);

$q = trim($_GET['q'] ?? '');
$sql = "SELECT f.id AS id_formulir, f.nik, f.nama, f.tanggal_lahir, f.jenis_kelamin, COALESCE(f.status_permohonan,'Diajukan') AS status_permohonan
        FROM formulir f";
$params = [];
if($q !== ''){
  $qLike = "%".$q."%";
  $sql .= " WHERE nik LIKE ? OR nama LIKE ?";
}
$sql .= " ORDER BY id_formulir DESC LIMIT 200";

$stmt = $koneksi->prepare($sql);
if($q !== ''){
  $stmt->bind_param("ss", $qLike, $qLike);
}
$stmt->execute();
$res = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Daftar KTP</title>
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
      <a class="nav-link" href="kelurahan.php">Profil</a>
      <a class="nav-link" href="formulir.php">Input</a>
      <a class="nav-link active" href="lihat.php">Daftar</a>
    </div>
  </div>
</nav>
<div class="container">
  <div class="card">
    <h2>Daftar KTP</h2>
    <form class="searchbar" method="get">
      <input type="text" name="q" placeholder="Cari NIK / Nama..." value="<?=htmlspecialchars($q)?>">
      <button class="btn" type="submit">Cari</button>
      <a class="btn secondary" href="lihat.php">Reset</a>
    </form>
    <div class="table-wrap">
    <table class="table">
      <thead>
        <tr><th>#</th><th>NIK</th><th>Nama</th><th>Tgl Lahir</th><th>JK</th><th>Status</th><th>Aksi</th></tr>
      </thead>
      <tbody>
      <?php $no=1; while($r = $res->fetch_assoc()): ?>
        <tr>
          <td><?=$no++?></td>
          <td><span class="badge"><?=htmlspecialchars($r['nik'])?></span></td>
          <td><?=htmlspecialchars($r['nama'])?></td>
          <td><?=htmlspecialchars($r['tanggal_lahir'])?></td>
          <td><?=htmlspecialchars($r['jenis_kelamin'])?></td>
          <td><span class="badge"><?=htmlspecialchars($r['status_permohonan'])?></span></td>
          <td>
            <a class="btn" href="output_ktp.php?id=<?=$r['id_formulir']?>">Lihat KTP</a>
            <a class="btn secondary" href="edit.php?id=<?=$r['id_formulir']?>">Edit</a>
            <?php 
              $next = ktp_status_next($r['status_permohonan']);
              if($next !== $r['status_permohonan']){ ?>
              <form action="status_permohonan.php" method="post" style="display:inline">
                <input type="hidden" name="id" value="<?=$r['id_formulir']?>">
                <input type="hidden" name="to" value="<?=htmlspecialchars($next)?>">
                <input type="hidden" name="back" value="<?=htmlspecialchars('lihat.php?q='.$q)?>">
                <button class="btn" type="submit">Lanjut: <?=$next?></button>
              </form>
            <?php } ?>
            <form action="hapus.php" method="post" style="display:inline" onsubmit="return confirm('Hapus data ini? Tindakan tidak bisa dibatalkan.');">
              <input type="hidden" name="id" value="<?=$r['id_formulir']?>">
              <button class="btn danger" type="submit">Hapus</button>
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
    </div>
    <?php if($no===1): ?>
      <p class="note">Data belum ada / tidak ditemukan.</p>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
