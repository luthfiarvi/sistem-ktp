<?php
require_once __DIR__.'/config/koneksi.php';
require_once __DIR__.'/config/status.php';

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    header('Location: lihat.php'); exit;
}

ensure_status_schema($koneksi);

$id = intval($_POST['id'] ?? 0);
$to = trim($_POST['to'] ?? '');
$catatan = trim($_POST['catatan'] ?? '');

if($id <= 0){ die('Parameter tidak valid.'); }

// Ambil status saat ini
$stmt = $koneksi->prepare('SELECT status_permohonan FROM formulir WHERE id=?');
$stmt->bind_param('i', $id);
$stmt->execute();
$currow = $stmt->get_result()->fetch_assoc();
$stmt->close();
if(!$currow){ die('Data tidak ditemukan.'); }

$allowed = ktp_status_stages();
if(!in_array($to, $allowed, true)){
    die('Status tujuan tidak dikenali.');
}

$current = $currow['status_permohonan'] ?: 'Diajukan';
$idxCur = array_search($current, $allowed, true);
$idxTo = array_search($to, $allowed, true);

// Batasi hanya maju (forward) atau tetap (idempotent)
if($idxTo === false || $idxCur === false || $idxTo < $idxCur){
    die('Perubahan status tidak diizinkan.');
}

// Update formulir
$stmt = $koneksi->prepare('UPDATE formulir SET status_permohonan=? WHERE id=?');
$stmt->bind_param('si', $to, $id);
if(!$stmt->execute()){
    die('Gagal memperbarui status: '.$stmt->error);
}
$stmt->close();

// Insert history
$stmt = $koneksi->prepare('INSERT INTO status_history (id_formulir,status,catatan) VALUES (?,?,?)');
$stmt->bind_param('iss', $id, $to, $catatan);
$stmt->execute();
$stmt->close();

$back = $_POST['back'] ?? ('output_ktp.php?id='.$id);
header('Location: '.$back);
exit;
?>

