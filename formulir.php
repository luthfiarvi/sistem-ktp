<?php require_once __DIR__.'/config/koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Input Formulir KTP - Kelurahan Munjul</title>
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
      <a class="nav-link active" href="formulir.php">Input</a>
      <a class="nav-link" href="lihat.php">Daftar</a>
    </div>
  </div>
</nav>

<div class="container">
  <div class="card">
    <h2 class="section-title">Formulir Pembuatan KTP</h2>
    <p class="note"><i class="fa-solid fa-circle-info"></i> Silakan isi seluruh data pribadi di bawah ini dengan lengkap dan unggah berkas persyaratan yang diminta.</p>

    <form method="post" action="simpan.php" enctype="multipart/form-data" autocomplete="off" style="margin-top: 1.5rem;">
      
      <!-- Data Personal -->
      <h3 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 1rem; border-bottom: 1px solid var(--border); padding-bottom: 0.5rem; color: var(--primary);">
        <i class="fa-solid fa-user"></i> Data Diri Pemohon
      </h3>
      <div class="row">
        <div>
          <label for="nik">Nomor Induk Kependudukan (NIK)</label>
          <input type="text" id="nik" name="nik" inputmode="numeric" minlength="8" maxlength="20" placeholder="Masukkan 16 digit NIK" required>
        </div>
        <div>
          <label for="nama">Nama Lengkap</label>
          <input type="text" id="nama" name="nama" placeholder="Masukkan nama sesuai Akte/KK" required>
        </div>
        <div>
          <label for="tempat_lahir">Tempat Lahir</label>
          <input type="text" id="tempat_lahir" name="tempat_lahir" placeholder="Kota atau Kabupaten tempat lahir" required>
        </div>
        <div>
          <label for="tanggal_lahir">Tanggal Lahir</label>
          <input type="date" id="tanggal_lahir" name="tanggal_lahir" required>
        </div>
        <div>
          <label for="jenis_kelamin">Jenis Kelamin</label>
          <select id="jenis_kelamin" name="jenis_kelamin" required>
            <option value="" disabled selected>Pilih Jenis Kelamin</option>
            <option value="L">Laki-laki</option>
            <option value="P">Perempuan</option>
          </select>
        </div>
        <div>
          <label for="agama">Agama</label>
          <select id="agama" name="agama" required>
            <option value="" disabled selected>Pilih Agama</option>
            <option value="Islam">Islam</option>
            <option value="Kristen">Kristen</option>
            <option value="Katolik">Katolik</option>
            <option value="Hindu">Hindu</option>
            <option value="Buddha">Buddha</option>
            <option value="Konghucu">Konghucu</option>
            <option value="Lainnya">Lainnya</option>
          </select>
        </div>
        <div>
          <label for="pekerjaan">Pekerjaan</label>
          <input type="text" id="pekerjaan" name="pekerjaan" placeholder="Contoh: Karyawan Swasta, Pelajar, dll." required>
        </div>
        <div>
          <label for="status">Status Perkawinan</label>
          <select id="status" name="status" required>
            <option value="" disabled selected>Pilih Status Perkawinan</option>
            <option value="Belum Kawin">Belum Kawin</option>
            <option value="Kawin">Kawin</option>
            <option value="Cerai Hidup">Cerai Hidup</option>
            <option value="Cerai Mati">Cerai Mati</option>
          </select>
        </div>
      </div>

      <!-- Alamat Lengkap -->
      <h3 style="font-size: 1.15rem; font-weight: 700; margin: 2rem 0 1rem; border-bottom: 1px solid var(--border); padding-bottom: 0.5rem; color: var(--primary);">
        <i class="fa-solid fa-map-location-dot"></i> Detail Alamat
      </h3>
      <div class="mt-3">
        <label for="alamat">Alamat Jalan / No. Rumah</label>
        <textarea id="alamat" name="alamat" rows="3" placeholder="Masukkan nama jalan, nomor rumah, dan RT/RW secara rinci" required></textarea>
      </div>

      <div class="row mt-3">
        <div>
          <label for="rt">Rukun Tetangga (RT)</label>
          <input type="text" id="rt" name="rt" inputmode="numeric" pattern="^[0-9]{1,3}$" maxlength="3" placeholder="Contoh: 001">
        </div>
        <div>
          <label for="rw">Rukun Warga (RW)</label>
          <input type="text" id="rw" name="rw" inputmode="numeric" pattern="^[0-9]{1,3}$" maxlength="3" placeholder="Contoh: 009">
        </div>
      </div>

      <!-- Dokumen Persyaratan -->
      <h3 style="font-size: 1.15rem; font-weight: 700; margin: 2rem 0 1rem; border-bottom: 1px solid var(--border); padding-bottom: 0.5rem; color: var(--primary);">
        <i class="fa-solid fa-file-arrow-up"></i> Berkas Persyaratan (Pendukung)
      </h3>
      <div class="row">
        <div>
          <label for="foto"><i class="fa-solid fa-image"></i> Pas Foto Resmi (JPG/PNG, maks 1.5MB)</label>
          <input type="file" id="foto" name="foto" accept="image/jpeg,image/png" required>
        </div>
        <div>
          <label for="kk"><i class="fa-solid fa-file-pdf"></i> Kartu Keluarga (PDF/JPG/PNG, maks 2MB)</label>
          <input type="file" id="kk" name="kk" accept="application/pdf,image/jpeg,image/png" required>
        </div>
        <div>
          <label for="akte"><i class="fa-solid fa-file-pdf"></i> Akte Kelahiran (PDF/JPG/PNG, maks 2MB)</label>
          <input type="file" id="akte" name="akte" accept="application/pdf,image/jpeg,image/png" required>
        </div>
      </div>
      <p class="note mt-2"><i class="fa-solid fa-triangle-exclamation"></i> Pastikan file unggahan Anda terlihat jelas dan berukuran di bawah batas yang ditentukan.</p>

      <!-- Tanda Tangan Digital -->
      <h3 style="font-size: 1.15rem; font-weight: 700; margin: 2rem 0 1rem; border-bottom: 1px solid var(--border); padding-bottom: 0.5rem; color: var(--primary);">
        <i class="fa-solid fa-signature"></i> Tanda Tangan Digital
      </h3>
      <div class="note" style="margin-bottom: 10px;">
        <i class="fa-solid fa-circle-question"></i> Silakan tanda tangani pada kotak canvas abu-abu di bawah ini menggunakan mouse atau layar sentuh (smartphone/tablet). Tekan tombol <b>Bersihkan</b> jika ingin mengulang kembali.
      </div>
      
      <div class="sigpad">
        <canvas id="ttdCanvas" width="450" height="180" style="width:100%; max-width:450px; height:180px;"></canvas>
        <div style="margin-top: 10px; display: flex; gap: 8px;">
          <button type="button" class="btn secondary" onclick="clearTtd()"><i class="fa-solid fa-eraser"></i> Bersihkan</button>
        </div>
        <input type="hidden" name="ttd_data" id="ttd_data">
      </div>

      <!-- Action Buttons -->
      <div class="mt-4" style="border-top: 1px solid var(--border); padding-top: 1.5rem; display: flex; gap: 10px; flex-wrap: wrap;">
        <button class="btn btn-pink" type="submit"><i class="fa-solid fa-floppy-disk"></i> Kirim Formulir</button>
        <a class="btn secondary" href="index.php"><i class="fa-solid fa-xmark"></i> Batal</a>
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

  // Saat submit, konversi ke PNG bila ada coretan
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
