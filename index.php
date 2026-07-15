<?php require_once __DIR__.'/config/koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Sistem Pembuatan KTP - Kelurahan Munjul</title>
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
      <a class="nav-link active" href="index.php">Beranda</a>
      <a class="nav-link" href="kelurahan.php">Profil</a>
      <a class="nav-link" href="formulir.php">Input</a>
      <a class="nav-link" href="lihat.php">Daftar</a>
    </div>
  </div>
</nav>

<!-- Video hero -->
<section id="home-video">
  <video autoplay muted loop playsinline id="myVideo">
    <source src="https://timur.jakarta.go.id/frontend3/timelapse-video/kelurahan cipayung-.mp4" type="video/mp4">
  </video>
  <div class="overlay">
    <h1>Kelurahan Munjul</h1>
    <p>Layanan Mandiri Pengajuan & Pembuatan Kartu Tanda Penduduk (KTP) Secara Cepat, Tepat, dan Transparan</p>
  </div>
</section>

<!-- Quick Action Cards Section -->
<div class="container">
  <div class="quick-access-wrapper">
    <div class="quick-card">
      <div class="icon-box"><i class="fa-solid fa-id-card"></i></div>
      <div>
        <h3>Buat KTP Baru</h3>
        <p>Ajukan pembuatan KTP baru dengan mengisi formulir digital dan mengunggah berkas pendukung secara online.</p>
      </div>
      <a href="formulir.php" class="btn btn-pink mt-auto">Mulai Pengajuan</a>
    </div>
    <div class="quick-card">
      <div class="icon-box"><i class="fa-solid fa-magnifying-glass"></i></div>
      <div>
        <h3>Cek Status & Daftar</h3>
        <p>Pantau status permohonan KTP Anda secara real-time dan lihat daftar pengajuan yang sedang diproses.</p>
      </div>
      <a href="lihat.php" class="btn secondary mt-auto">Lihat Daftar</a>
    </div>
    <div class="quick-card">
      <div class="icon-box"><i class="fa-solid fa-circle-info"></i></div>
      <div>
        <h3>Profil Kelurahan</h3>
        <p>Pelajari informasi wilayah, sejarah, peta administratif, serta data demografis Kelurahan Munjul.</p>
      </div>
      <a href="kelurahan.php" class="btn secondary mt-auto">Pelajari Profil</a>
    </div>
  </div>
</div>

<!-- Sejarah & Peta Section -->
<section class="container mt-4">
  <div class="card">
    <div class="row" style="display:flex; gap:2.5rem; flex-wrap:wrap; align-items:flex-start;">
      <div style="flex:1 1 450px; min-width:300px;">
        <h2 class="section-title">Sejarah Munjul</h2>
        <div style="color: var(--muted); margin-bottom: 1.5rem; text-align: justify; line-height: 1.8;">
          <p>Desa Munjul adalah desa sebelah selatan kelurahan Cilangkap, kenapa di sebut kelurahan munjul, karena disana tepatnya di area MTsN 30 ada tanah munjul. Ketika pembuatan local untuk dijadikan sekolah(MTs) dan tanah tersebut di gali, tapi ternyata munjul lagi tanah, hingga membuat ramai menjadi perbincangan masyarakat. Maka diatas tanah tersebut tidak dijadikan bangunan. Sedangkan tanah yang munjul diberi ruang khusus dipagar dan diberi atap, inilah sekelumit sejarah kelurahan Munjul.</p>
        </div>
        <i style="font-size: 0.85rem; color: var(--muted);"><i class="fa-solid fa-book-open"></i> Sumber: Toponimi Jakarta Timur, Sudin Kebudayaan Jaktim (2023)</i>
      </div>
      <div style="flex:1 1 350px; min-width:300px;">
        <figure style="overflow: hidden; border-radius: var(--radius-md); box-shadow: var(--shadow);">
          <img src="https://timur.jakarta.go.id/frontend3/Peta Website/Cipayung, Munjul.png" alt="Peta Munjul" style="width:100%; height:auto; transition: transform 0.3s;" onmouseover="this.style.transform='scale(1.03)'" onmouseout="this.style.transform='scale(1)'">
        </figure>
      </div>
    </div>
  </div>
</section>

<!-- Lokasi Kantor Kelurahan -->
<section class="container">
  <div class="card">
    <h2 class="section-title">Kantor Kelurahan Munjul</h2>
    <div class="row" style="display:flex; gap:2rem; flex-wrap:wrap; align-items:stretch;">
      <div style="flex:1 1 450px; min-width:300px;">
        <div style="overflow: hidden; border-radius: var(--radius-md); border: 1px solid var(--border); height: 350px; box-shadow: var(--shadow);">
          <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3965.35182923103!2d106.889134176307!3d-6.348470377246073!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69ecc35a6da6f1%3A0xd6e29beb7936d9ff!2sKantor%20Lurah%20Munjul!5e0!3m2!1sid!2sus!4v1715917955381!5m2!1sid!2sus" width="100%" height="100%" style="border:0;" allowfullscreen loading="lazy"></iframe>
        </div>
      </div>
      <div style="flex:1 1 350px; min-width:300px;">
        <div style="overflow: hidden; border-radius: var(--radius-md); border: 1px solid var(--border); height: 350px; box-shadow: var(--shadow);">
          <img src="https://timur.jakarta.go.id/storage/kelurahan/9HzDjPtxOOxdta0xEPA790hkwNS1SFCW41wXpbn8.jpeg" alt="Kantor Lurah Munjul" style="width:100%; height:100%; object-fit:cover;">
        </div>
      </div>
    </div>
  </div>
</section>

<footer class="site-footer">
  <div class="inner">
    <span><i class="fa-solid fa-building"></i> Kelurahan Munjul &mdash; Jakarta Timur</span>
    <span>&copy; <?=date('Y')?> Sistem Pelayanan KTP</span>
  </div>
</footer>

<!-- Popup pertama dibuka -->
<div id="popup" class="popup" aria-hidden="true">
  <div class="popup-content">
    <span class="close-btn" onclick="closePopup()" aria-label="Tutup">&times;</span>
    <img src="https://timur.jakarta.go.id/ppid/files/251003-061322.jpg" alt="251003-061322.jpg" style="max-width:350px; border-radius: var(--radius-sm);">
  </div>
</div>

<script>
  (function(){
    function showPopup(){ 
      var el = document.getElementById('popup'); 
      if (el) { 
        el.style.display = 'flex'; 
        el.setAttribute('aria-hidden','false'); 
      } 
    }
    window.closePopup = function(){ 
      var el = document.getElementById('popup'); 
      if (el) { 
        el.style.display = 'none'; 
        el.setAttribute('aria-hidden','true'); 
      }
      try { sessionStorage.setItem('popupSeen', '1'); } catch(e) {}
    };
    window.addEventListener('load', function(){
      try {
        if (!sessionStorage.getItem('popupSeen')) { showPopup(); }
      } catch(e) { showPopup(); }
    });
  })();
</script>
</body>
</html>
