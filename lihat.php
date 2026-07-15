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

function getStatusBadgeClass($status) {
    switch ($status) {
        case 'Diajukan':
            return 'badge-info';
        case 'Diverifikasi':
            return 'badge-warning';
        case 'Disetujui':
            return 'badge-success';
        case 'Dicetak':
            return 'badge-purple';
        case 'Selesai':
            return 'badge-success';
        default:
            return 'badge-info';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Daftar Permohonan KTP - Kelurahan Munjul</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
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
      <a class="nav-link" href="kelurahan.php">Profil</a>
      <a class="nav-link" href="formulir.php">Input</a>
      <a class="nav-link active" href="lihat.php">Daftar</a>
    </div>
  </div>
</nav>

<div class="container">
  <div class="card">
    <h2 class="section-title">Daftar Permohonan KTP</h2>
    <p class="note" style="margin-bottom: 1.5rem;"><i class="fa-solid fa-folder-open"></i> Di bawah ini adalah daftar data pemohon KTP Kelurahan Munjul. Anda dapat mencari pemohon berdasarkan NIK atau Nama.</p>

    <!-- Search bar -->
    <form class="searchbar" method="get" autocomplete="off">
      <input type="text" name="q" placeholder="Cari NIK / Nama Pemohon..." value="<?=htmlspecialchars($q)?>">
      <button class="btn" type="submit"><i class="fa-solid fa-magnifying-glass"></i> Cari</button>
      <a class="btn secondary" href="lihat.php"><i class="fa-solid fa-rotate-left"></i> Reset</a>
    </form>

    <!-- Table content -->
    <div class="table-wrap">
      <table class="table">
        <thead>
          <tr>
            <th>#</th>
            <th>NIK</th>
            <th>Nama Lengkap</th>
            <th>Tgl Lahir</th>
            <th>JK</th>
            <th>Status Permohonan</th>
            <th style="text-align: center;">Aksi</th>
          </tr>
        </thead>
        <tbody>
        <?php $no=1; while($r = $res->fetch_assoc()): ?>
          <tr>
            <td><?=$no++?></td>
            <td><span class="badge badge-info" style="font-family: monospace; font-size: 0.85rem; letter-spacing: 0.2px;"><?=htmlspecialchars($r['nik'])?></span></td>
            <td style="font-weight: 600; color: var(--text);"><?=htmlspecialchars($r['nama'])?></td>
            <td><?=date('d-m-Y', strtotime($r['tanggal_lahir']))?></td>
            <td><span style="font-weight: 600;"><?=htmlspecialchars($r['jenis_kelamin'])?></span></td>
            <td>
              <span class="badge <?=getStatusBadgeClass($r['status_permohonan'])?>">
                <?=htmlspecialchars($r['status_permohonan'])?>
              </span>
            </td>
            <td style="text-align: center;">
              <div style="display: inline-flex; gap: 6px; justify-content: center; flex-wrap: wrap;">
                <a class="btn btn-pink" style="padding: 0.5rem 0.85rem; font-size: 0.85rem;" href="output_ktp.php?id=<?=$r['id_formulir']?>" title="Lihat Detail KTP virtual"><i class="fa-solid fa-address-card"></i> Detail</a>
                <a class="btn secondary" style="padding: 0.5rem 0.85rem; font-size: 0.85rem;" href="edit.php?id=<?=$r['id_formulir']?>" title="Edit Data"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
                
                <?php 
                $next = ktp_status_next($r['status_permohonan']);
                if($next !== $r['status_permohonan']){ ?>
                  <form action="status_permohonan.php" method="post" style="display:inline">
                    <input type="hidden" name="id" value="<?=$r['id_formulir']?>">
                    <input type="hidden" name="to" value="<?=htmlspecialchars($next)?>">
                    <input type="hidden" name="back" value="<?=htmlspecialchars('lihat.php?q='.$q)?>">
                    <button class="btn" style="padding: 0.5rem 0.85rem; font-size: 0.85rem; background: var(--success); border-color: var(--success);" type="submit" title="Ubah status ke <?=$next?>">
                      <i class="fa-solid fa-circle-chevron-right"></i> <?=$next?>
                    </button>
                  </form>
                <?php } ?>
                
                <form action="hapus.php" method="post" style="display:inline" onsubmit="return confirm('Hapus data pemohon ini? Tindakan tidak dapat dibatalkan.');">
                  <input type="hidden" name="id" value="<?=$r['id_formulir']?>">
                  <button class="btn danger" style="padding: 0.5rem 0.85rem; font-size: 0.85rem;" type="submit" title="Hapus Data"><i class="fa-solid fa-trash-can"></i> Hapus</button>
                </form>
              </div>
            </td>
          </tr>
        <?php endwhile; ?>
        </tbody>
      </table>
    </div>

    <?php if($no===1): ?>
      <div class="alert danger mt-4" style="margin-bottom: 0;">
        <i class="fa-solid fa-triangle-exclamation"></i> Data permohonan KTP belum ada atau tidak ditemukan.
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
