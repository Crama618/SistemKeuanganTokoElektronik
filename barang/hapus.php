<?php
include '../config.php';
$id = $_GET['id'];
mysqli_query($conn, "DELETE FROM barang WHERE id='$id'");
header("Location: ../barang/index.php");
exit;
?>
