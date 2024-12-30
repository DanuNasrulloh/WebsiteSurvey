<?php
require 'koneksi.php';
session_start();

$stmt = $conn->prepare("SELECT * FROM survey ORDER BY tanggal_dibuat DESC");
$stmt->execute();
$surveys = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Sistem Survei Sederhana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
        }

        .survey-card {
            transition: transform 0.2s;
        }

        .survey-card:hover {
            transform: scale(1.02);
        }

        .content {
            flex: 1;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="landing.php">SurveiApp</a>
        </div>
    </nav>

    <div class="container mt-5 content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Sistem Survei Sederhana</h1>
            <a href="create_survey.php" class="btn btn-success">Buat Survei Baru</a>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['success'];
                unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['error'];
                unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if ($surveys->num_rows > 0): ?>
            <div class="row">
                <?php while ($survey = $surveys->fetch_assoc()): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card survey-card shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($survey['judul']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($survey['deskripsi']); ?></p>
                                <p class="card-text"><small class="text-muted">Dibuat pada: <?php echo htmlspecialchars($survey['tanggal_dibuat']); ?></small></p>
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="add_question.php?id_survey=<?php echo $survey['id_survey']; ?>" class="btn btn-secondary btn-sm">Tambah Pertanyaan</a>
                                    <a href="survey_form.php?id_survey=<?php echo $survey['id_survey']; ?>" class="btn btn-success btn-sm">Bagikan Survei</a>
                                    <a href="result.php?id_survey=<?php echo $survey['id_survey']; ?>" class="btn btn-info btn-sm">Lihat Hasil</a>
                                    <a href="delete_survey.php?id_survey=<?php echo $survey['id_survey']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus survei ini?');">Hapus</a>
                                    <a href="cetak_pdf.php?id_survey=<?php echo $survey['id_survey']; ?>" class="btn btn-primary" target="_blank">Cetak PDF</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-warning" role="alert">
                Belum ada survei yang dibuat.
            </div>
        <?php endif; ?>
    </div>

    <footer class="bg-primary text-white text-center py-3 mt-5">
        &copy; <?php echo date("Y"); ?> SurveiApp. All rights reserved.
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>