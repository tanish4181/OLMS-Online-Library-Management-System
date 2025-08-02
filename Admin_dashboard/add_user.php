<?php
// Start the session to check if admin is logged in
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../auth/adminLogin.php");
    exit();
}

// Variables to store success and error messages
$success = "";
$error = "";

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Connect to database
    $conn = mysqli_connect("localhost", "root", "", "olms");

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Get the form data
    $username = $_POST['username'];
    $email = $_POST['email'];
    $fullname = $_POST['fullname'];
    $address = $_POST['address'];
    $password = $_POST['password'];

    // Hash the password before storing it in the database
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert the new user into the database
    $sql = "INSERT INTO users (username, email, fullname, address, password) 
            VALUES ('$username', '$email', '$fullname', '$address', '$hashed_password')";

    if (mysqli_query($conn, $sql)) {
       $success = "User '$username' added successfully!";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }

    // Close the database connection
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Add User</title>
  <link rel="stylesheet" href="../asset/style.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
</head>
<body class="addUser">
  <?php include("navbar_admin.php"); ?>

  <div class="add_user-box">
    <h1>Add User</h1>

    <?php if ($success): ?>
      <div class="alert alert-success"><?= $success ?></div>
    <?php elseif ($error): ?>
      <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <div class="adU-flx">
      <form class="form-control m-2 p-2" action="" method="POST">
        <label class="m-2"><strong>Username*</strong></label>
        <input type="text" name="username" class="form-control" required />

        <label class="m-2"><strong>Email*</strong></label>
        <input type="email" name="email" class="form-control" required />

        <label class="m-2"><strong>Fullname*</strong></label>
        <input type="text" name="fullname" class="form-control" required />

        <label class="m-2"><strong>Address*</strong></label>
        <textarea name="address" class="form-control" required></textarea>

        <label class="m-2"><strong>Password*</strong></label>
        <input type="password" name="password" class="form-control mb-2" required />

        <button class="btn btn-warning form-control mt-2">Add User</button>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
