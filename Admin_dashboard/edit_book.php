
<?php
include __DIR__ . '/config.php';
if (isset($_POST['edit_book'])) {
  $id = $_POST['id'];
  $title = $_POST['title'];
  $author = $_POST['author'];
  $quantity = $_POST['quantity'];
  $category = $_POST['category'];
  $sql = "UPDATE books SET title='$title', author='$author', quantity='$quantity', category='$category' WHERE id='$id'";
  mysqli_query($conn, $sql);
  header("Location: manage_books.php");
  exit();
}
?>
