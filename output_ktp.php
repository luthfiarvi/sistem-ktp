<?php
require_once __DIR__.'/config/koneksi.php';
require_once __DIR__.'/config/status.php';
$id = intval($_GET['id'] ?? 0);
if($id<=0){ die("Parameter tidak valid."); }

$stmt = $koneksi->prepare("
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
    LEFT JOIN kelurahan kel ON 1 = 1
    WHERE f.id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
$stmt->close();

ensure_status_schema($koneksi);

// Ambil status permohonan + riwayat
$sp = 'Diajukan';
$stmt = $koneksi->prepare('SELECT COALESCE(status_permohonan,\'Diajukan\') AS sp FROM formulir WHERE id=?');
$stmt->bind_param('i',$id);
$stmt->execute();
$rowSp = $stmt->get_result()->fetch_assoc();
$stmt->close();
if($rowSp){ $sp = $rowSp['sp']; }

$history = [];
$hres = $koneksi->prepare('SELECT status, catatan, created_at FROM status_history WHERE id_formulir=? ORDER BY id ASC');
$hres->bind_param('i',$id); $hres->execute(); $resH=$hres->get_result();
while($h = $resH->fetch_assoc()){ $history[] = $h; }
$hres->close();

if(!$data){ die("Data tidak ditemukan."); }

// path foto
$fotopath = 'uploads/foto/'.($data['foto'] ?? '');
if(!is_file($fotopath)){
  if(is_file('assets/img/placeholder.png')){
    $fotopath = 'assets/img/placeholder.png';
  } else {
    $fotopath = 'assets/img/placeholder.svg';
  }
} // fallback foto

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
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>KTP - <?=htmlspecialchars($data['nama'])?></title>
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
    <!-- Print Bar & Actions -->
    <div class="printbar">
      <div style="display: inline-flex; gap: 8px;">
        <a class="btn secondary" href="lihat.php"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
        <a class="btn secondary" href="edit.php?id=<?=$id?>"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
        <form action="hapus.php" method="post" style="display:inline" onsubmit="return confirm('Hapus data ini? Tindakan tidak bisa dibatalkan.');">
          <input type="hidden" name="id" value="<?=$id?>">
          <button class="btn danger" type="submit"><i class="fa-solid fa-trash-can"></i> Hapus</button>
        </form>
      </div>
      <button class="btn btn-pink" onclick="window.print()"><i class="fa-solid fa-print"></i> Cetak KTP</button>
    </div>

    <!-- Status Tracker -->
    <div class="note" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
      <div>
        <i class="fa-solid fa-signal"></i> Status Permohonan: 
        <span class="badge <?=getStatusBadgeClass($sp)?>" style="margin-left: 6px;"><?=htmlspecialchars($sp)?></span>
      </div>
      
      <?php $next = ktp_status_next($sp); if($next !== $sp): ?>
      <form action="status_permohonan.php" method="post" style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
        <input type="hidden" name="id" value="<?=$id?>">
        <input type="hidden" name="to" value="<?=htmlspecialchars($next)?>">
        <input type="hidden" name="back" value="<?=htmlspecialchars('output_ktp.php?id='.$id)?>">
        <input type="text" name="catatan" placeholder="Catatan opsional..." style="padding: 0.5rem 0.75rem; font-size: 0.85rem; width: 200px;">
        <button class="btn" style="padding: 0.55rem 1rem; font-size: 0.85rem; background: var(--success); border-color: var(--success);" type="submit">
          <i class="fa-solid fa-circle-chevron-right"></i> Lanjut: <?=$next?>
        </button>
      </form>
      <?php endif; ?>
    </div>

    <?php
      // Helper kecil untuk format tampilan sesuai contoh KTP
      $jkText = ($data['jenis_kelamin'] === 'P') ? 'Perempuan' : 'Laki - Laki';
      $tglLahir = $data['tanggal_lahir'] ? date('d - m - Y', strtotime($data['tanggal_lahir'])) : '';
      function extractRtRw($alamat){
        $rt = '-'; $rw = '-';
        if(preg_match('/RT\s*0*([0-9]{1,3})/i', $alamat, $m)) $rt = sprintf('%02d', (int)$m[1]);
        if(preg_match('/RW\s*0*([0-9]{1,3})/i', $alamat, $m)) $rw = sprintf('%02d', (int)$m[1]);
        if($rt !== '-' || $rw !== '-'){
          return ($rt === '-'?'-':$rt) . '/' . ($rw === '-'?'-':$rw);
        }
        if(preg_match('/(\d{1,3})\s*\/\s*(\d{1,3})/', $alamat, $m)){
          return sprintf('%02d/%02d', (int)$m[1], (int)$m[2]);
        }
        return '—';
      }
      
      // Prioritaskan RT/RW eksplisit jika tersedia di tabel formulir
      $rtRw = null;
      $rt = null; $rw = null;
      $stmt = $koneksi->prepare('SELECT rt, rw, ttd FROM formulir WHERE id=?');
      $stmt->bind_param('i',$id); $stmt->execute(); $rr = $stmt->get_result()->fetch_assoc(); $stmt->close();
      if($rr){ 
        $rt = trim((string)($rr['rt'] ?? '')); 
        $rw = trim((string)($rr['rw'] ?? '')); 
        $ttd_file = trim((string)($rr['ttd'] ?? '')); 
      }
      if($rt !== '' || $rw !== ''){
        $rtRw = sprintf('%s/%s', $rt!==''?str_pad($rt,2,'0',STR_PAD_LEFT):'00', $rw!==''?str_pad($rw,2,'0',STR_PAD_LEFT):'00');
      }
      if(!$rtRw){ $rtRw = extractRtRw($data['alamat'] ?? ''); }

      // Tanggal pembuatan: pakai riwayat status pertama jika ada, jika tidak pakai hari ini
      $tgl_pembuatan = date('d - m - Y');
      if(!empty($history) && !empty($history[0]['created_at'])){
        $tgl_pembuatan = date('d - m - Y', strtotime($history[0]['created_at']));
      }
    ?>

    <!-- e-KTP Virtual Card Wrapper -->
    <div style="width: 100%; overflow: auto; padding: 20px 0; display: flex; justify-content: center;">
      <div class="ktp">
        <span class="inner-border"></span>
        <div class="safe">
          <div class="title-center">
            <div class="prov">PROVINSI <?=strtoupper(htmlspecialchars($data['provinsi'] ?? ''))?></div>
            <div class="kota"><?=strtoupper(htmlspecialchars($data['kota'] ?? ''))?></div>
          </div>

          <div class="content-grid">
            <div class="left">
              <div class="nik-line">
                <span class="label">NIK</span>
                <span class="sep">:</span>
                <span class="value nik-value"><?=htmlspecialchars($data['nik'])?></span>
              </div>

              <div class="data-rows">
                <div class="row"><span class="label">Nama</span><span class="sep">:</span><span class="value"><?=htmlspecialchars($data['nama'])?></span></div>
                <div class="row"><span class="label">Tempat/Tgl Lahir</span><span class="sep">:</span><span class="value"><?=htmlspecialchars($data['tempat_lahir'])?> / <?=htmlspecialchars($tglLahir)?></span></div>
                <div class="row"><span class="label">Jenis Kelamin</span><span class="sep">:</span><span class="value"><?=$jkText?></span></div>
                <div class="row"><span class="label">Alamat</span><span class="sep">:</span><span class="value"><?=nl2br(htmlspecialchars($data['alamat']))?></span></div>
                <div class="row shift"><span class="label">RT/RW</span><span class="sep">:</span><span class="value"><?=htmlspecialchars($rtRw)?></span></div>
                <div class="row shift"><span class="label">Kel/Desa</span><span class="sep">:</span><span class="value"><?=htmlspecialchars($data['nama_kelurahan'])?></span></div>
                <div class="row shift"><span class="label">Kecamatan</span><span class="sep">:</span><span class="value"><?=htmlspecialchars($data['kecamatan'])?></span></div>
                <div class="row"><span class="label">Agama</span><span class="sep">:</span><span class="value"><?=htmlspecialchars($data['agama'])?></span></div>
                <div class="row"><span class="label">Status Perkawinan</span><span class="sep">:</span><span class="value"><?=htmlspecialchars($data['status'])?></span></div>
                <div class="row"><span class="label">Pekerjaan</span><span class="sep">:</span><span class="value"><?=htmlspecialchars($data['pekerjaan'])?></span></div>
                <div class="row"><span class="label">Kewarganegaraan</span><span class="sep">:</span><span class="value">WNI</span></div>
                <div class="row"><span class="label">Berlaku Hingga</span><span class="sep">:</span><span class="value">Seumur Hidup</span></div>
              </div>
            </div>

            <div class="right">
              <div class="foto-frame">
                <img class="foto" src="<?=htmlspecialchars($fotopath)?>" alt="Foto">
              </div>
              <div class="photo-caption">JAKARTA TIMUR<br><span class="photo-date"><?=$tgl_pembuatan?></span></div>
              <?php 
                $ttdpath = (!empty($ttd_file) && is_file('uploads/ttd/'.$ttd_file)) ? ('uploads/ttd/'.$ttd_file) : '';
                if($ttdpath): ?>
                <img class="ttd-small" src="<?=htmlspecialchars($ttdpath)?>" alt="Tanda Tangan">
              <?php endif; ?>
            </div>
          </div>

          <div class="footer">
            <div class="city">
              <?=strtoupper(htmlspecialchars($data['kota']))?><br>
              <span><?=date('d - m - Y')?></span>
            </div>
            <div class="sign">
              <div class="sign-line"></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Berkas Pendukung -->
    <div class="mt-4" style="border-top: 1px solid var(--border); padding-top: 1.5rem;">
      <h3 class="section-title">Berkas Persyaratan yang Diunggah</h3>
      <div class="row mt-2" style="display:flex; gap:12px; flex-wrap:wrap;">
        <?php if(!empty($data['file_kk'])): ?>
          <a class="btn secondary" target="_blank" href="uploads/kk/<?=htmlspecialchars($data['file_kk'])?>">
            <i class="fa-solid fa-file-pdf"></i> Lihat Kartu Keluarga (KK)
          </a>
        <?php endif; ?>
        <?php if(!empty($data['file_akte'])): ?>
          <a class="btn secondary" target="_blank" href="uploads/akte/<?=htmlspecialchars($data['file_akte'])?>">
            <i class="fa-solid fa-file-pdf"></i> Lihat Akte Kelahiran
          </a>
        <?php endif; ?>
      </div>
    </div>

    <!-- Riwayat Status -->
    <div class="mt-4" style="border-top: 1px solid var(--border); padding-top: 1.5rem;">
      <h3 class="section-title">Riwayat Status Permohonan</h3>
      <?php if(empty($history)): ?>
        <p class="note"><i class="fa-solid fa-circle-exclamation"></i> Belum ada riwayat perubahan status untuk permohonan ini.</p>
      <?php else: ?>
        <div class="table-wrap">
          <table class="table">
            <thead>
              <tr>
                <th>Waktu Perubahan</th>
                <th>Status</th>
                <th>Catatan / Keterangan</th>
              </tr>
            </thead>
            <tbody>
            <?php foreach($history as $h): ?>
              <tr>
                <td style="font-weight: 600;"><?=date('d-m-Y H:i', strtotime($h['created_at']))?></td>
                <td>
                  <span class="badge <?=getStatusBadgeClass($h['status'])?>">
                    <?=htmlspecialchars($h['status'])?>
                  </span>
                </td>
                <td style="color: var(--muted);"><?=nl2br(htmlspecialchars($h['catatan'] ?? '-'))?></td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

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
