<?php
include("config.php");

if (isset($_POST['delete_book'])) {
  $id = $_POST['id'];
  $sql = "DELETE FROM books WHERE id='$id'";
  mysqli_query($conn, $sql);

  header("Location: manage_books.php");
  exit();
}
?>
