<?php
require 'koneksi.php';
session_start();

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
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Hasil Survei - <?php echo htmlspecialchars($survey['judul']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }

        .chart-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 50vh;
            width: 50vw;
            margin: auto;
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
        <h2>Hasil Survei: <?php echo htmlspecialchars($survey['judul']); ?></h2>
        <p><?php echo htmlspecialchars($survey['deskripsi']); ?></p>

        <?php while ($q = $pertanyaan->fetch_assoc()): ?>
            <div class="mb-5">
                <h4><?php echo htmlspecialchars($q['teks_pertanyaan']); ?></h4>
                <?php if ($q['tipe_jawaban'] === 'isian_singkat'): ?>
                    <?php
                    $stmt = $conn->prepare("SELECT jawaban FROM jawaban_responden WHERE id_survey = ? AND id_pertanyaan = ?");
                    $stmt->bind_param("ii", $id_survey, $q['id_pertanyaan']);
                    $stmt->execute();
                    $jawaban = $stmt->get_result();
                    $stmt->close();
                    ?>
                    <?php if ($jawaban->num_rows > 0): ?>
                        <ul class="list-group">
                            <?php while ($ans = $jawaban->fetch_assoc()): ?>
                                <li class="list-group-item"><?php echo htmlspecialchars($ans['jawaban']); ?></li>
                            <?php endwhile; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-muted">Tidak ada jawaban.</p>
                    <?php endif; ?>
                <?php elseif ($q['tipe_jawaban'] === 'pilihan_ganda'): ?>
                    <?php
                    $options = [
                        'option_a' => $q['option_a'],
                        'option_b' => $q['option_b'],
                        'option_c' => $q['option_c'],
                        'option_d' => $q['option_d']
                    ];

                    $filtered_options = array_filter($options, function ($value) {
                        return !empty($value);
                    });

                    $placeholders = implode(',', array_fill(0, count($filtered_options), '?'));
                    $types = str_repeat('s', count($filtered_options));
                    $values = array_values($filtered_options);

                    $sql = "SELECT jawaban, COUNT(*) as jumlah FROM jawaban_responden WHERE id_survey = ? AND id_pertanyaan = ? AND jawaban IN ($placeholders) GROUP BY jawaban";
                    $stmt = $conn->prepare($sql);
                    if ($stmt === false) {
                        die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
                    }

                    $param_types = "ii" . $types;
                    $stmt->bind_param($param_types, $id_survey, $q['id_pertanyaan'], ...$values);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $counts = [];
                    while ($row = $result->fetch_assoc()) {
                        $counts[$row['jawaban']] = intval($row['jumlah']);
                    }
                    $stmt->close();

                    $labels = [];
                    $data = [];
                    $backgroundColors = [
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 206, 86, 0.8)',
                        'rgba(75, 192, 192, 0.8)'
                    ];

                    foreach ($filtered_options as $option) {
                        $labels[] = htmlspecialchars($option);
                        $data[] = isset($counts[$option]) ? $counts[$option] : 0;
                    }

                    $canvasID = "chart_" . $q['id_pertanyaan'];
                    ?>
                    <div class="chart-container mb-4">
                        <canvas id="<?php echo $canvasID; ?>"></canvas>
                    </div>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            var ctx = document.getElementById('<?php echo $canvasID; ?>').getContext('2d');
                            var myChart = new Chart(ctx, {
                                type: 'pie',
                                data: {
                                    labels: <?php echo json_encode($labels); ?>,
                                    datasets: [{
                                        label: '# of Votes',
                                        data: <?php echo json_encode($data); ?>,
                                        backgroundColor: <?php echo json_encode($backgroundColors); ?>,
                                        borderWidth: 1
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    plugins: {
                                        legend: {
                                            position: 'top',
                                        },
                                        title: {
                                            display: true,
                                            text: 'Distribusi Jawaban'
                                        }
                                    }
                                }
                            });
                        });
                    </script>
                <?php else: ?>
                    <p class="text-danger">Jenis jawaban tidak dikenali.</p>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>

        <a href="index.php" class="btn btn-secondary">Kembali ke Halaman Utama</a>
    </div>

    <footer class="bg-primary text-white text-center py-3 mt-5">
        &copy; <?php echo date("Y"); ?> SurveiApp. All rights reserved.
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>