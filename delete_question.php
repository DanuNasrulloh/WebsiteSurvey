<?php
require 'koneksi.php';
session_start();

if (!isset($_GET['id_survey']) || !isset($_GET['id_pertanyaan'])) {
    $_SESSION['error'] = "ID Pertanyaan atau ID Survei tidak ditemukan.";
    header("Location: index.php");
    exit();
}

$id_survey = intval($_GET['id_survey']);
$id_pertanyaan = intval($_GET['id_pertanyaan']);

$stmt = $conn->prepare("DELETE FROM pertanyaan WHERE id_pertanyaan = ? AND id_survey = ?");
$stmt->bind_param("ii", $id_pertanyaan, $id_survey);

if ($stmt->execute()) {
    $_SESSION['success'] = "Pertanyaan berhasil dihapus.";
} else {
    $_SESSION['error'] = "Gagal menghapus pertanyaan.";
}

$stmt->close();

header("Location: add_question.php?id_survey=" . $id_survey);
exit();
