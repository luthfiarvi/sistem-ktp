<?php
require_once __DIR__.'/config/koneksi.php';
require_once __DIR__.'/config/status.php';
// pastikan skema status ada agar RELASI CASCADE aktif
ensure_status_schema($koneksi);

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
$id = intval($_POST['id'] ?? 0);
if($id<=0){ die('Parameter tidak valid.'); }

// ambil data lama untuk audit dan hapus file
$sql = "SELECT f.*, (SELECT file_kk FROM kk WHERE id_formulir=f.id LIMIT 1) AS file_kk, (SELECT file_akte FROM akte WHERE id_formulir=f.id LIMIT 1) AS file_akte FROM formulir f WHERE f.id=?";
$stmt=$koneksi->prepare($sql); $stmt->bind_param('i',$id); $stmt->execute(); $lama=$stmt->get_result()->fetch_assoc(); $stmt->close();
if(!$lama){ header('Location: lihat.php'); exit; }

// hapus file fisik
if(!empty($lama['foto']) && is_file(__DIR__.'/uploads/foto/'.$lama['foto'])) @unlink(__DIR__.'/uploads/foto/'.$lama['foto']);
if(!empty($lama['file_kk']) && is_file(__DIR__.'/uploads/kk/'.$lama['file_kk'])) @unlink(__DIR__.'/uploads/kk/'.$lama['file_kk']);
if(!empty($lama['file_akte']) && is_file(__DIR__.'/uploads/akte/'.$lama['file_akte'])) @unlink(__DIR__.'/uploads/akte/'.$lama['file_akte']);

// hapus baris db (urutkan child lalu parent)
$koneksi->query("DELETE FROM status_history WHERE id_formulir=".(int)$id);
$koneksi->query("DELETE FROM kk WHERE id_formulir=".(int)$id);
$koneksi->query("DELETE FROM akte WHERE id_formulir=".(int)$id);
$koneksi->query("DELETE FROM formulir WHERE id=".(int)$id);

// audit
log_audit($koneksi,'delete','formulir',$id,$lama,['deleted'=>true]);

header('Location: lihat.php');
exit;
