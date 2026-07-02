<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

  <title>Member | Jakarta Film Commission</title>
<meta name="description" content="Join Jakarta Film Commission as a member and enjoy exclusive benefits, industry connections, and curated opportunities to support your film production journey in Jakarta.">
<meta name="keywords" content="Jakarta Film Commission membership, film production support Jakarta, exclusive member benefits, film industry connections, Jakarta film opportunities">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

 <!-- Icon -->
 <link rel="icon" type="image/x-icon" href="assets\icon\logo&icon.ico">
 <link rel="shortcut icon" href="assets\icon\logo&icon.ico">
  <link rel="icon" type="image/png" href="assets/icon/JakartaFilmCommissionLogo-9.ico">
 <!-- Link Swiper's CSS -->
 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
 <!-- Google tag (gtag.js) -->
 <script async src="https://www.googletagmanager.com/gtag/js?id=G-V0S9GRM6LS"></script>

 <!-- internal resources -->
 <link rel="stylesheet" href="assets/css/custom.css">

<style>


/* HERO */

.hero{
    background:
    linear-gradient(
    rgba(0,0,0,.65),
    rgba(0,0,0,.65)),
    url('gallery/cover/promotion.webp');

    background-size:cover;
    background-position:center;

    min-height:550px;

    display:flex;
    align-items:center;
}

.hero h1{
    color:#fff;
    font-size:56px;
    font-weight:700;
}

.hero p{
    color:#fff;
    font-size:20px;
    max-width:700px;
}

.btn-orange{
    background:#FFC72C;
    border:none;
    color:#111;
    padding:14px 30px;
    border-radius:10px;
    font-weight:600;
}

.btn-orange:hover{
    background:#e94c13;
    color:#fff;
}

/* SECTION */

.section-title{
    font-size:34px;
    font-weight:700;
    margin-bottom:15px;
}

.section-subtitle{
    color:#666;
    max-width:700px;
    margin:auto;
}

/* Member TYPE */

.package-card{
    background:#fff;
    border-radius:20px;
    overflow:hidden;
    box-shadow:0 5px 20px rgba(0,0,0,.08);
}

.package-header{
    background:#ff5a1f;
    color:white;
    padding-top: 8px;
    padding-bottom:1px;
    text-align:center;
}

.package-price{
    font-size:40px;
    font-weight:bold;
}

.package-body{
    padding:30px;
}

.package-body ul{
    padding-left:20px;
}

.package-body li{
    margin-bottom:10px;
}

/* CTA */

.cta{
    background:#111;
    color:#fff;
    padding:80px 0;
}

.cta h2{
    font-size:42px;
    font-weight:700;
}

/* FORM */

.register-card{
    background:#DA291C;
    border-radius:20px;
    padding:30px;
    box-shadow:0 5px 20px rgba(0,0,0,.08);
}

/* Member SELECTOR */

.membership-badge{
    background:#ff5a1f;
    color:#fff;
    padding:8px 16px;
    border-radius:30px;
    font-size:12px;
    font-weight:700;
    letter-spacing:1px;
}

.membership-option{
    border-radius:20px;
    background-color: #FF6A14;
    padding:15px;
    height:100%;
    border:1px solid #eee;
    transition:.35s ease;
    position:relative;
    overflow:hidden;
}

.membership-option:hover{
    transform:translateY(-10px);
    box-shadow:0 20px 40px rgba(0,0,0,.12);
}

.membership-option::before{
    content:"";
    position:absolute;
    top:0;
    left:-100%;
    width:100%;
    height:100%;
    background:
    linear-gradient(
        120deg,
        transparent,
        rgba(255,255,255,.35),
        transparent
    );
    transition:.8s;
}

.membership-option:hover::before{
    left:100%;
}

.membership-icon{
    width:80px;
    height:80px;
    margin:auto;
    margin-bottom:20px;
    border-radius:50%;
    background:#fff2ed;
    display:flex;
    align-items:center;
    justify-content:center;
}

.membership-icon i{
    font-size:34px;
    color:#ff5a1f;
}

.membership-option h4{
    font-weight:700;
    margin-bottom:15px;
}

.membership-option p{
    color:#666;
    min-height:80px;
}

.btn-orange{
    transition:.3s;
}

.btn-orange:hover{
    transform:translateY(-3px);
}

.btn-dark{
    font-weight:600;    
    padding:14px 30px;
    background-color: #97B300;
    border: none;
    border-radius:10px;
    transition:.3s;
}

.btn-dark:hover{
    background-color: #526E3E;
    transform:translateY(-3px);
}

.benefit-badge{
    background:#ff5a1f;
    color:#fff;
    padding:8px 20px;
    border-radius:50px;
    font-weight:600;
}

.highlight-box{
    background:linear-gradient(
        135deg,
        #ff8a00,
        #e53900
    );
    color:white;
    padding:50px;
    border-radius:25px;
}

.highlight-box h2{
    font-size:54px;
    font-weight:800;
    line-height:1.1;
}

.highlight-label{
    display:inline-block;
    background:rgba(255,255,255,.2);
    padding:8px 15px;
    border-radius:50px;
    margin-bottom:20px;
}

.benefit-showcase{
    background:white;
    border-radius:20px;
    overflow:hidden;
    box-shadow:0 5px 25px rgba(0,0,0,.08);
    transition:.3s;
    height:100%;
}

.benefit-showcase:hover{
    transform:translateY(-8px);
}

.showcase-img{
    width:100%;
    height:240px;
    object-fit:cover;
}

.showcase-body{
    padding:25px;
}

.showcase-body h4{
    font-weight:700;
}

.cinema-banner{
    background:linear-gradient(
        135deg,
        #ff8a00,
        #d40000
    );
    color:white;
    padding:40px;
    border-radius:25px;
}

.cinema-banner h2{
    font-size:42px;
    font-weight:800;
}

.cinema-icon{
    font-size:80px;
}

.benefit-wrapper{
    display:flex;
    overflow:hidden;
    border-radius:25px;
    box-shadow:0 15px 40px rgba(0,0,0,.12);
}

.benefit-main{
    flex:2;
    min-height:500px;

    padding:60px;

    display:flex;
    flex-direction:column;
    justify-content:center;

    background:
    linear-gradient(
        135deg,
        #ff8a00,
        #ff5a1f,
        #1f1f1f
    );

    color:white;
}

.benefit-tag{
    display:inline-block;

    background:rgba(255,255,255,.15);

    padding:10px 20px;

    border-radius:50px;

    margin-bottom:20px;

    font-weight:600;

    width:max-content;
}

.benefit-main h2{
    font-size:64px;
    font-weight:800;
    line-height:1;
    margin-bottom:20px;
}

.benefit-main p{
    font-size:20px;
    opacity:.9;
}

.benefit-list{
    flex:1;
    background:#eee;

    display:flex;
    flex-direction:column;
}
@media (max-width:768px){

    .benefit-wrapper{
        flex-direction:column;
    }

}

.mini-benefit{
    flex:1;

    display:flex;
    align-items:center;

    gap:20px;

    padding:20px;

    border-bottom:1px solid #eee;

    transition:.3s;
}

.mini-benefit:last-child{
    border-bottom:none;
}

.mini-benefit:hover{
    background:#DA291C;
    transform:translateX(5px);
}

.mini-benefit:hover span{
    color: #FFC72C;
    transform:translateX(5px);
}

.mini-benefit img{
    width:120px;
    height:100px;

    object-fit:cover;

    border-radius:12px;
}

.mini-benefit span{
    font-size:22px;
    font-weight:700;
    color:#222;
}
.benefit-main{
    background:
    linear-gradient(
        rgba(255,90,31,.9),
        rgba(20,20,20,.95)
    ),
    url('gallery/cover/promotion.webp');

    background-size:cover;
    background-position:center;
}

.cinema-banner{

    position:relative;

    min-height:350px;

    border-radius:25px;

    overflow:hidden;

    background:
    linear-gradient(
        rgba(255,90,31,.75),
        rgba(20,20,20,.85)
    ),
    url('gallery/cover/sinar-mas-land-menyelenggarakan-acara-nonton-bareng-nobar-film-_181124204857-746.jpg');

    background-size:cover;
    background-position:center;

    box-shadow:
    0 15px 40px rgba(0,0,0,.15);
}

.cinema-overlay{

    min-height:350px;

    display:flex;
    flex-direction:column;
    justify-content:center;

    padding:60px;

    color:white;
}

.cinema-badge{

    display:inline-block;

    width:max-content;

    background:rgba(255,255,255,.15);

    backdrop-filter:blur(5px);

    padding:10px 18px;

    border-radius:50px;

    margin-bottom:20px;

    font-weight:600;
}

.cinema-overlay h2{

    font-size:54px;
    font-weight:800;

    line-height:1.1;
    color: white;

    margin-bottom:20px;
}

.cinema-overlay p{

    font-size:18px;

    max-width:600px;

    margin:0;

    opacity:.95;
}

@media(max-width:768px){

    .cinema-overlay{
        padding:40px 25px;
    }

    .cinema-overlay h2{
        font-size:34px;
    }

}

.service-card{

    position:relative;

    min-height:550px;
    height: 100%;

    border-radius:25px;

    overflow:hidden;

    background-size:cover;
    background-position:center;

    box-shadow:
    0 15px 40px rgba(0,0,0,.15);
}

.scouting-card{

    background:
    linear-gradient(
        rgba(255,90,31,.75),
        rgba(20,20,20,.85)
    ),
    url('gallery/cover/jakarta-malam-ramai.webp');
     background-size: cover;
    background-position: center;
}

.production-card{

    background:
    linear-gradient(
        rgba(255,90,31,.75),
        rgba(20,20,20,.85)
    ),
    url('gallery/cover/crew-film-warm.webp');
    
     background-size: cover;
    background-position: center;
}

.service-overlay{

    padding:50px;

    color:white;

    height:100%;

    display:flex;
    flex-direction:column;
    justify-content:flex-start;
}

.service-overlay h2{

    font-size:42px;
    font-weight:800;
    color: white;

    margin-bottom:25px;
}

.service-overlay ul{

    list-style:none;
    padding:0;
    margin:0;
}

.service-overlay li{

    margin-bottom:12px;

    font-size:18px;
}

.point {
  color: #ffffffb0; 
  padding-left: 20px;
  font-size: 70%;
}

.card-tnc {
  margin-top: auto;
  display: flex;
  font-style: italic;
  font-size: 60%;
  justify-content: flex-end; 
}

        /* Accent Section */
        .accent-section {
            background: linear-gradient(135deg, #ff5a1f 0%, #ff5a1f 100%);
            border-radius: 1rem;
            padding: 3rem;
            text-align: center;
            color: white;
            margin: 3rem 0;
        }

        .accent-section h3 {
            color: white;
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        .accent-section p {
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 1.5rem;
        }


        /* Programs Section */
        .programs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .program-card {
            border: 1px solid #221F1F;
            background-color: #F8F0DF;
            border-radius: 0.75rem;
            padding: 2rem;
            transition: all 0.3s ease-out;
        }

        .program-card:hover {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border-color: #ff5a1f;
        }

        .program-icon {
            width: 48px;
            height: 48px;
            background: rgba(212, 175, 55, 0.1);
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }

        .program-icon::after {
            content: '';
            width: 24px;
            height: 24px;
            background: #ff5a1f;
            border-radius: 50%;
        }

        .program-card h3 {
            margin-bottom: 0.5rem;
            color: #DA291C;
        }

        .program-card .subtitle {
            color: #96B300;
            font-size: 0.875rem;
            margin-bottom: 1rem;
        }

        .program-card ul {
            list-style: none;
            color: #526D3D;
        }

        .program-card li {
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
            display: flex;
            gap: 0.5rem;
        }

        .program-card li::before {
            content: '•';
            color: #ff5a1f;
            font-weight: bold;
        }


        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        @media (min-width: 768px) {
            .container {
                padding: 0 1.5rem;
            }
        }

        @media (min-width: 1024px) {
            .container {
                padding: 0 2rem;
            }
        }
        
        
        /* Benefits Table */
        .benefits-table {
            width: 100%;
            color: #070707;
            border-collapse: collapse;
            background: #F8F0DF;
            border-radius: 0.75rem;
            overflow: hidden;
        }

        .benefits-table thead {
            background: linear-gradient(135deg, #ff5a1f 0%, #ff5a1f 100%);
            color: white;
        }

        .benefits-table th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
        }

        .benefits-table td {
            padding: 1rem;
            border-bottom: 1px solid #070707;
        }

        .benefits-table tbody tr:nth-child(even) {
            background-color: #fff3f3;
            /* color: #DA291C; */
        }

        .benefits-table .checkmark {
            text-align: center;
            font-weight: bold;
            font-size: 1.5rem;
            color: #ff5a1f;
        }

        .benefits-table .empty {
            color: #DA291C;
        }

        /* Icon Button Styling */
        .icon-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 5px;
            width: 40px;
            height: 40px;
            background: #F8F0DF;
            border: 1px solid #070707;
            color: white;
            border-radius: 20%;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1.25rem;
            margin-top: 1rem;
        }

        .icon-button:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(15, 58, 125, 0.3);
        }

        .icon-button:active {
            transform: scale(0.95);
        }

        /* Popup Styling */
        .popup-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .popup-overlay.active {
            display: flex;
        }

        .popup-bubble {
            background: white;
            border-radius: 1rem;
            padding: 2rem;
            max-width: 400px;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            animation: popupSlideIn 0.3s ease-out;
        }

        @keyframes popupSlideIn {
            from {
                opacity: 0;
                transform: scale(0.9) translateY(-20px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .popup-bubble h3 {
            margin-bottom: 0.5rem;
            color: #ff5a1f;
        }

        .popup-bubble p {
            color: #666;
            margin-bottom: 1.5rem;
        }

        .popup-close-btn {
            background: #ff5a1f;
            color: #fff;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.2s;
        }

        .popup-close-btn:hover {
            background: #ff5a1f;
        }

</style>
</head>
<body style="background-color: #F8F0DF;">
<!-- LOADER -->
  <div id="loader-container"></div>
  
  <!-- Hamburger Menu -->
  <div class="menu-overlay" id="menuOverlay"></div>

  <!-- WHATSAPP -->
  <!-- <div id="whatsapp-container"></div> -->
  
  <!-- NAVBAR -->
  <div id="navbar"></div>

<!-- Popup Bubble untuk Coming Soon -->
<div id="comingSoonPopup" class="popup-overlay">
    <div class="popup-bubble">
        <h3>Coming Soon</h3>
        <p>Fitur ini akan segera tersedia. Terima kasih atas kesabaran Anda!</p>
        <button class="popup-close-btn" onclick="closePopup()">Tutup</button>
    </div>
</div>
    
<!-- HERO -->
<section class="hero" style="background-color: #F8F0DF; padding-top: 100px; padding-bottom: 100px;">
    <div class="container">

        <h1>
            Become a Member of
            <br>
            Jakarta Film Commission
        </h1>

        <p>
            Dapatkan akses ke berbagai manfaat eksklusif, koneksi industri, dan peluang terkurasi untuk mempermudah perjalanan produksi Anda di Jakarta, mulai dari pra-produksi, produksi, hingga promosi
        </p>

        <a href="#register" class="btn btn-orange mt-3">
            Join Member
        </a>

    </div>
</section>

<!-- Programs Section -->
<!-- <section id="programs" style="margin-top: 50px; background-color: #F8F0DF; padding-top: 50px;">
        <div class="container">
            <h2 style="color: #ff5a1f;">JFC Programs</h2>
            <p style="color:#DA291C">Dukungan Komprehensif dalam Lima Pilar Utama Produksi dan Promosi Film di Jakarta</p>

            <div class="accent-section">
                <h3>Five Pillars of Film Ecosystem</h3>
                <p>Film Production • Talent Ecosystem • Production Facilities • City Storytelling • International Promotion</p>
            </div>

            <div class="programs-grid">
                <div class="program-card">
                    <div class="program-icon"></div>
                    <h3>One Stop Film Service</h3>
                    <p class="subtitle">Mempermudah proses produksi melalui Filming in Jakarta</p>
                    <ul>
                        <li>Koordinasi perizinan produksi lintas OPD melalui layanan terintegrasi</li>
                        <li>DatDatabase dan kurasi lokasi syuting Jakarta, dari landmark ikonik hingga kampung kota</li>
                        <li>Standarisasi biaya, prosedur, dan waktu layanan yang transparan</li>
                        <li>Menjadikan Jakarta sebagai kota yang ramah dan kompetitif bagi produksi nasional maupun internasional</li>
                    </ul>
                    <button class="icon-button" onclick="redirectTo('https://filminginjakarta.com/' )" title="Filming In Jakarta">
                        <img src="assets/icon/fij&black&orange.ico">
                    </button>
                </div>

                <div class="program-card">
                    <div class="program-icon"></div>
                    <h3>Pengembangan Talenta & Ekosistem Industri</h3>
                    <p class="subtitle">Menyiapkan talenta dan tenaga profesional perfilman</p>
                    <ul>
                        <li>Program pengembangan talenta film Jakarta</li>
                        <li>Peningkatan kompetensi tenaga kerja perfilman melalui pelatihan dan sertifikasi profesi</li>
                        <li>Penguatan jejaring antara industri, perguruan tinggi, sekolah, dan komunitas film</li>
                        <li>Penciptaan jalur karier yang lebih terbuka bagi talenta lokal</li>
                    </ul>
                    <button class="icon-button" onclick="showComingSoon()" title="Coming Soon">
                        <img src="assets/icon/Asset24.ico">
                    </button>                    
                    <button class="icon-button" onclick="showComingSoon()" title="Coming Soon">
                        <img src="assets/icon/Asset21.ico">
                    </button>
                    <button class="icon-button" onclick="showComingSoon()" title="Coming Soon">
                        <img src="assets/icon/Asset20.ico">
                    </button>
                </div>

                <div class="program-card">
                    <div class="program-icon"></div>
                    <h3>Dukungan Produksi & Insentif</h3>
                    <p class="subtitle">Production support and incentives</p>
                    <ul>
                        <li>Fasilitasi dukungan non-fiskal berupa akses lokasi, logistik, dan promosi</li>
                        <li>Pengembangan skema insentif untuk meningkatkan daya saing Jakarta sebagai lokasi produksi</li>
                        <li>Dukungan bagi karya yang mengangkat Jakarta sebagai bagian dari narasi cerita</li>
                        <li>Kemitraan dengan industri pendukung untuk menciptakan efisiensi biaya produksi</li>
                    </ul>
                </div>

                <div class="program-card">
                    <div class="program-icon"></div>
                    <h3>Akses Publik & Literasi Film</h3>
                    <p class="subtitle">Meningkatkan akses dan partisipasi masyarakat dalam ekosistem perfilman</p>
                    <ul>
                        <li>Meningkatkan literasi dan apresiasi masyarakat terhadap film</li>
                        <li>Mengaktifkan ruang publik sebagai ruang kreasi, produksi, dan pemutaran film</li>
                        <li>MemMemperluas akses masyarakat terhadap karya dan kegiatan perfilman</li>
                        <li>Mendorong partisipasi warga dalam ekosistem kreatif kota</li>
                    </ul>
                    
                    <button class="icon-button" onclick="showComingSoon()" title="Coming Soon">
                        <img src="assets/icon/Asset23.ico">
                    </button>                    
                    <button class="icon-button" onclick="showComingSoon()" title="Coming Soon">
                        <img src="assets/icon/Asset22.ico">
                    </button>
                </div>

                <div class="program-card">
                    <div class="program-icon"></div>
                    <h3>Promosi & Diplomasi Budaya</h3>
                    <p class="subtitle">Memperkuat posisi Jakarta di panggung perfilman dunia</p>
                    <ul>
                        <li>Memanfaatkan film sebagai medium promosi Jakarta Kota Global</li>
                        <li>Menghadirkan Jakarta secara strategis di festival dan pasar film internasional</li>
                        <li>Membangun kolaborasi dengan film commission, institusi, dan pelaku industri global</li>
                        <li>Mendorong pertukaran budaya dan kerja sama produksi lintas negara</li>
                    </ul>
                </div>
            </div>
        </div>
    </section> -->

    


<!-- member BENEFITS -->
<section class="py-5" style="background-color: #F8F0DF;">

    <div class="container">

        <div class="text-center mb-5">

            <span class="benefit-badge">
                Member Exclusive Benefits
            </span>

            <h2 class="section-title mt-3" style="color: #070707;">
                Your Production, Better Supported
            </h2>

            <p class="section-subtitle">
                Nikmati berbagai kesempatan promosi eksklusif yang hanya tersedia bagi member Jakarta Film Commission.
            </p>

        </div>
    </div>
</section>

    <!-- Benefit 1 -->
    <section class="py-5" style="margin-top: -100px; background-color: #F8F0DF;" >
        <div class="container">
            <div class="benefit-wrapper">

                <!-- LEFT SIDE -->
                <div class="benefit-main">

                    <span class="benefit-tag">
                        Promotion
                    </span>

                    <h2 style="color: white;">
                        FREE UP TO SEVEN DAYS
                        <br>
                        FILM PROMOTION
                    </h2>

                    <p>
                        Gain wider exposure for your films through JXB’s 
                        OOH advertising network across Jakarta’s key hotspots.
                    </p>

                    <div class="card-tnc">
                        Terms and Condition Applied
                    </div>

                </div>

                <!-- RIGHT SIDE -->

                <div class="benefit-list">

                    <div class="mini-benefit">

                        <img
                        src="gallery/cover/jxbpromotion.webp"
                        alt="Mobil Kiosk">

                        <span>
                            Mobil Kiosk
                        </span>

                    </div>

                    <div class="mini-benefit">

                        <img
                        src="gallery/cover/jakarta-street-experience.png"
                        alt="Jakarta Street Experience">

                        <span>
                            Jakarta Street Experience
                        </span>

                    </div>

                    <div class="mini-benefit">

                        <img
                        src="gallery/cover/jxb-vending.jpg"
                        alt="JXB Vending">

                        <span>
                            JXB Vending Machine
                        </span>

                    </div>

                    <!-- <div class="mini-benefit">

                        <img
                        src="assets/cover/sinar-mas-land-menyelenggarakan-acara-nonton-bareng-nobar-film-_181124204857-746.jpg"
                        alt="Nobar Bioskop">

                        <span>
                            Nobar Bioskop
                        </span>

                    </div> -->

                </div>
            </div>
        </div>

        <!-- Bottom Banner -->
        <div class="container">
            <div class="cinema-banner mt-5">

                <div class="cinema-overlay">

                    <span class="cinema-badge">
                        Promotion
                    </span>

                    <h2>
                        FREE MOVIE SCREENING
                        <br>
                        AT CINEMA
                    </h2>

                    <p>
                        For selected films with educational value, 
                        subject to Jakarta Film Commission's curation and program availability.
                    </p>

                </div>

                <div class="card-tnc">
                    Terms and Condition Applied
                </div>
            </div>
        </div>
    </section>

    
<section class="service-benefits py-5" style="background-color: #F8F0DF;">
    <div class="container">
        <div class="row g-4 align-items-stretch">

            <!-- Location Scouting -->
            <div class="col-lg-6">
                <div class="service-card scouting-card">
                    <div class="service-overlay">
                        <span class="cinema-badge">
                        Pre-Production
                        </span>

                        <h2>
                            LOCATION SCOUTING
                        </h2>

                        <ul>
                            <li>
                                National & International Production House <br>
                                <span class="point">*  Free 1 night accomodation & transportation for 4 pax</span><br>
                                <span class="point">*  Free airport transport</span><br>                                
                                <span class="point">*  Free on-site assistance</span>
                                
                            </li>
                            <li>
                                Jakarta-based Production House <br>
                                <span class="point">*  Free transportation for 4 pax</span><br>
                                <span class="point">*  Free on-site assistance</span>
                            </li>
                            
                        </ul>

                        <div class="card-tnc">
                            Terms and Condition Applied
                        </div>

                    </div>
                </div>
            </div>

            <!-- Production Service -->
            <div class="col-lg-6">
                <div class="service-card production-card">
                    <div class="service-overlay">
                        <span class="cinema-badge">
                        Production
                    </span>

                        <h2>
                            PRODUCTION SERVICES
                        </h2>

                        <ul>
                            <li>
                                Up to 50% Off Selected Hotel Partners
                                <!-- National & International Production House <br>
                                <span class="point">*  Tavia Heritage, Superior Duluxe</span><br>
                                <span class="point">*  5 rooms</span><br>                                
                                <span class="point">*  Include breakfast</span><br>                                
                                <span class="point">*  pendampingan location permits</span><br>
                                <span class="point">*  supporting facilities oleh Pemprov DKI Jakarta</span><br> -->
                            </li>
                            <li>
                                Access to Selected Supporting Facilities by the Jakarta Provincial Government
                            </li>

                        </ul>

                        <div class="card-tnc">
                            Terms and Condition Applied
                        </div>
                        
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>


<!-- Member TYPE -->
<!-- <section class="py-5 bg-light">
    <div class="container">

        <div class="text-center mb-5">

            <h2 class="section-title">
                Member Categories
            </h2>

        </div>

        <div class="row g-4 justify-content-center">

            <div class="col-md-5 col-lg-4">
                <div class="package-card">

                    <div class="package-header">
                        <h3 style="background:#ff5a1f; color:white; text-align:center;">
                            <strong>Individual</strong>
                        </h3>
                    </div>

                    <div class="package-body">
                        <ul>
                            <li>Active Industry Practitioner</li>
                            <li>Emerging Filmmakers</li>
                            <li>Students</li>
                            <li>Film Enthusiast</li>
                            <li>Others</li>
                        </ul>
                    </div>

                </div>
            </div>

            <div class="col-md-5 col-lg-4">
                <div class="package-card">

                    <div class="package-header">
                        <h3 style="background:#ff5a1f; color:white; text-align:center;">
                            <strong>Entities</strong>
                        </h3>
                    </div>

                    <div class="package-body">
                        <ul>
                            <li>Production House</li>
                            <li>Academic Institution</li>
                            <li>Industry Support</li>
                            <li>SMSEs</li>
                            <li>Others</li>
                        </ul>
                    </div>

                </div>
            </div>

        </div>

    </div>
</section> -->

<!-- CTA -->

<!-- <section class="cta">

    <div class="container text-center">

        <h2 style="color:#fff; padding:80px 0; font-size:42px; font-weight:700;">
            Ready to Join the Jakarta Film Community?
        </h2>

        <p class="mt-3">
            Register today and become part of the official
            Filming in Jakarta ecosystem.
        </p>

        <a href="#register" class="btn btn-orange mt-3">
            Register Now
        </a>

    </div>

</section> -->


    

<!-- REGISTER -->
<section id="register" class="py-5" style="background-color: #F8F0DF;">
<div class="container">

    <div class="row justify-content-center">

        <div class="col-lg-8">

            <div class="register-card text-center">

                <span class="membership-badge">
                    REGISTRATION OPEN
                </span>

                <h2 class="mt-3 mb-3" style="color: #FFC72C;">
                    Choose Your Member Type
                </h2>

                <p class="mb-5" style="color: white;">
                    Select the registration form that best
                    matches your profile.
                </p>

                <div class="row g-4">

                    <!-- Individual -->

                    <div class="col-md-6">

                        <div class="membership-option" 
                        style="
                        background:
                        linear-gradient(
                        rgba(0,0,0,.65),
                        rgba(0,0,0,.65)),
                        url('gallery/cover/rendy-novantino-VKmJAnzyB6c-unsplash.jpg.jpeg');

                        background-size:cover;
                        background-position:center;">

                            <div class="membership-icon">
                                <i class="fa-solid fa-user"></i>
                            </div>

                            <h4>Individual Member</h4>

                                <ul class="package-body" style="text-align: left;">
                                    <li>Active Industry Practitioner</li>
                                    <li>Independent Filmmakers</li>
                                    <li>Students</li>
                                    <li>Film Enthusiast</li>                                    
                                    <li>Community</li>
                                    <li>Individual Enterprise</li>
                                    <li>Others</li>
                                </ul>
                            <!-- <p>
                                For filmmakers, freelancers,
                                students, crew members,
                                artists, and creative professionals.
                            </p> -->

                            <a
                            href="https://forms.gle/gX6rj3MApcqG2fk1A"
                            target="_blank"
                            class="btn btn-orange btn-lg w-100">
                                Register as Individual
                            </a>

                        </div>

                    </div>

                    <!-- Entities -->

                    <div class="col-md-6">

                        <div class="membership-option"                        
                        style="
                        background:
                        linear-gradient(
                        rgba(0,0,0,.65),
                        rgba(0,0,0,.65)),
                        url('gallery/cover/gints-gailis-dn8xoYmzLZg-unsplash.jpeg');

                        background-size:cover;
                        background-position:center;">

                            <div class="membership-icon">
                                <i class="fa-solid fa-building"></i>
                            </div>

                            <h4>Entities Member</h4>

                                <ul class="package-body" style="text-align: left;">
                                    <li>Production House</li>
                                    <li>Academic Institution</li>
                                    <li>Venture Capital</li>
                                    <li>Industry Support</li>
                                    <li>Property Management</li>
                                    <li>SMSEs</li>
                                    <li>Others</li>
                                </ul>
                            <!-- <p>
                                For production houses,
                                agencies, studios,
                                vendors and organizations.
                            </p> -->

                            <a
                            href="https://forms.gle/WChB8x8aGkeKEtcJ8"
                            target="_blank"
                            class="btn btn-dark btn-lg w-100">
                                Register as Entities
                            </a>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>
</section>

<!-- Benefits Section -->
    <section id="benefits" style="background-color: #F8F0DF; padding-bottom: 50px;">
        <div class="container">
            <h2 style="color: #FF6A13;">Member Benefits by Type</h2>
            <p style="color: #DA291C;">Jelajahi Program yang tersedia untuk masing masing type keanggotaan Jkarta Film Commission</p>

            <div style="overflow-x: auto;">
                <table class="benefits-table">
                    <thead>
                        <tr>
                            <th>Program</th>
                            <th style="text-align: center;">Entity</th>
                            <th style="text-align: center;">Personal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>One Stop Film Service</td>
                            <td class="checkmark">✓</td>
                            <td class="checkmark">✓</td>
                        </tr>
                        <tr>
                            <td>Ekosistem SDM & Talent</td>
                            <td class="empty" style="text-align: center;">—</td>
                            <td class="checkmark">✓</td>
                        </tr>
                        <tr>
                            <td>Dukungan Produksi & Insentif</td>
                            <td class="checkmark">✓</td>
                            <td class="empty" style="text-align: center;">—</td>
                        </tr>
                        <tr>
                            <td>Kota Sebagai Pengguna Cerita</td>
                            <td class="checkmark">✓</td>
                            <td class="checkmark">✓</td>
                        </tr>
                        <tr>
                            <td>Promosi & Diplomasi Budaya</td>
                            <td class="checkmark">✓</td>
                            <td class="checkmark">✓</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

<!-- <footer>

    © <?= date('Y') ?> Filming in Jakarta.
    All Rights Reserved.

</footer> -->

  <!-- FOOTER -->
  <div id="footer"></div>


<!-- JAVASCRIPT -->
  <script src="assets/js/components.js"></script>
  <script src="assets/js/custom.js"></script>
  <script src="assets/data/press-release-data.js"></script>
  
    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>

const observer = new IntersectionObserver(entries => {

    entries.forEach(entry => {

        if(entry.isIntersecting){

            entry.target.style.opacity = "1";
            entry.target.style.transform = "translateY(0)";
        }

    });

},{
    threshold:0.15
});

document
.querySelectorAll(
'.benefit-card,.package-card,.membership-option'
)
.forEach(el=>{

    el.style.opacity="0";
    el.style.transform="translateY(40px)";
    el.style.transition="all .8s ease";

    observer.observe(el);

});

// Function untuk menampilkan popup Coming Soon
function showComingSoon() {
    document.getElementById('comingSoonPopup').classList.add('active');
}

// Function untuk menutup popup
function closePopup() {
    document.getElementById('comingSoonPopup').classList.remove('active');
}

// Function untuk redirect ke halaman lain
function redirectTo(url) {
    window.location.href = url;
}

// Tutup popup jika user klik di luar bubble
document.addEventListener('DOMContentLoaded', function() {
    const popup = document.getElementById('comingSoonPopup');
    popup.addEventListener('click', function(e) {
        if (e.target === popup) {
            closePopup();
        }
    });
});
// function loadComponent(id, url) {

//         const target =
//             document.getElementById(id);

//         if(!target){
//             console.warn(
//                 "Element not found:",
//                 id
//             );
//             return;
//         }

//         fetch(url)
//             .then(res => res.text())
//             .then(data => {

//                 target.innerHTML = data;

//             })
//             .catch(err =>
//                 console.error(
//                     "Component error:",
//                     err
//                 )
//             );
//     }

//     // load components
//     loadComponent("navbar", "components/navbar.html");
</script>

</body>
</html>