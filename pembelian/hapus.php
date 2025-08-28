<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}
include '../config.php';
$id = $_GET['id'];
mysqli_query($conn, "DELETE FROM pembelian WHERE id=$id");
header("Location: index.php");
