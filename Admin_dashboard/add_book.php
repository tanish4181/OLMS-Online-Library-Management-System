<?php
// Start the session to check if admin is logged in
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../auth/adminLogin.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Add Book - Admin Dashboard</title>
  <link rel="stylesheet" href="../asset/style.css" />
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
    rel="stylesheet" />
</head>

<body class="addUser">
  <?php
  include("navbar_admin.php");
  ?>
  <?php
// Include the database connection file
include("config.php");

// Check if the form was submitted
if (isset($_POST['add'])) {
    // Get the form data
    $title = $_POST['title'];
    $author = $_POST['author'];
    $category = $_POST['category'];
    $quantity = $_POST['quantity'];
    $description = $_POST['description'];

    // Handle file upload
    $filename = $_FILES["cover"]["name"];
    $tempname = $_FILES["cover"]["tmp_name"];
    $folder = "../uploads/" . $filename; // Create this folder if not exists

    // Move the uploaded file to our uploads folder
    move_uploaded_file($tempname, $folder);

    // Insert the book into the database
    $sql = "INSERT INTO books (title, cover, author, category, quantity, description) 
            VALUES ('$title', '$folder', '$author', '$category', '$quantity', '$description')";

    $run = mysqli_query($conn, $sql);

    // Check if the insert was successful
    if ($run) {
        echo "<script>alert('Book added successfully');</script>";
    } else {
        echo "<script>alert('Failed to add book');</script>";
    }
}
?>

  <div class="add_user-box" style="height: 120vh;">
    <h1>Add book</h1>
    <div class="adU-flx">
      <form class="form-control m-2 p-2" action="" method="POST" enctype="multipart/form-data">
        <label class="m-2"><strong>Title*</strong></label>
        <input type="text" class="form-control" name="title" required />

        <label class="m-2"><strong>Cover page*</strong></label>
        <input type="file" class="form-control" name="cover" required />

        <label class="m-2"><strong>Author*</strong></label>
        <input type="text" class="form-control" name="author" required />

        <label class="m-2"><strong>Category*</strong></label>
        <input type="text" class="form-control" name="category" required />

        <label class="m-2"><strong>Quantity*</strong></label>
        <input type="text" class="form-control" name="quantity" required />

        <label class="m-2"><strong>Description*</strong></label>
        <textarea class="form-control mb-2" name="description" required></textarea>

        <button class="btn btn-success form-control" name="add">Add Book</button>
      </form>

    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>