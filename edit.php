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
  <title>Ubah Data KTP - Kelurahan Munjul</title>
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
    <h2 class="section-title">Ubah Data Formulir</h2>
    <p class="note"><i class="fa-solid fa-pen-to-square"></i> Perbarui data pribadi pemohon di bawah ini. Anda juga dapat mengganti berkas lampiran pendukung jika diperlukan.</p>

    <form method="post" action="update.php" enctype="multipart/form-data" autocomplete="off" style="margin-top: 1.5rem;">
      <input type="hidden" name="id" value="<?=$id?>">
      
      <!-- Data Personal -->
      <h3 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 1rem; border-bottom: 1px solid var(--border); padding-bottom: 0.5rem; color: var(--primary);">
        <i class="fa-solid fa-user"></i> Data Diri Pemohon
      </h3>
      <div class="row">
        <div>
          <label for="nik">Nomor Induk Kependudukan (NIK)</label>
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

      <!-- Alamat Lengkap -->
      <h3 style="font-size: 1.15rem; font-weight: 700; margin: 2rem 0 1rem; border-bottom: 1px solid var(--border); padding-bottom: 0.5rem; color: var(--primary);">
        <i class="fa-solid fa-map-location-dot"></i> Detail Alamat
      </h3>
      <div class="mt-3">
        <label for="alamat">Alamat Jalan / No. Rumah</label>
        <textarea id="alamat" name="alamat" rows="3" required><?=htmlspecialchars($data['alamat'])?></textarea>
      </div>

      <div class="row mt-3">
        <div>
          <label for="rt">Rukun Tetangga (RT)</label>
          <input type="text" id="rt" name="rt" inputmode="numeric" pattern="^[0-9]{1,3}$" maxlength="3" value="<?=htmlspecialchars($data['rt'] ?? '')?>" placeholder="01">
        </div>
        <div>
          <label for="rw">Rukun Warga (RW)</label>
          <input type="text" id="rw" name="rw" inputmode="numeric" pattern="^[0-9]{1,3}$" maxlength="3" value="<?=htmlspecialchars($data['rw'] ?? '')?>" placeholder="09">
        </div>
      </div>

      <!-- Dokumen Persyaratan -->
      <h3 style="font-size: 1.15rem; font-weight: 700; margin: 2rem 0 1rem; border-bottom: 1px solid var(--border); padding-bottom: 0.5rem; color: var(--primary);">
        <i class="fa-solid fa-file-arrow-up"></i> Berkas Persyaratan (Pendukung)
      </h3>
      <div class="row">
        <!-- Pas Foto -->
        <div>
          <label>Pas Foto Saat Ini</label>
          <?php if(!empty($data['foto'])): ?>
            <div style="margin: 0.5rem 0;"><img src="uploads/foto/<?=htmlspecialchars($data['foto'])?>" alt="Foto" style="max-width:140px; border:1px solid var(--border); border-radius: var(--radius-sm)"></div>
          <?php else: ?>
            <p class="note">Belum ada foto.</p>
          <?php endif; ?>
          <label for="foto" class="mt-2">Ganti Pas Foto (opsional)</label>
          <input type="file" id="foto" name="foto" accept="image/jpeg,image/png">
        </div>

        <!-- File KK -->
        <div>
          <label>File Kartu Keluarga (KK) Saat Ini</label>
          <?php if(!empty($data['file_kk'])): ?>
            <div class="note" style="margin: 0.5rem 0;">
              <a href="uploads/kk/<?=htmlspecialchars($data['file_kk'])?>" target="_blank"><i class="fa-solid fa-file-pdf"></i> <?=htmlspecialchars($data['file_kk'])?></a>
            </div>
          <?php else: ?>
            <p class="note">Belum ada file KK.</p>
          <?php endif; ?>
          <label for="kk" class="mt-2">Ganti File KK (opsional)</label>
          <input type="file" id="kk" name="kk" accept="application/pdf,image/jpeg,image/png">
        </div>

        <!-- File Akte -->
        <div>
          <label>File Akte Kelahiran Saat Ini</label>
          <?php if(!empty($data['file_akte'])): ?>
            <div class="note" style="margin: 0.5rem 0;">
              <a href="uploads/akte/<?=htmlspecialchars($data['file_akte'])?>" target="_blank"><i class="fa-solid fa-file-pdf"></i> <?=htmlspecialchars($data['file_akte'])?></a>
            </div>
          <?php else: ?>
            <p class="note">Belum ada file Akte.</p>
          <?php endif; ?>
          <label for="akte" class="mt-2">Ganti File Akte (opsional)</label>
          <input type="file" id="akte" name="akte" accept="application/pdf,image/jpeg,image/png">
        </div>
      </div>
      <p class="note mt-2"><i class="fa-solid fa-circle-info"></i> Kosongkan input unggah berkas jika tidak ingin mengganti berkas lama.</p>

      <!-- Tanda Tangan Digital -->
      <h3 style="font-size: 1.15rem; font-weight: 700; margin: 2rem 0 1rem; border-bottom: 1px solid var(--border); padding-bottom: 0.5rem; color: var(--primary);">
        <i class="fa-solid fa-signature"></i> Tanda Tangan Digital
      </h3>
      <?php if(!empty($data['ttd'])): ?>
        <div style="margin-bottom: 0.5rem;">
          <label>Tanda Tangan Saat Ini</label>
          <div style="background: #ffffff; padding: 10px; border: 1px solid var(--border); border-radius: var(--radius-sm); display: inline-block;">
            <img src="uploads/ttd/<?=htmlspecialchars($data['ttd'])?>" alt="TTD" style="max-width:200px; max-height: 80px;">
          </div>
        </div>
      <?php endif; ?>
      
      <div class="note mt-2">
        <i class="fa-solid fa-circle-question"></i> Anda dapat menggambar ulang tanda tangan pada kotak canvas di bawah untuk mengganti tanda tangan lama. Kosongkan jika tidak ingin mengubah.
      </div>
      
      <div class="sigpad">
        <canvas id="ttdCanvas" width="450" height="180" style="width:100%; max-width:450px; height:180px;"></canvas>
        <div style="margin-top: 10px; display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
          <button type="button" class="btn secondary" onclick="clearTtd()"><i class="fa-solid fa-eraser"></i> Bersihkan Canvas</button>
          <?php if(!empty($data['ttd'])): ?>
            <label style="display:inline-flex; align-items:center; gap:6px; margin: 0; cursor: pointer; font-weight: 600; font-size: 0.95rem;">
              <input type="checkbox" name="hapus_ttd" value="1" style="width: auto; margin-right: 6px;"> <i class="fa-solid fa-trash-can" style="color: var(--danger)"></i> Hapus tanda tangan saat ini
            </label>
          <?php endif; ?>
        </div>
        <input type="hidden" name="ttd_data" id="ttd_data">
      </div>

      <!-- Action Buttons -->
      <div class="mt-4" style="border-top: 1px solid var(--border); padding-top: 1.5rem; display: flex; gap: 10px; flex-wrap: wrap;">
        <button class="btn btn-pink" type="submit"><i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan</button>
        <a class="btn secondary" href="output_ktp.php?id=<?=$id?>"><i class="fa-solid fa-xmark"></i> Batal</a>
      </div>
    </form>
  </div>
</div>

<footer class="site-footer">
  <div class="inner">
    <span><i class="fa-solid fa-building"></i> Kelurahan Munjul &mdash; Jakarta Timur</span>
    <span>&copy; <?=date('Y')?> Sistem Pelayanan KTP</span>
  </div>
</footer>

<script>
// Canvas ttd sederhana
(function(){
  const cvs = document.getElementById('ttdCanvas');
  if(!cvs) return;
  const ctx = cvs.getContext('2d');
  let drawing = false, last = null, dirty = false;

  // Set style coretan canvas
  ctx.lineWidth = 3.5;
  ctx.lineCap = 'round';
  ctx.strokeStyle = '#0f172a';

  const getPos = (e) => {
    const r = cvs.getBoundingClientRect();
    if(e.touches && e.touches[0]){ 
      return { 
        x: e.touches[0].clientX - r.left, 
        y: e.touches[0].clientY - r.top 
      }; 
    }
    return { x: e.clientX - r.left, y: e.clientY - r.top };
  };

  const drawTo = (p) => {
    if(!last){ last = p; }
    ctx.beginPath(); 
    ctx.moveTo(last.x, last.y); 
    ctx.lineTo(p.x, p.y); 
    ctx.stroke(); 
    last = p; 
    dirty = true;
  };

  const start = (e) => { 
    drawing = true; 
    last = getPos(e); 
    e.preventDefault(); 
  };

  const move = (e) => { 
    if(!drawing) return; 
    drawTo(getPos(e)); 
    e.preventDefault(); 
  };

  const stop = () => { 
    drawing = false; 
    last = null; 
  };

  cvs.addEventListener('mousedown', start); 
  document.addEventListener('mousemove', move); 
  document.addEventListener('mouseup', stop);
  
  cvs.addEventListener('touchstart', start, {passive:false}); 
  cvs.addEventListener('touchmove', move, {passive:false}); 
  cvs.addEventListener('touchend', stop);

  window.clearTtd = function(){ 
    ctx.clearRect(0,0,cvs.width,cvs.height); 
    dirty = false; 
  };

  const form = cvs.closest('form');
  if(form){ 
    form.addEventListener('submit', function(){
      if(!dirty){ return; }
      const data = cvs.toDataURL('image/png');
      document.getElementById('ttd_data').value = data;
    }); 
  }
})();
</script>
</body>
</html>
