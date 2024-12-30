<?php
require 'koneksi.php';
session_start();

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (!isset($_GET['id_survey'])) {
    echo "Survei tidak ditemukan.";
    exit();
}

$id_survey = intval($_GET['id_survey']);

$stmt = $conn->prepare("SELECT * FROM survey WHERE id_survey = ?");
$stmt->bind_param("i", $id_survey);
$stmt->execute();
$cek_survey = $stmt->get_result();

if ($cek_survey->num_rows === 0) {
    echo "Survei tidak ditemukan.";
    exit();
}

$survey = $cek_survey->fetch_assoc();
$stmt->close();

$stmt = $conn->prepare("SELECT * FROM pertanyaan WHERE id_survey = ? ORDER BY id_pertanyaan ASC");
$stmt->bind_param("i", $id_survey);
$stmt->execute();
$pertanyaan = $stmt->get_result();
$stmt->close();

if (!isset($_SESSION['responden_token'])) {
    $_SESSION['responden_token'] = uniqid('resp_', true);
}
$responden_token = $_SESSION['responden_token'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Token CSRF tidak valid.";
    } else {
        if (isset($_POST['jawaban']) && is_array($_POST['jawaban'])) {
            $stmt = $conn->prepare("INSERT INTO jawaban_responden (id_survey, id_pertanyaan, responden_token, jawaban) VALUES (?, ?, ?, ?)");
            if ($stmt === false) {
                die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
            }

            $all_filled = true;

            foreach ($_POST['jawaban'] as $id_pertanyaan_post => $isi_jawaban) {
                $id_pertanyaan_post = intval($id_pertanyaan_post);
                $isi_jawaban = trim($isi_jawaban);

                if (empty($isi_jawaban)) {
                    $all_filled = false;
                    break;
                }

                $stmt->bind_param("iiss", $id_survey, $id_pertanyaan_post, $responden_token, $isi_jawaban);
                $stmt->execute();
            }

            $stmt->close();

            if ($all_filled) {
                unset($_SESSION['responden_token']);
                header("Location: survey_thankyou.php");
                exit();
            } else {
                $error = "Silakan isi semua pertanyaan.";
            }
        } else {
            $error = "Silakan isi semua pertanyaan.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Isi Survei - <?php echo htmlspecialchars($survey['judul']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .form-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .survey-title {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="home.php">SurveiApp</a>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="form-container">
            <h2 class="survey-title"><?php echo htmlspecialchars($survey['judul']); ?></h2>
            <p class="text-center"><?php echo htmlspecialchars($survey['deskripsi']); ?></p>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <?php while ($q = $pertanyaan->fetch_assoc()): ?>
                    <div class="mb-4">
                        <label class="form-label"><?php echo htmlspecialchars($q['teks_pertanyaan']); ?></label>
                        <?php if ($q['tipe_jawaban'] === 'isian_singkat'): ?>
                            <input type="text" name="jawaban[<?php echo $q['id_pertanyaan']; ?>]" class="form-control" required>
                        <?php elseif ($q['tipe_jawaban'] === 'pilihan_ganda'): ?>
                            <?php
                            $options = [];
                            if (!empty($q['option_a'])) $options[] = htmlspecialchars($q['option_a']);
                            if (!empty($q['option_b'])) $options[] = htmlspecialchars($q['option_b']);
                            if (!empty($q['option_c'])) $options[] = htmlspecialchars($q['option_c']);
                            if (!empty($q['option_d'])) $options[] = htmlspecialchars($q['option_d']);
                            ?>
                            <?php foreach ($options as $option): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="jawaban[<?php echo $q['id_pertanyaan']; ?>]" value="<?php echo $option; ?>" required>
                                    <label class="form-check-label"><?php echo $option; ?></label>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-danger">Jenis jawaban tidak dikenali.</p>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
                <button type="submit" class="btn btn-primary w-100">Kirim Jawaban</button>
            </form>
        </div>
    </div>

    <footer class="bg-primary text-white text-center py-3 mt-5">
        &copy; <?php echo date("Y"); ?> SurveiApp. All rights reserved.
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>