<?php
require 'koneksi.php';
session_start();

if (!isset($_GET['id_survey'])) {
    header("Location: home.php");
    exit();
}

$id_survey = intval($_GET['id_survey']);

$stmt = $conn->prepare("SELECT * FROM survey WHERE id_survey = ?");
$stmt->bind_param("i", $id_survey);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Survei tidak ditemukan.";
    exit();
}
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $teks_pertanyaan = trim($_POST['teks_pertanyaan']);
    $tipe_jawaban = strtolower(trim($_POST['tipe_jawaban']));

    $allowed_types = ['isian_singkat', 'pilihan_ganda'];
    if (!in_array($tipe_jawaban, $allowed_types)) {
        $error = "Tipe jawaban tidak valid.";
    }

    $option_a = $option_b = $option_c = $option_d = null;

    if ($tipe_jawaban === 'pilihan_ganda') {
        $option_a = trim($_POST['option_a']);
        $option_b = trim($_POST['option_b']);
        $option_c = trim($_POST['option_c']);
        $option_d = trim($_POST['option_d']);

        if (strlen(trim($option_a)) === 0 || strlen(trim($option_b)) === 0 || strlen(trim($option_c)) === 0 || strlen(trim($option_d)) === 0) {
            $error = "Semua opsi (A, B, C, D) harus diisi untuk tipe jawaban Pilihan Ganda.";
        }
    }

    if (!isset($error)) {
        $stmt = $conn->prepare("INSERT INTO pertanyaan (id_survey, teks_pertanyaan, tipe_jawaban, option_a, option_b, option_c, option_d) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if ($stmt === false) {
            die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }

        $stmt->bind_param("issssss", $id_survey, $teks_pertanyaan, $tipe_jawaban, $option_a, $option_b, $option_c, $option_d);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Pertanyaan berhasil ditambahkan.<br>Link untuk responden: <a href='survey_form.php?id_survey=" . $id_survey . "' target='_blank'>survey_form.php?id_survey=" . $id_survey . "</a>";
            $stmt->close();
            header("Location: add_question.php?id_survey=" . $id_survey);
            exit();
        } else {
            $error = "Terjadi kesalahan: " . $stmt->error;
        }
    }
}

$stmt = $conn->prepare("SELECT * FROM pertanyaan WHERE id_survey = ? ORDER BY id_pertanyaan ASC");
$stmt->bind_param("i", $id_survey);
$stmt->execute();
$questions = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tambah Pertanyaan - Survei ID <?php echo $id_survey; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .survey-card {
            transition: transform 0.2s;
        }

        .survey-card:hover {
            transform: scale(1.02);
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="landing.php">SurveiApp</a>
        </div>
    </nav>

    <div class="container mt-5">
        <h2>Tambah Pertanyaan untuk Survei ID: <?php echo $id_survey; ?></h2>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['success'];
                unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($error); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form method="POST" action="" class="mt-4">
            <div class="mb-3">
                <label class="form-label">Teks Pertanyaan</label>
                <input type="text" name="teks_pertanyaan" class="form-control" required value="<?php echo isset($_POST['teks_pertanyaan']) ? htmlspecialchars($_POST['teks_pertanyaan']) : ''; ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Tipe Jawaban</label>
                <select name="tipe_jawaban" class="form-select" id="tipe_jawaban" required>
                    <option value="">-- Pilih Tipe Jawaban --</option>
                    <option value="isian_singkat" <?php echo (isset($_POST['tipe_jawaban']) && $_POST['tipe_jawaban'] === 'isian_singkat') ? 'selected' : ''; ?>>Isian Singkat</option>
                    <option value="pilihan_ganda" <?php echo (isset($_POST['tipe_jawaban']) && $_POST['tipe_jawaban'] === 'pilihan_ganda') ? 'selected' : ''; ?>>Pilihan Ganda</option>
                </select>
            </div>

            <div id="pilihan_ganda_options" style="display: <?php echo (isset($_POST['tipe_jawaban']) && $_POST['tipe_jawaban'] === 'pilihan_ganda') ? 'block' : 'none'; ?>;">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Opsi A</label>
                        <input type="text" name="option_a" class="form-control" value="<?php echo isset($_POST['option_a']) ? htmlspecialchars($_POST['option_a']) : ''; ?>" <?php echo (isset($_POST['tipe_jawaban']) && $_POST['tipe_jawaban'] === 'pilihan_ganda') ? 'required' : ''; ?>>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Opsi B</label>
                        <input type="text" name="option_b" class="form-control" value="<?php echo isset($_POST['option_b']) ? htmlspecialchars($_POST['option_b']) : ''; ?>" <?php echo (isset($_POST['tipe_jawaban']) && $_POST['tipe_jawaban'] === 'pilihan_ganda') ? 'required' : ''; ?>>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Opsi C</label>
                        <input type="text" name="option_c" class="form-control" value="<?php echo isset($_POST['option_c']) ? htmlspecialchars($_POST['option_c']) : ''; ?>" <?php echo (isset($_POST['tipe_jawaban']) && $_POST['tipe_jawaban'] === 'pilihan_ganda') ? 'required' : ''; ?>>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Opsi D</label>
                        <input type="text" name="option_d" class="form-control" value="<?php echo isset($_POST['option_d']) ? htmlspecialchars($_POST['option_d']) : ''; ?>" <?php echo (isset($_POST['tipe_jawaban']) && $_POST['tipe_jawaban'] === 'pilihan_ganda') ? 'required' : ''; ?>>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-success">Tambah Pertanyaan</button>
            <a href="home.php" class="btn btn-secondary">Selesai</a>
        </form>

        <hr>

        <h3>Pertanyaan yang Ada</h3>
        <?php if ($questions->num_rows > 0): ?>
            <div class="list-group">
                <?php while ($q = $questions->fetch_assoc()): ?>
                    <div class="list-group-item list-group-item-action flex-column align-items-start">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1"><?php echo htmlspecialchars($q['teks_pertanyaan']); ?></h5>
                            <small><?php echo ucfirst(str_replace('_', ' ', $q['tipe_jawaban'])); ?></small>
                        </div>
                        <?php if ($q['tipe_jawaban'] === 'pilihan_ganda'): ?>
                            <ul class="mb-1">
                                <li><?php echo htmlspecialchars($q['option_a']); ?></li>
                                <li><?php echo htmlspecialchars($q['option_b']); ?></li>
                                <li><?php echo htmlspecialchars($q['option_c']); ?></li>
                                <li><?php echo htmlspecialchars($q['option_d']); ?></li>
                            </ul>
                        <?php endif; ?>
                        <a href="delete_question.php?id_pertanyaan=<?php echo $q['id_pertanyaan']; ?>&id_survey=<?php echo $id_survey; ?>"
                            class="btn btn-danger btn-sm mt-2"
                            onclick="return confirm('Apakah Anda yakin ingin menghapus pertanyaan ini?');">Hapus</a>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-warning" role="alert">
                Belum ada pertanyaan yang ditambahkan.
            </div>
        <?php endif; ?>
    </div>

    <footer class="bg-primary text-white text-center py-3 mt-5">
        &copy; <?php echo date("Y"); ?> SurveiApp. All rights reserved.
    </footer>

    <script>
        $(document).ready(function() {
            $('#tipe_jawaban').change(function() {
                if ($(this).val() === 'pilihan_ganda') {
                    $('#pilihan_ganda_options').slideDown();
                    $('#pilihan_ganda_options input').attr('required', true);
                } else {
                    $('#pilihan_ganda_options').slideUp();
                    $('#pilihan_ganda_options input').removeAttr('required').val('');
                }
            });

            var selected = $('#tipe_jawaban').val();
            if (selected === 'pilihan_ganda') {
                $('#pilihan_ganda_options').show();
                $('#pilihan_ganda_options input').attr('required', true);
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>