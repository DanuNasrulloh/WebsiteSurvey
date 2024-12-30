<?php
require 'koneksi.php';
session_start();

if (!isset($_GET['id_survey'])) {
    $_SESSION['error'] = "ID Survei tidak ditemukan.";
    header("Location: home.php");
    exit();
}

$id_survey = intval($_GET['id_survey']);

$stmt = $conn->prepare("DELETE FROM survey WHERE id_survey = ?");
$stmt->bind_param("i", $id_survey);

if ($stmt->execute()) {
    $_SESSION['success'] = "Survei berhasil dihapus.";
} else {
    $_SESSION['error'] = "Gagal menghapus survei.";
}

$stmt->close();

header("Location: home.php");
exit();
