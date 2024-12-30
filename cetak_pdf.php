<?php
require 'koneksi.php';
require('fpdf/fpdf.php');

if (!isset($_GET['id_survey'])) {
    die("ID Survey tidak ditemukan");
}

$id_survey = intval($_GET['id_survey']);

// Ambil data survey
$stmt = $conn->prepare("SELECT * FROM survey WHERE id_survey = ?");
if (!$stmt) {
    die("Error preparing survey query: " . $conn->error);
}
$stmt->bind_param("i", $id_survey);
if (!$stmt->execute()) {
    die("Error executing survey query: " . $stmt->error);
}
$survey = $stmt->get_result()->fetch_assoc();

if (!$survey) {
    die("Survey tidak ditemukan");
}

// Ambil pertanyaan survey
$stmt = $conn->prepare("SELECT * FROM pertanyaan WHERE id_survey = ? ORDER BY id_pertanyaan ASC");
if (!$stmt) {
    die("Error preparing pertanyaan query: " . $conn->error);
}
$stmt->bind_param("i", $id_survey);
if (!$stmt->execute()) {
    die("Error executing pertanyaan query: " . $stmt->error);
}
$pertanyaan = $stmt->get_result();

// Buat instance PDF
$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetMargins(20, 20, 20);

// Header
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(170, 10, 'SURVEIAPP', 0, 1, 'C');
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(170, 10, $survey['judul'], 0, 1, 'C');
$pdf->Ln(5);

// Tambahkan deskripsi survey
$pdf->SetFont('Arial', '', 12);
$pdf->MultiCell(170, 10, $survey['deskripsi'], 0, 'L');
$pdf->Ln(5);

// Tambahkan pertanyaan
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(170, 10, 'Pertanyaan:', 0, 1);
$pdf->SetFont('Arial', '', 12);

$no = 1;
while ($row = $pertanyaan->fetch_assoc()) {
    // Tambahkan spasi sebelum pertanyaan
    $pdf->Ln(5);

    // Cetak nomor dan pertanyaan
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(10, 10, $no . '.', 0, 0);
    $pdf->MultiCell(160, 10, $row['teks_pertanyaan'], 0, 'L');

    // Reset font untuk opsi
    $pdf->SetFont('Arial', '', 12);

    // Jika tipe jawaban adalah pilihan ganda, tampilkan opsi
    if ($row['tipe_jawaban'] === 'pilihan_ganda') {
        $pdf->SetX(30); // Indent untuk opsi
        $pdf->MultiCell(150, 8, 'A. ' . $row['option_a'], 0, 'L');
        $pdf->SetX(30);
        $pdf->MultiCell(150, 8, 'B. ' . $row['option_b'], 0, 'L');
        $pdf->SetX(30);
        $pdf->MultiCell(150, 8, 'C. ' . $row['option_c'], 0, 'L');
        $pdf->SetX(30);
        $pdf->MultiCell(150, 8, 'D. ' . $row['option_d'], 0, 'L');
    } else {
        // Jika tipe jawaban adalah isian singkat
        $pdf->SetX(30);
        $pdf->MultiCell(150, 8, '_____________________', 0, 'L');
    }

    $no++;
    $pdf->Ln(2); // Tambahkan sedikit spasi setelah setiap pertanyaan
}

// Output PDF
$pdf->Output('I', 'Survey_' . $survey['judul'] . '.pdf');
