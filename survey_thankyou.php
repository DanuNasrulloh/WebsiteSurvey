<?php
// survey_thankyou.php
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Terima Kasih</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .thank-you-container {
            margin: auto;
            text-align: center;
            background: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        h1 {
            margin-bottom: 20px;
        }
        p {
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="index.php">SurveiApp</a>
    </div>
</nav>

<div class="container d-flex" style="flex:1;">
    <div class="thank-you-container">
        <h1>Terima Kasih!</h1>
        <p>Survei Anda telah berhasil dikirim. Kami menghargai waktu dan partisipasi Anda.</p>
        <a href="index.php" class="btn btn-secondary">Kembali ke Halaman Utama</a>
    </div>
</div>

<footer class="bg-primary text-white text-center py-3">
    &copy; <?php echo date("Y"); ?> SurveiApp. All rights reserved.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
