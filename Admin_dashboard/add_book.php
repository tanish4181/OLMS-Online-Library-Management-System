<?php
include("config.php");

if (isset($_POST['add'])) {
  $title = $_POST['title'];
  $author = $_POST['author'];
  $category = $_POST['category'];
  $quantity = $_POST['quantity'];
  $description = $_POST['description'];

  $filename = $_FILES["cover"]["name"];
  $tempname = $_FILES["cover"]["tmp_name"];
  $folder = "../uploads/" . $filename;

  move_uploaded_file($tempname, $folder);

  $sql = "INSERT INTO books (title, cover, author, category, quantity, description) 
          VALUES ('$title', '$folder', '$author', '$category', '$quantity', '$description')";

  $run = mysqli_query($conn, $sql);

  if ($run) {
    echo "<script>alert('Book added successfully');</script>";
  } else {
    echo "<script>alert('Failed to add book');</script>";
  }
}
?>
