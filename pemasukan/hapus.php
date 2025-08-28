<?php
include '../config.php';
if (!isset($_SESSION['login'])) { header("Location: ../login.php"); exit; }

$id = $_GET['id'];
mysqli_query($conn, "DELETE FROM pemasukan WHERE id=$id");
header("Location: index.php");
exit;
?>
