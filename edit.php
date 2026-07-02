<?php
require_once __DIR__.'/config/koneksi.php';
$id = intval($_GET['id'] ?? 0);
if($id<=0){ die('Parameter tidak valid.'); }

$sql = "SELECT f.*, kk.file_kk, akte.file_akte
        FROM formulir f
        LEFT JOIN kk   ON kk.id_formulir   = f.id
        LEFT JOIN akte ON akte.id_formulir = f.id
        WHERE f.id = ?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
$stmt->close();
if(!$data){ die('Data tidak ditemukan.'); }
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Ubah Data KTP</title>
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
    <h2 class="section-title">Ubah Data Formulir</h2>

    <form method="post" action="update.php" enctype="multipart/form-data" autocomplete="off">
      <input type="hidden" name="id" value="<?=$id?>">
      <div class="row">
        <div>
          <label for="nik">NIK</label>
          <input type="text" id="nik" name="nik" inputmode="numeric" minlength="8" maxlength="20" value="<?=htmlspecialchars($data['nik'])?>" required>
        </div>
        <div>
          <label for="nama">Nama Lengkap</label>
          <input type="text" id="nama" name="nama" value="<?=htmlspecialchars($data['nama'])?>" required>
        </div>
        <div>
          <label for="tempat_lahir">Tempat Lahir</label>
          <input type="text" id="tempat_lahir" name="tempat_lahir" value="<?=htmlspecialchars($data['tempat_lahir'])?>" required>
        </div>
        <div>
          <label for="tanggal_lahir">Tanggal Lahir</label>
          <input type="date" id="tanggal_lahir" name="tanggal_lahir" value="<?=htmlspecialchars($data['tanggal_lahir'])?>" required>
        </div>
        <div>
          <label for="jenis_kelamin">Jenis Kelamin</label>
          <select id="jenis_kelamin" name="jenis_kelamin" required>
            <option value="L" <?=$data['jenis_kelamin']==='L'?'selected':''?>>Laki-laki</option>
            <option value="P" <?=$data['jenis_kelamin']==='P'?'selected':''?>>Perempuan</option>
          </select>
        </div>
        <div>
          <label for="agama">Agama</label>
          <select id="agama" name="agama" required>
            <?php
              $agamalist=['Islam','Kristen','Katolik','Hindu','Buddha','Konghucu','Lainnya'];
              foreach($agamalist as $ag){
                $sel = ($data['agama']===$ag)?'selected':'';
                echo "<option value=\"".htmlspecialchars($ag)."\" $sel>".htmlspecialchars($ag)."</option>";
              }
            ?>
          </select>
        </div>
        <div>
          <label for="pekerjaan">Pekerjaan</label>
          <input type="text" id="pekerjaan" name="pekerjaan" value="<?=htmlspecialchars($data['pekerjaan'])?>" required>
        </div>
        <div>
          <label for="status">Status Perkawinan</label>
          <select id="status" name="status" required>
            <?php
              $statusList=['Belum Kawin','Kawin','Cerai Hidup','Cerai Mati'];
              foreach($statusList as $st){
                $sel = ($data['status']===$st)?'selected':'';
                echo "<option value=\"".htmlspecialchars($st)."\" $sel>".htmlspecialchars($st)."</option>";
              }
            ?>
          </select>
        </div>
      </div>

      <div class="mt-3">
        <label for="alamat">Alamat</label>
        <textarea id="alamat" name="alamat" rows="3" required><?=htmlspecialchars($data['alamat'])?></textarea>
      </div>

      <div class="row">
        <div>
          <label for="rt">RT</label>
          <input type="text" id="rt" name="rt" inputmode="numeric" pattern="^[0-9]{1,3}$" maxlength="3" value="<?=htmlspecialchars($data['rt'] ?? '')?>" placeholder="01">
        </div>
        <div>
          <label for="rw">RW</label>
          <input type="text" id="rw" name="rw" inputmode="numeric" pattern="^[0-9]{1,3}$" maxlength="3" value="<?=htmlspecialchars($data['rw'] ?? '')?>" placeholder="09">
        </div>
      </div>

      <div class="mt-3">
        <h3 class="section-title">Berkas</h3>
        <div class="row">
          <div>
            <label>Pas Foto Saat Ini</label>
            <?php if(!empty($data['foto'])): ?>
              <div class="mt-2"><img src="uploads/foto/<?=htmlspecialchars($data['foto'])?>" alt="Foto" style="max-width:140px;border:1px solid #e5e7eb;border-radius:8px"></div>
            <?php else: ?>
              <p class="note">Belum ada foto.</p>
            <?php endif; ?>
            <label for="foto" class="mt-2">Ganti Pas Foto (opsional)</label>
            <input type="file" id="foto" name="foto" accept="image/jpeg,image/png">
          </div>
          <div>
            <label>File KK Saat Ini</label>
            <?php if(!empty($data['file_kk'])): ?>
              <p class="mt-2"><a href="uploads/kk/<?=htmlspecialchars($data['file_kk'])?>" target="_blank"><?=htmlspecialchars($data['file_kk'])?></a></p>
            <?php else: ?>
              <p class="note">Belum ada KK.</p>
            <?php endif; ?>
            <label for="kk" class="mt-2">Ganti KK (opsional)</label>
            <input type="file" id="kk" name="kk" accept="application/pdf,image/jpeg,image/png">
          </div>
          <div>
            <label>File Akte Saat Ini</label>
            <?php if(!empty($data['file_akte'])): ?>
              <p class="mt-2"><a href="uploads/akte/<?=htmlspecialchars($data['file_akte'])?>" target="_blank"><?=htmlspecialchars($data['file_akte'])?></a></p>
            <?php else: ?>
              <p class="note">Belum ada Akte.</p>
            <?php endif; ?>
            <label for="akte" class="mt-2">Ganti Akte (opsional)</label>
            <input type="file" id="akte" name="akte" accept="application/pdf,image/jpeg,image/png">
          </div>
        </div>
        <p class="note mt-2">Kosongkan input file jika tidak ingin mengganti berkas.</p>
      </div>

      <div class="mt-3">
        <h3 class="section-title">Tanda Tangan</h3>
        <?php if(!empty($data['ttd'])): ?>
          <div class="note">TTD saat ini:</div>
          <div class="mt-2"><img src="uploads/ttd/<?=htmlspecialchars($data['ttd'])?>" alt="TTD" style="max-width:200px;border:1px solid #e5e7eb;border-radius:8px;background:#fff"></div>
        <?php endif; ?>
        <div class="note mt-2">Anda dapat menggambar ulang di bawah ini. Kosongkan jika tidak ingin mengubah. Centang hapus untuk menghapus TTD yang ada.</div>
        <div class="sigpad" style="border:1px solid var(--border); border-radius:10px; padding:10px; background:#fff">
          <canvas id="ttdCanvas" width="400" height="160" style="width:100%; max-width:420px; height:160px; border:1px dashed #6b7280; border-radius:6px; background:#fff; cursor:crosshair; background-image: repeating-linear-gradient(0deg, rgba(0,0,0,.04) 0, rgba(0,0,0,.04) 1px, transparent 1px, transparent 12px), repeating-linear-gradient(90deg, rgba(0,0,0,.03) 0, rgba(0,0,0,.03) 1px, transparent 1px, transparent 12px);"></canvas>
          <div style="margin-top:8px; display:flex; gap:8px; flex-wrap:wrap;">
            <button type="button" class="btn secondary" onclick="clearTtd()">Bersihkan</button>
            <?php if(!empty($data['ttd'])): ?>
              <label style="display:inline-flex; align-items:center; gap:6px;">
                <input type="checkbox" name="hapus_ttd" value="1"> Hapus TTD yang ada
              </label>
            <?php endif; ?>
          </div>
          <input type="hidden" name="ttd_data" id="ttd_data">
        </div>
      </div>

      <div class="mt-4">
        <button class="btn" type="submit">Simpan Perubahan</button>
        <a class="btn secondary" href="output_ktp.php?id=<?=$id?>">Batal</a>
      </div>
    </form>
  </div>
</div>
<script>
(function(){
  const cvs = document.getElementById('ttdCanvas');
  if(!cvs) return;
  const ctx = cvs.getContext('2d');
  let drawing = false, last = null, dirty=false;

  const getPos = (e) => {
    const r = cvs.getBoundingClientRect();
    if(e.touches && e.touches[0]){ return { x: e.touches[0].clientX - r.left, y: e.touches[0].clientY - r.top }; }
    return { x: e.clientX - r.left, y: e.clientY - r.top };
  };
  const drawTo = (p) => {
    ctx.lineWidth = 3.2; ctx.lineCap = 'round'; ctx.strokeStyle = '#6b7280';
    if(!last){ last = p; }
    ctx.beginPath(); ctx.moveTo(last.x, last.y); ctx.lineTo(p.x, p.y); ctx.stroke(); last = p; dirty = true;
  };
  const start = (e)=>{ drawing = true; last = getPos(e); e.preventDefault(); };
  const move  = (e)=>{ if(!drawing) return; drawTo(getPos(e)); e.preventDefault(); };
  const stop  = ()=>{ drawing = false; last = null; };

  cvs.addEventListener('mousedown', start); document.addEventListener('mousemove', move); document.addEventListener('mouseup', stop);
  cvs.addEventListener('touchstart', start, {passive:false}); cvs.addEventListener('touchmove', move, {passive:false}); cvs.addEventListener('touchend', stop);

  window.clearTtd = function(){ ctx.clearRect(0,0,cvs.width,cvs.height); dirty=false; };

  const form = cvs.closest('form');
  if(form){ form.addEventListener('submit', function(){
    if(!dirty){ return; }
    // Simpan transparan (tanpa latar putih)
    const data = cvs.toDataURL('image/png');
    document.getElementById('ttd_data').value = data;
  }); }
})();
</script>

</body>
</html>
