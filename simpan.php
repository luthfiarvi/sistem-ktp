<?php
require_once __DIR__.'/config/koneksi.php';
require_once __DIR__.'/config/status.php';

// Helper: aman-kan nama file
function safe_filename($name){
  $name = preg_replace('/[^A-Za-z0-9._-]/','_', $name);
  return time().'_'.$name;
}
// Helper: resize + crop foto ke rasio 3:4 (mis. 600x800 px)
function resize_to_3x4($srcPath, $destPath){
  if(!extension_loaded('gd')) return false;
  $info = @getimagesize($srcPath);
  if(!$info) return false;
  [$w,$h] = $info; $mime = $info['mime'] ?? '';
  $targetRatio = 3/4; $srcRatio = $w / max(1,$h);

  // tentukan area crop agar jadi 3:4
  if($srcRatio > $targetRatio){
    // terlalu lebar, potong kiri/kanan
    $cropW = (int)round($h * $targetRatio);
    $cropH = $h; $sx = (int)floor(($w - $cropW)/2); $sy = 0;
  } else {
    // terlalu tinggi, potong atas/bawah
    $cropW = $w; $cropH = (int)round($w / $targetRatio);
    $sx = 0; $sy = (int)floor(($h - $cropH)/2);
  }

  // ukuran akhir standar
  $dstW = 600; $dstH = 800;

  switch($mime){
    case 'image/jpeg': $src = @imagecreatefromjpeg($srcPath); break;
    case 'image/png':  $src = @imagecreatefrompng($srcPath);  break;
    default: return false;
  }
  if(!$src) return false;

  $dst = imagecreatetruecolor($dstW, $dstH);
  // latar putih (jaga jika PNG transparan)
  $white = imagecolorallocate($dst, 255,255,255);
  imagefilledrectangle($dst, 0,0, $dstW,$dstH, $white);
  imagecopyresampled($dst, $src, 0,0, $sx,$sy, $dstW,$dstH, $cropW,$cropH);

  $ok = false;
  if($mime==='image/jpeg'){
    $ok = imagejpeg($dst, $destPath, 85);
  } elseif($mime==='image/png'){
    $ok = imagepng($dst, $destPath, 6);
  }
  imagedestroy($src); imagedestroy($dst);
  return $ok;
}
// Helper: validasi ukuran & mime sederhana
function validate_upload($file, $maxMB, $allowedExt){
  if(!isset($file['tmp_name']) || $file['error']!==UPLOAD_ERR_OK) return "Upload gagal.";
  $sizeMB = $file['size']/(1024*1024);
  if($sizeMB > $maxMB) return "Ukuran melebihi {$maxMB}MB.";
  $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
  if(!in_array($ext, $allowedExt)) return "Ekstensi tidak diizinkan.";
  return true;
}

// Audit helpers
function ensure_audit_table($koneksi){
  $koneksi->query("CREATE TABLE IF NOT EXISTS audit_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    action VARCHAR(32),
    entity VARCHAR(64),
    entity_id INT,
    before_text LONGTEXT,
    after_text LONGTEXT
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
}
function log_audit($koneksi,$action,$entity,$id,$before,$after){
  ensure_audit_table($koneksi);
  $stmt=$koneksi->prepare("INSERT INTO audit_log(action,entity,entity_id,before_text,after_text) VALUES (?,?,?,?,?)");
  $b=json_encode($before,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
  $a=json_encode($after,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
  $stmt->bind_param('ssiss',$action,$entity,$id,$b,$a);
  $stmt->execute();
  $stmt->close();
}

if($_SERVER['REQUEST_METHOD']!=='POST'){ header('Location: formulir.php'); exit; }

// Pastikan skema status tersedia
ensure_status_schema($koneksi);

$nik           = trim($_POST['nik'] ?? '');
$nama          = trim($_POST['nama'] ?? '');
$tempat_lahir  = trim($_POST['tempat_lahir'] ?? '');
$tanggal_lahir = $_POST['tanggal_lahir'] ?? null;
$jenis_kelamin = $_POST['jenis_kelamin'] ?? 'L';
$alamat        = trim($_POST['alamat'] ?? '');
$agama         = trim($_POST['agama'] ?? '');
$pekerjaan     = trim($_POST['pekerjaan'] ?? '');
$status        = trim($_POST['status'] ?? ''); // status perkawinan (bukan status permohonan)
$rt            = trim($_POST['rt'] ?? '');
$rw            = trim($_POST['rw'] ?? '');
$status_permohonan = 'Diajukan';

// Validasi dasar
if($nik==='' || $nama===''){
  die("NIK dan Nama wajib diisi. <a href='formulir.php'>Kembali</a>");
}

// Cek duplikasi NIK lebih awal agar user mendapat pesan ramah
$cek = $koneksi->prepare('SELECT id, nama FROM formulir WHERE nik = ?');
$cek->bind_param('s', $nik);
$cek->execute();
$exists = $cek->get_result()->fetch_assoc();
$cek->close();
if($exists){
  $id_exist = (int)$exists['id'];
  $nama_exist = $exists['nama'];
  ?><!DOCTYPE html>
  <html lang="id">
  <head>
    <meta charset="utf-8">
    <title>NIK sudah terdaftar</title>
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
        <a class="nav-link active" href="formulir.php">Input</a>
        <a class="nav-link" href="lihat.php">Daftar</a>
      </div>
    </div>
  </nav>
  <div class="container">
    <div class="card">
      <h2 class="section-title">NIK sudah terdaftar</h2>
      <p class="alert warning">Data dengan NIK <b><?=htmlspecialchars($nik)?></b> sudah ada atas nama <b><?=htmlspecialchars($nama_exist)?></b>.<br>Silakan gunakan menu di bawah ini.</p>
      <div class="mt-3" style="display:flex; gap:10px; flex-wrap:wrap;">
        <a class="btn" href="output_ktp.php?id=<?=$id_exist?>">Lihat KTP</a>
        <a class="btn secondary" href="edit.php?id=<?=$id_exist?>">Edit Data</a>
        <a class="btn secondary" href="lihat.php?q=<?=urlencode($nik)?>">Lihat di Daftar</a>
        <a class="btn secondary" href="formulir.php">Kembali ke Formulir</a>
      </div>
    </div>
  </div>
  </body>
  </html><?php
  exit;
}

// Validasi file
$valFoto = validate_upload($_FILES['foto'], 1.5, ['jpg','jpeg','png']);
$valKK   = validate_upload($_FILES['kk'],   2.0, ['pdf','jpg','jpeg','png']);
$valAkte = validate_upload($_FILES['akte'], 2.0, ['pdf','jpg','jpeg','png']);

foreach (['Foto'=>$valFoto,'KK'=>$valKK,'Akte'=>$valAkte] as $label=>$res) {
  if($res !== true){ die("Gagal $label: $res <a href='formulir.php'>Kembali</a>"); }
}

// Simpan file
@mkdir(__DIR__.'/uploads/kk', 0775, true);
@mkdir(__DIR__.'/uploads/akte', 0775, true);
@mkdir(__DIR__.'/uploads/foto', 0775, true);

$fotoName = safe_filename($_FILES['foto']['name']);
$kkName   = safe_filename($_FILES['kk']['name']);
$akteName = safe_filename($_FILES['akte']['name']);

move_uploaded_file($_FILES['foto']['tmp_name'], __DIR__."/uploads/foto/".$fotoName);
move_uploaded_file($_FILES['kk']['tmp_name'],   __DIR__."/uploads/kk/".$kkName);
move_uploaded_file($_FILES['akte']['tmp_name'], __DIR__."/uploads/akte/".$akteName);

// Resize foto menjadi 3x4 agar konsisten dengan KTP
@resize_to_3x4(__DIR__."/uploads/foto/".$fotoName, __DIR__."/uploads/foto/".$fotoName);

// Simpan ttd jika ada (canvas base64)
@mkdir(__DIR__.'/uploads/ttd', 0775, true);
$ttdName = null;
if(!empty($_POST['ttd_data'])){
  $raw = $_POST['ttd_data'];
  if(strpos($raw, 'data:image') === 0){
    $parts = explode(',', $raw, 2);
    $bin = base64_decode($parts[1] ?? '');
    if($bin !== false && strlen($bin) > 0){
      $ttdName = 'ttd_'.time().'_'.preg_replace('/\D/','',$nik).'.png';
      file_put_contents(__DIR__.'/uploads/ttd/'.$ttdName, $bin);
    }
  }
}

// Insert ke formulir (prepared statement)
$stmt = $koneksi->prepare("INSERT INTO formulir
  (nik,nama,tempat_lahir,tanggal_lahir,jenis_kelamin,alamat,rt,rw,agama,pekerjaan,status,foto,ttd,status_permohonan)
  VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
$stmt->bind_param("ssssssssssssss",
  $nik,$nama,$tempat_lahir,$tanggal_lahir,$jenis_kelamin,$alamat,$rt,$rw,$agama,$pekerjaan,$status,$fotoName,$ttdName,$status_permohonan
);

try{
  $stmt->execute();
} catch(mysqli_sql_exception $e){
  // Kode 1062 = duplicate key
  if((int)$e->getCode() === 1062){
    // rollback file yang terlanjur diunggah
    @unlink(__DIR__."/uploads/foto/".$fotoName);
    @unlink(__DIR__."/uploads/kk/".$kkName);
    @unlink(__DIR__."/uploads/akte/".$akteName);
    if($ttdName){ @unlink(__DIR__."/uploads/ttd/".$ttdName); }

    // Cari ID yang sudah ada untuk navigasi
    $st = $koneksi->prepare('SELECT id, nama FROM formulir WHERE nik=?');
    $st->bind_param('s',$nik); $st->execute(); $ex = $st->get_result()->fetch_assoc(); $st->close();
    $id_exist = (int)($ex['id'] ?? 0); $nama_exist = $ex['nama'] ?? '';
    ?><!DOCTYPE html>
    <html lang="id">
    <head>
      <meta charset="utf-8">
      <title>NIK sudah terdaftar</title>
      <link rel="stylesheet" href="assets/css/style.css">
    </head>
    <body>
    <div class="container">
      <div class="card">
        <h2 class="section-title">NIK sudah terdaftar</h2>
        <p class="alert warning">Data dengan NIK <b><?=htmlspecialchars($nik)?></b> sudah ada<?= $nama_exist? ' atas nama <b>'.htmlspecialchars($nama_exist).'</b>':'' ?>.<br>Pilih tindakan berikut:</p>
        <div class="mt-3" style="display:flex; gap:10px; flex-wrap:wrap;">
          <?php if($id_exist): ?>
          <a class="btn" href="output_ktp.php?id=<?=$id_exist?>">Lihat KTP</a>
          <a class="btn secondary" href="edit.php?id=<?=$id_exist?>">Edit Data</a>
          <?php endif; ?>
          <a class="btn secondary" href="lihat.php?q=<?=urlencode($nik)?>">Lihat di Daftar</a>
          <a class="btn secondary" href="formulir.php">Kembali ke Formulir</a>
        </div>
      </div>
    </div>
    </body>
    </html><?php
    exit;
  }
  // selain duplicate, perlakukan seperti error biasa
  @unlink(__DIR__."/uploads/foto/".$fotoName);
  @unlink(__DIR__."/uploads/kk/".$kkName);
  @unlink(__DIR__."/uploads/akte/".$akteName);
  if($ttdName){ @unlink(__DIR__."/uploads/ttd/".$ttdName); }
  die("Gagal simpan formulir: ".$e->getMessage()." <a href='formulir.php'>Kembali</a>");
}
$id_formulir = $stmt->insert_id;
$stmt->close();

// Insert KK
$stmt = $koneksi->prepare("INSERT INTO kk (id_formulir,file_kk) VALUES (?,?)");
$stmt->bind_param("is", $id_formulir, $kkName);
if(!$stmt->execute()){ die("Gagal simpan KK: ".$stmt->error); }
$stmt->close();

// Insert Akte
$stmt = $koneksi->prepare("INSERT INTO akte (id_formulir,file_akte) VALUES (?,?)");
$stmt->bind_param("is", $id_formulir, $akteName);
if(!$stmt->execute()){ die("Gagal simpan Akte: ".$stmt->error); }
$stmt->close();

// Beres: arahkan ke output
// audit log create
log_audit($koneksi,'create','formulir',$id_formulir,[],[
  'nik'=>$nik,'nama'=>$nama,'tempat_lahir'=>$tempat_lahir,'tanggal_lahir'=>$tanggal_lahir,
  'jenis_kelamin'=>$jenis_kelamin,'alamat'=>$alamat,'agama'=>$agama,'pekerjaan'=>$pekerjaan,'status'=>$status,
  'foto'=>$fotoName,'ttd'=>$ttdName,'file_kk'=>$kkName,'file_akte'=>$akteName
]);

// Catat history awal
$stmt = $koneksi->prepare('INSERT INTO status_history (id_formulir,status,catatan) VALUES (?,?,?)');
$catatan_awal = 'Permohonan diajukan';
$stmt->bind_param('iss', $id_formulir, $status_permohonan, $catatan_awal);
$stmt->execute();
$stmt->close();

header("Location: output_ktp.php?id=".$id_formulir);
exit;
