<?php require_once __DIR__.'/config/koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Input Formulir KTP</title>
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
    <h2 class="section-title">Formulir Pembuatan KTP</h2>

    <form method="post" action="simpan.php" enctype="multipart/form-data" autocomplete="off">
      <div class="row">
        <div>
          <label for="nik">NIK</label>
          <input type="text" id="nik" name="nik" inputmode="numeric" minlength="8" maxlength="20" placeholder="NIK" required>
        </div>
        <div>
          <label for="nama">Nama Lengkap</label>
          <input type="text" id="nama" name="nama" placeholder="Nama sesuai KTP" required>
        </div>
        <div>
          <label for="tempat_lahir">Tempat Lahir</label>
          <input type="text" id="tempat_lahir" name="tempat_lahir" placeholder="Kota/kabupaten" required>
        </div>
        <div>
          <label for="tanggal_lahir">Tanggal Lahir</label>
          <input type="date" id="tanggal_lahir" name="tanggal_lahir" required>
        </div>
        <div>
          <label for="jenis_kelamin">Jenis Kelamin</label>
          <select id="jenis_kelamin" name="jenis_kelamin" required>
            <option value="L">Laki-laki</option>
            <option value="P">Perempuan</option>
          </select>
        </div>
        <div>
          <label for="agama">Agama</label>
          <select id="agama" name="agama" required>
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
          <input type="text" id="pekerjaan" name="pekerjaan" placeholder="Pekerjaan" required>
        </div>
        <div>
          <label for="status">Status Perkawinan</label>
          <select id="status" name="status" required>
            <option value="Belum Kawin">Belum Kawin</option>
            <option value="Kawin">Kawin</option>
            <option value="Cerai Hidup">Cerai Hidup</option>
            <option value="Cerai Mati">Cerai Mati</option>
          </select>
        </div>
      </div>

      <div class="mt-3">
        <label for="alamat">Alamat</label>
        <textarea id="alamat" name="alamat" rows="3" placeholder="Nama jalan, No Rumah" required></textarea>
      </div>

      <div class="row">
        <div>
          <label for="rt">RT</label>
          <input type="text" id="rt" name="rt" inputmode="numeric" pattern="^[0-9]{1,3}$" maxlength="3" placeholder="01">
        </div>
        <div>
          <label for="rw">RW</label>
          <input type="text" id="rw" name="rw" inputmode="numeric" pattern="^[0-9]{1,3}$" maxlength="3" placeholder="09">
        </div>
      </div>

      <div class="mt-3">
        <h3 class="section-title">Berkas Pendukung</h3>
        <div class="row">
          <div>
            <label for="foto">Pas Foto (JPG/PNG, maks 1.5MB)</label>
            <input type="file" id="foto" name="foto" accept="image/jpeg,image/png" required>
          </div>
          <div>
            <label for="kk">Kartu Keluarga (PDF/JPG/PNG, maks 2MB)</label>
            <input type="file" id="kk" name="kk" accept="application/pdf,image/jpeg,image/png" required>
          </div>
          <div>
            <label for="akte">Akte Kelahiran (PDF/JPG/PNG, maks 2MB)</label>
            <input type="file" id="akte" name="akte" accept="application/pdf,image/jpeg,image/png" required>
          </div>
        </div>
        <p class="note mt-2">Catatan: pastikan ukuran file sesuai batas. Folder <code>uploads/</code> akan dibuat otomatis.</p>
      </div>

      <div class="mt-3">
        <h3 class="section-title">Tanda Tangan</h3>
        <div class="note" style="margin-bottom:8px">Tanda tangani pada canvas di bawah (boleh pakai mouse atau layar sentuh). Klik Bersihkan jika perlu ulang.</div>
        <div class="sigpad" style="border:1px solid var(--border); border-radius:10px; padding:10px; background:#fff">
          <canvas id="ttdCanvas" width="400" height="160" style="width:100%; max-width:420px; height:160px; border:1px dashed #6b7280; border-radius:6px; background:#fff; cursor:crosshair; background-image: repeating-linear-gradient(0deg, rgba(0,0,0,.04) 0, rgba(0,0,0,.04) 1px, transparent 1px, transparent 12px), repeating-linear-gradient(90deg, rgba(0,0,0,.03) 0, rgba(0,0,0,.03) 1px, transparent 1px, transparent 12px);"></canvas>
          <div style="margin-top:8px; display:flex; gap:8px; flex-wrap:wrap;">
            <button type="button" class="btn secondary" onclick="clearTtd()">Bersihkan</button>
          </div>
          <input type="hidden" name="ttd_data" id="ttd_data">
        </div>
      </div>

      <div class="mt-4">
        <button class="btn" type="submit">Simpan Formulir</button>
        <a class="btn secondary" href="index.php">Batal</a>
      </div>
    </form>
  </div>
</div>
<script>
// Canvas ttd sederhana
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

  // Saat submit, konversi ke PNG (dengan latar putih) bila ada coretan
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
