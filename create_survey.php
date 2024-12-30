<?php
require 'koneksi.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = trim($_POST['judul']);
    $deskripsi = trim($_POST['deskripsi']);

    if (empty($judul) || empty($deskripsi)) {
        $error = "Judul dan deskripsi survei harus diisi.";
    }

    if (!isset($error)) {
        $stmt = $conn->prepare("INSERT INTO survey (judul, deskripsi) VALUES (?, ?)");
        if ($stmt === false) {
            die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }
        $stmt->bind_param("ss", $judul, $deskripsi);

        if ($stmt->execute()) {
            $id_survey = $stmt->insert_id;
            $stmt->close();
            $_SESSION['success'] = "Survei berhasil dibuat.<br>Link untuk responden: <a href='survey_form.php?id_survey=".$id_survey."' target='_blank'>survey_form.php?id_survey=".$id_survey."</a>";
            header("Location: add_question.php?id_survey=".$id_survey);
            exit();
        } else {
            $error = "Terjadi kesalahan: " . $stmt->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buat Survei Baru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="home.php">SurveiApp</a>
    </div>
</nav>


<div class="container mt-5">
    <h2>Buat Survei Baru</h2>
    <?php if(isset($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($error); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <form method="POST" action="" class="mt-4">
        <div class="mb-3">
            <label class="form-label">Judul Survei</label>
            <input type="text" name="judul" class="form-control" required value="<?php echo isset($_POST['judul']) ? htmlspecialchars($_POST['judul']) : ''; ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea name="deskripsi" class="form-control" rows="4" required><?php echo isset($_POST['deskripsi']) ? htmlspecialchars($_POST['deskripsi']) : ''; ?></textarea>
        </div>
        <button type="submit" class="btn btn-success">Buat Survei</button>
        <a href="index.php" class="btn btn-secondary">Kembali</a>
    </form>
</div>

<footer class="bg-primary text-white text-center py-3 mt-5">
    &copy; <?php echo date("Y"); ?> SurveiApp. All rights reserved.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
