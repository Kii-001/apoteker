<?php
session_start();
include 'config/database.php';
include 'config/functions.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami - Apotek Sehat</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <section class="about-hero">
        <div class="container">
            <div class="hero-content">
                <h1>Tentang Apotek Sehat</h1>
                <p>Melayani dengan hati, menjaga kesehatan Anda</p>
            </div>
        </div>
    </section>

    <section class="about-content">
        <div class="container">
            <div class="about-grid">
                <div class="about-text">
                    <h2>Visi & Misi Kami</h2>
                    <p>Apotek Sehat hadir sebagai solusi kesehatan digital yang terpercaya bagi masyarakat Indonesia. Dengan komitmen untuk menyediakan obat-obatan berkualitas dan pelayanan terbaik, kami berdedikasi untuk mendukung kesehatan dan kesejahteraan Anda.</p>
                    
                    <div class="mission-vision">
                        <div class="mv-item">
                            <h3><i class="fas fa-bullseye"></i> Visi</h3>
                            <p>Menjadi apotek digital terdepan yang memberikan akses mudah dan terpercaya terhadap produk kesehatan bagi seluruh masyarakat Indonesia.</p>
                        </div>
                        <div class="mv-item">
                            <h3><i class="fas fa-flag"></i> Misi</h3>
                            <p>Menyediakan obat-obatan berkualitas dengan harga terjangkau, memberikan pelayanan konsultasi kesehatan profesional, dan memastikan kepuasan pelanggan melalui layanan yang cepat dan aman.</p>
                        </div>
                    </div>
                </div>
                
                <div class="about-image">
                    <img src="assets/images/about-pharmacy.jpg" alt="Apotek Sehat">
                </div>
            </div>
        </div>
    </section>

    <section class="values-section">
        <div class="container">
            <h2>Nilai-Nilai Kami</h2>
            <div class="values-grid">
                <div class="value-card">
                    <i class="fas fa-shield-alt"></i>
                    <h3>Terpercaya</h3>
                    <p>Semua produk dijamin keasliannya dengan sertifikat resmi dari BPOM</p>
                </div>
                <div class="value-card">
                    <i class="fas fa-heart"></i>
                    <h3>Peduli</h3>
                    <p>Tim apoteker siap memberikan konsultasi dan saran kesehatan terbaik</p>
                </div>
                <div class="value-card">
                    <i class="fas fa-rocket"></i>
                    <h3>Cepat</h3>
                    <p>Proses pemesanan mudah dengan pengiriman cepat ke seluruh Indonesia</p>
                </div>
                <div class="value-card">
                    <i class="fas fa-hand-holding-heart"></i>
                    <h3>Aman</h3>
                    <p>Sistem pembayaran yang aman dan terjamin keamanan datanya</p>
                </div>
            </div>
        </div>
    </section>

    <section class="team-section">
        <div class="container">
            <h2>Tim Profesional Kami</h2>
            <div class="team-grid">
                <div class="team-member">
                    <div class="member-photo">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <h3>noval</h3>
                    <p>Sebagai apa</p>
                    <p>keterangan</p>
                </div>
                <div class="team-member">
                    <div class="member-photo">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <h3>Dila.</h3>
                    <p>Sebagai apa</p>
                    <p>keterangan</p>
                </div>
                <div class="team-member">
                    <div class="member-photo">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <h3>dela</h3>
                    <p>Sebagai apa</p>
                    <p>keterangan</p>
                </div>
                <div class="team-member">
                    <div class="member-photo">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <h3>julaika</h3>
                    <p>Sebagai apa</p>
                    <p>keterangan</p>
                </div>
            </div>
        </div>
    </section>

    <style>
    .about-hero {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
        padding: 4rem 0;
        text-align: center;
    }
    
    .about-hero h1 {
        font-size: 3rem;
        margin-bottom: 1rem;
    }
    
    .about-content {
        padding: 4rem 0;
    }
    
    .about-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 4rem;
        align-items: center;
    }
    
    .about-text h2 {
        font-size: 2.5rem;
        margin-bottom: 1.5rem;
        color: var(--dark);
    }
    
    .about-text p {
        font-size: 1.125rem;
        line-height: 1.7;
        margin-bottom: 2rem;
        color: var(--secondary);
    }
    
    .mission-vision {
        display: grid;
        gap: 2rem;
    }
    
    .mv-item h3 {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1rem;
        color: var(--primary);
    }
    
    .mv-item i {
        font-size: 1.5rem;
    }
    
    .about-image img {
        width: 100%;
        border-radius: 1rem;
        box-shadow: var(--shadow);
    }
    
    .values-section {
        padding: 4rem 0;
        background: var(--light);
    }
    
    .values-section h2 {
        text-align: center;
        font-size: 2.5rem;
        margin-bottom: 3rem;
        color: var(--dark);
    }
    
    .values-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2rem;
    }
    
    .value-card {
        background: white;
        padding: 2rem;
        border-radius: 1rem;
        box-shadow: var(--shadow);
        text-align: center;
        transition: transform 0.3s;
    }
    
    .value-card:hover {
        transform: translateY(-5px);
    }
    
    .value-card i {
        font-size: 3rem;
        color: var(--primary);
        margin-bottom: 1rem;
    }
    
    .value-card h3 {
        margin-bottom: 1rem;
        color: var(--dark);
    }
    
    .team-section {
        padding: 4rem 0;
    }
    
    .team-section h2 {
        text-align: center;
        font-size: 2.5rem;
        margin-bottom: 3rem;
        color: var(--dark);
    }
    
    .team-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
    }
    
    .team-member {
        background: white;
        padding: 2rem;
        border-radius: 1rem;
        box-shadow: var(--shadow);
        text-align: center;
    }
    
    .member-photo {
        width: 100px;
        height: 100px;
        background: var(--primary);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        font-size: 2.5rem;
    }
    
    .team-member h3 {
        margin-bottom: 0.5rem;
        color: var(--dark);
    }
    
    .team-member p {
        color: var(--secondary);
        margin-bottom: 0.5rem;
    }
    
    @media (max-width: 768px) {
        .about-grid {
            grid-template-columns: 1fr;
            gap: 2rem;
        }
        
        .about-hero h1 {
            font-size: 2rem;
        }
        
        .values-grid,
        .team-grid {
            grid-template-columns: 1fr;
        }
    }
    </style>

    <?php include 'includes/footer.php'; ?>
</body>
</html>