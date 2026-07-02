<?php
require_once __DIR__.'/config/koneksi.php';
require_once __DIR__.'/config/status.php';

function safe_filename($name){
  $name = preg_replace('/[^A-Za-z0-9._-]/','_', $name);
  return time().'_'.$name;
}
function validate_upload($file, $maxMB, $allowedExt){
  if(!isset($file['tmp_name'])) return "Upload tidak valid.";
  if($file['error'] === UPLOAD_ERR_NO_FILE) return 'SKIP';
  if($file['error'] !== UPLOAD_ERR_OK) return "Upload gagal.";
  $sizeMB = $file['size']/(1024*1024);
  if($sizeMB > $maxMB) return "Ukuran melebihi {$maxMB}MB.";
  $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
  if(!in_array($ext, $allowedExt)) return "Ekstensi tidak diizinkan.";
  return true;
}
function resize_to_3x4($srcPath, $destPath){
  if(!extension_loaded('gd')) return false;
  $info = @getimagesize($srcPath); if(!$info) return false;
  [$w,$h] = $info; $mime = $info['mime'] ?? '';
  $targetRatio = 3/4; $srcRatio = $w / max(1,$h);
  if($srcRatio > $targetRatio){ $cropW = (int)round($h * $targetRatio); $cropH=$h; $sx=(int)(($w-$cropW)/2); $sy=0; }
  else { $cropW=$w; $cropH=(int)round($w / $targetRatio); $sx=0; $sy=(int)(($h-$cropH)/2); }
  $dstW=600; $dstH=800;
  switch($mime){
    case 'image/jpeg': $src=@imagecreatefromjpeg($srcPath); break;
    case 'image/png':  $src=@imagecreatefrompng($srcPath); break;
    default: return false;
  }
  if(!$src) return false;
  $dst=imagecreatetruecolor($dstW,$dstH); $white=imagecolorallocate($dst,255,255,255);
  imagefilledrectangle($dst,0,0,$dstW,$dstH,$white);
  imagecopyresampled($dst,$src,0,0,$sx,$sy,$dstW,$dstH,$cropW,$cropH);
  $ok=false; if($mime==='image/jpeg') $ok=imagejpeg($dst,$destPath,85); else $ok=imagepng($dst,$destPath,6);
  imagedestroy($src); imagedestroy($dst); return $ok;
}

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

if($_SERVER['REQUEST_METHOD']!=='POST'){ header('Location: lihat.php'); exit; }

// Pastikan skema opsional tersedia (rt/rw/status table)
ensure_status_schema($koneksi);

$id = intval($_POST['id'] ?? 0);
if($id<=0){ die('Parameter tidak valid.'); }

// Ambil data lama
$stmt = $koneksi->prepare("SELECT f.*, (SELECT file_kk FROM kk WHERE id_formulir=f.id LIMIT 1) AS file_kk, (SELECT file_akte FROM akte WHERE id_formulir=f.id LIMIT 1) AS file_akte FROM formulir f WHERE f.id=?");
$stmt->bind_param('i',$id);
$stmt->execute();
$lama = $stmt->get_result()->fetch_assoc();
$stmt->close();
if(!$lama){ die('Data tidak ditemukan.'); }

$nik           = trim($_POST['nik'] ?? '');
$nama          = trim($_POST['nama'] ?? '');
$tempat_lahir  = trim($_POST['tempat_lahir'] ?? '');
$tanggal_lahir = $_POST['tanggal_lahir'] ?? null;
$jenis_kelamin = $_POST['jenis_kelamin'] ?? 'L';
$alamat        = trim($_POST['alamat'] ?? '');
$rt            = trim($_POST['rt'] ?? '');
$rw            = trim($_POST['rw'] ?? '');
$agama         = trim($_POST['agama'] ?? '');
$pekerjaan     = trim($_POST['pekerjaan'] ?? '');
$status        = trim($_POST['status'] ?? '');

if($nik==='' || $nama===''){
  die("NIK dan Nama wajib diisi. <a href='edit.php?id=".$id."'>Kembali</a>");
}

@mkdir(__DIR__.'/uploads/kk', 0775, true);
@mkdir(__DIR__.'/uploads/akte', 0775, true);
@mkdir(__DIR__.'/uploads/foto', 0775, true);

// Validasi file jika diupload
$valFoto = validate_upload($_FILES['foto'] ?? [], 1.5, ['jpg','jpeg','png']);
$valKK   = validate_upload($_FILES['kk'] ?? [],   2.0, ['pdf','jpg','jpeg','png']);
$valAkte = validate_upload($_FILES['akte'] ?? [], 2.0, ['pdf','jpg','jpeg','png']);
foreach (['Foto'=>$valFoto,'KK'=>$valKK,'Akte'=>$valAkte] as $label=>$res) {
  if($res !== true && $res !== 'SKIP'){ die("Gagal $label: $res <a href='edit.php?id=$id'>Kembali</a>"); }
}

$fotoName = $lama['foto'];
if(($valFoto===true) && isset($_FILES['foto']) && $_FILES['foto']['error']===UPLOAD_ERR_OK){
  $new = safe_filename($_FILES['foto']['name']);
  if(move_uploaded_file($_FILES['foto']['tmp_name'], __DIR__."/uploads/foto/".$new)){
    @resize_to_3x4(__DIR__."/uploads/foto/".$new, __DIR__."/uploads/foto/".$new);
    $old = $fotoName; $fotoName = $new;
    if(!empty($old) && is_file(__DIR__."/uploads/foto/".$old)) @unlink(__DIR__."/uploads/foto/".$old);
  }
}

// TTD
$ttdName = $lama['ttd'] ?? null;
@mkdir(__DIR__.'/uploads/ttd', 0775, true);
if(!empty($_POST['hapus_ttd'])){
  if(!empty($ttdName) && is_file(__DIR__.'/uploads/ttd/'.$ttdName)) @unlink(__DIR__.'/uploads/ttd/'.$ttdName);
  $ttdName = null;
}
if(!empty($_POST['ttd_data'])){
  $raw = $_POST['ttd_data'];
  if(strpos($raw,'data:image')===0){
    $parts = explode(',', $raw, 2); $bin = base64_decode($parts[1] ?? '');
    if($bin !== false && strlen($bin)>0){
      $new = 'ttd_'.time().'_'.preg_replace('/\D/','',$nik).'.png';
      file_put_contents(__DIR__.'/uploads/ttd/'.$new, $bin);
      if(!empty($ttdName) && is_file(__DIR__.'/uploads/ttd/'.$ttdName)) @unlink(__DIR__.'/uploads/ttd/'.$ttdName);
      $ttdName = $new;
    }
  }
}

// Update data formulir (termasuk ttd)
$stmt = $koneksi->prepare("UPDATE formulir SET nik=?, nama=?, tempat_lahir=?, tanggal_lahir=?, jenis_kelamin=?, alamat=?, rt=?, rw=?, agama=?, pekerjaan=?, status=?, foto=?, ttd=? WHERE id=?");
$stmt->bind_param('sssssssssssssi', $nik,$nama,$tempat_lahir,$tanggal_lahir,$jenis_kelamin,$alamat,$rt,$rw,$agama,$pekerjaan,$status,$fotoName,$ttdName,$id);
if(!$stmt->execute()){
  die('Gagal update formulir: '.$stmt->error);
}
$stmt->close();

// KK
$kkName = $lama['file_kk'];
if(($valKK===true) && isset($_FILES['kk']) && $_FILES['kk']['error']===UPLOAD_ERR_OK){
  $new = safe_filename($_FILES['kk']['name']);
  if(move_uploaded_file($_FILES['kk']['tmp_name'], __DIR__."/uploads/kk/".$new)){
    $old = $kkName; $kkName = $new;
    if(!empty($old) && is_file(__DIR__."/uploads/kk/".$old)) @unlink(__DIR__."/uploads/kk/".$old);
  }
}
$exists = $koneksi->prepare('SELECT COUNT(*) c FROM kk WHERE id_formulir=?');
$exists->bind_param('i',$id); $exists->execute(); $c=$exists->get_result()->fetch_assoc()['c'] ?? 0; $exists->close();
if($c>0){ $stmt = $koneksi->prepare('UPDATE kk SET file_kk=? WHERE id_formulir=?'); $stmt->bind_param('si',$kkName,$id); $stmt->execute(); $stmt->close(); }
else { $stmt = $koneksi->prepare('INSERT INTO kk (id_formulir,file_kk) VALUES (?,?)'); $stmt->bind_param('is',$id,$kkName); $stmt->execute(); $stmt->close(); }

// Akte
$akteName = $lama['file_akte'];
if(($valAkte===true) && isset($_FILES['akte']) && $_FILES['akte']['error']===UPLOAD_ERR_OK){
  $new = safe_filename($_FILES['akte']['name']);
  if(move_uploaded_file($_FILES['akte']['tmp_name'], __DIR__."/uploads/akte/".$new)){
    $old = $akteName; $akteName = $new;
    if(!empty($old) && is_file(__DIR__."/uploads/akte/".$old)) @unlink(__DIR__."/uploads/akte/".$old);
  }
}
$exists = $koneksi->prepare('SELECT COUNT(*) c FROM akte WHERE id_formulir=?');
$exists->bind_param('i',$id); $exists->execute(); $c=$exists->get_result()->fetch_assoc()['c'] ?? 0; $exists->close();
if($c>0){ $stmt = $koneksi->prepare('UPDATE akte SET file_akte=? WHERE id_formulir=?'); $stmt->bind_param('si',$akteName,$id); $stmt->execute(); $stmt->close(); }
else { $stmt = $koneksi->prepare('INSERT INTO akte (id_formulir,file_akte) VALUES (?,?)'); $stmt->bind_param('is',$id,$akteName); $stmt->execute(); $stmt->close(); }

// audit log update (before/after ringkas)
$before = $lama;
$after = [
  'nik'=>$nik,'nama'=>$nama,'tempat_lahir'=>$tempat_lahir,'tanggal_lahir'=>$tanggal_lahir,
  'jenis_kelamin'=>$jenis_kelamin,'alamat'=>$alamat,'rt'=>$rt,'rw'=>$rw,'agama'=>$agama,'pekerjaan'=>$pekerjaan,'status'=>$status,
  'foto'=>$fotoName,'ttd'=>$ttdName,'file_kk'=>$kkName,'file_akte'=>$akteName
];
log_audit($koneksi,'update','formulir',$id,$before,$after);

header('Location: output_ktp.php?id='.$id);
exit;
