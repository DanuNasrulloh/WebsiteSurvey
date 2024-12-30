<?php
// landing.php
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SurveiApp - Solusi Survei Anda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Gaya Umum */
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(to right, #4e79a7, #59a14f), url('https://images.unsplash.com/photo-1601933470011-fd8e9c00dce5?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1080') center/cover no-repeat;
            color: #fff;
            text-align: center;
            padding: 120px 20px;
            position: relative;
        }

        .hero h1 {
            font-size: 3rem;
            font-weight: bold;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .hero p {
            font-size: 1.2rem;
            margin-bottom: 30px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .hero .btn-cta {
            background-color: #ffd700;
            color: #333;
            padding: 12px 30px;
            font-size: 1.2rem;
            font-weight: bold;
            border: none;
            transition: transform 0.3s ease, background-color 0.3s ease;
        }

        .hero .btn-cta:hover {
            background-color: #f1c40f;
            transform: scale(1.1);
        }

        /* Features Section */
        .features {
            padding: 60px 20px;
            background-color: #f8f9fa;
        }

        .features h2 {
            font-size: 2.5rem;
            margin-bottom: 30px;
            font-weight: bold;
            color: #333;
        }

        .features .feature-item {
            margin-bottom: 30px;
            text-align: center;
        }

        .features .feature-item img {
            max-width: 80px;
            margin-bottom: 20px;
        }

        .features .feature-item h4 {
            font-weight: bold;
        }

        /* Footer */
        .footer {
            background-color: #333;
            color: #fff;
            padding: 2px 0;
            text-align: center;
        }

        /* Animasi */
        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeIn 1s forwards;
        }

        @keyframes fadeIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1 class="fade-in">SurveiApp: Solusi Survei Modern</h1>
            <p class="fade-in" style="animation-delay: 0.5s;">Membuat survei menjadi mudah. Bagikan survei dan dapatkan hasil instan dengan antarmuka profesional.</p>
            <a href="home.php" class="btn btn-cta fade-in" style="animation-delay: 1s;">Mulai Sekarang</a>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="container">
            <h2 class="fade-in">Kenapa Memilih SurveiApp?</h2>
            <div class="row">
                <!-- Feature 1 -->
                <div class="col-md-4 feature-item fade-in" style="animation-delay: 0.5s;">
                    <img src="https://img.icons8.com/color/96/checklist.png" alt="Kemudahan Penggunaan">
                    <h4>Kemudahan Penggunaan</h4>
                    <p>Antarmuka sederhana untuk membuat survei tanpa hambatan teknis.</p>
                </div>
                <!-- Feature 2 -->
                <div class="col-md-4 feature-item fade-in" style="animation-delay: 0.7s;">
                    <img src="https://img.icons8.com/color/96/bar-chart.png" alt="Analisis Data Cepat">
                    <h4>Analisis Data Cepat</h4>
                    <p>Hasil survei langsung dengan visualisasi data yang mudah dipahami.</p>
                </div>
                <!-- Feature 3 -->
                <div class="col-md-4 feature-item fade-in" style="animation-delay: 0.9s;">
                    <img src="https://img.icons8.com/color/96/share.png" alt="Mudah Dibagikan">
                    <h4>Mudah Dibagikan</h4>
                    <p>Bagikan survei dengan tautan yang dapat diakses kapan saja.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; <?php echo date("Y"); ?> SurveiApp. Semua hak dilindungi.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>