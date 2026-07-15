<?php require_once __DIR__.'/config/koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Sistem Pembuatan KTP</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    /* Popup overlay & content */
    .popup { position: fixed; inset: 0; background: rgba(0,0,0,0.55); display: none; align-items: center; justify-content: center; z-index: 1000; }
    .popup-content { position: relative; background: #fff; border-radius: 12px; padding: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.25); }
    .close-btn { position: absolute; top: 4px; right: 10px; font-size: 22px; line-height: 1; cursor: pointer; color: #374151; }
    .close-btn:hover { color: #111827; }

    /* Video hero */
    #home-video{ position:relative; height:60vh; min-height:380px; overflow:hidden; }
    #home-video video{ position:absolute; inset:0; width:100%; height:100%; object-fit:cover; }
    #home-video .overlay{ position:relative; z-index:1; height:100%; display:flex; align-items:center; justify-content:center; background:linear-gradient( to bottom, rgba(0,0,0,.35), rgba(0,0,0,.25) ); }
    #home-video .overlay h1{ color:#fff; font-weight:800; letter-spacing:.5px; }

    .section-inner{ max-width:1100px; margin:0 auto; padding:24px 16px; }
    .centered-search{ display:none; }
  </style>
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
      <a class="nav-link" href="lihat.php">Lihat</a>
    </div>
  </div>
</nav>
<!-- Video hero -->
<section id="home-video">
  <video autoplay muted loop playsinline id="myVideo">
    <source src="https://timur.jakarta.go.id/frontend3/timelapse-video/kelurahan cipayung-.mp4" type="video/mp4">
  </video>
  <div class="overlay">
    <h1>Kelurahan Munjul - Jakarta Timur</h1>
  </div>
</section>

<!-- Sejarah section -->
<section class="footer-container" style="display:flex; justify-content:center; margin-top:24px;">
  <div class="section-inner">
    <div class="rows">
      <div class="container mt-4">
        <div class="row" style="display:flex; gap:24px; flex-wrap:wrap; align-items:flex-start;">
          <div class="col-md-6" style="flex:1 1 360px; min-width:280px;">
            <h2>Sejarah Munjul</h2>
            <hr>
            <div class="scrollable-content">
              <p>Desa Munjul adalah desa sebelah selatan kelurahan Cilangkap, kenapa di sebut kelurahan munjul, karena disana tepatnya di area MTsN 30 ada tanah munjul. Ketika pembuatan local untuk dijadikan sekolah(MTs) dan tanah tersebut di gali, tapi ternyata munjul lagi tanah, hingga membuat ramai menjadi perbincangan masyarakat. Maka diatas tanah tersebut tidak dijadikan bangunan. Sedangkan tanah yang munjul diberi ruang khusus dipagar dan diberi atap, inilah sekelumit sejarah kelurahan Munjul.</p>
              <i>Sumber: Toponimi Jakarta Timur, Sudin Kebudayaan Jaktim (2023)</i>
            </div>
          </div>
          <div class="col-md-6" style="flex:1 1 360px; min-width:280px;">
            <figure class="about-img">
              <img src="https://timur.jakarta.go.id/frontend3/Peta Website/Cipayung, Munjul.png" alt="Peta Munjul" style="width:100%; height:auto; border-radius:8px;">
            </figure>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Lokasi & Foto -->
<section>
  <div class="section-inner">
    <div class="rows">
      <div class="container mt-4">
        <div class="row" style="display:flex; gap:24px; flex-wrap:wrap; align-items:stretch;">
          <div class="col-md-6" style="flex:1 1 360px; min-width:280px;">
            <div class="p-3 mb-2 bg-light text-dark">
              <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3965.35182923103!2d106.889134176307!3d-6.348470377246073!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69ecc35a6da6f1%3A0xd6e29beb7936d9ff!2sKantor%20Lurah%20Munjul!5e0!3m2!1sid!2sus!4v1715917955381!5m2!1sid!2sus" width="100%" height="360" style="border:0; border-radius:8px;" allowfullscreen loading="lazy"></iframe>
            </div>
          </div>
          <div class="col-md-6" style="flex:1 1 360px; min-width:280px;">
            <div class="p-3 mb-2 bg-light text-dark">
              <img src="https://timur.jakarta.go.id/storage/kelurahan/9HzDjPtxOOxdta0xEPA790hkwNS1SFCW41wXpbn8.jpeg" alt="Kantor Lurah Munjul" style="width:100%; height:360px; object-fit:cover; border-radius:8px;">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<footer class="site-footer">
  <div class="inner">
    <span>Kelurahan Munjul &mdash; Jakarta Timur</span>
    <span>&copy; <?=date('Y')?> Sistem KTP</span>
  </div>
</footer>
<!-- Popup pertama dibuka -->
<div id="popup" class="popup" aria-hidden="true">
  <div class="popup-content">
    <span class="close-btn" onclick="closePopup()" aria-label="Tutup">&times;</span>
    <img src="https://timur.jakarta.go.id/ppid/files/251003-061322.jpg" alt="251003-061322.jpg" style="max-width:350px">
  </div>
  
</div>
<script>
  (function(){
    function showPopup(){ var el = document.getElementById('popup'); if (el) { el.style.display = 'flex'; el.setAttribute('aria-hidden','false'); } }
    window.closePopup = function(){ var el = document.getElementById('popup'); if (el) { el.style.display = 'none'; el.setAttribute('aria-hidden','true'); }
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
