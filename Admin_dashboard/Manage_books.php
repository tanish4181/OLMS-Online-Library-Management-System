<?php
include("config.php");

$limit = 5;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start_from = ($page - 1) * $limit;

$sql = "SELECT * FROM books LIMIT $start_from, $limit";
$result = mysqli_query($conn, $sql);
?>
