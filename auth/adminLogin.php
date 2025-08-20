<?php



ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start the session to store admin data
session_start();

// Include the database connection file
require("../database/config.php");

// Variable to store error message
$message = "";

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the email and password from the form
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $passwordInput = $_POST["password"];

    // Create SQL query to find user with this email and admin role
    $sql = "SELECT * FROM users WHERE email = '$email' AND role = 'admin'";
    $result = mysqli_query($conn, $sql);

    // Check if we found an admin with this email
    if ($result && mysqli_num_rows($result) == 1) {
        // Get the admin data
        $admin = mysqli_fetch_assoc($result);

        // Check if the password is correct
        if (password_verify($passwordInput, $admin["password"])) {
            // Password is correct, so log the admin in
            $_SESSION["admin_logged_in"] = true;
            $_SESSION["admin_id"] = $admin["id"];
            $_SESSION["admin_email"] = $admin["email"];
            $_SESSION["admin_name"] = $admin["fullname"];
            $_SESSION["sta"] = $email; // Keep for backward compatibility
            header("Location: ../Admin_dashboard/admindashboard.php");
            exit();
        } else {
            // Password is wrong
            $message = "Incorrect password. Please check again.";
        }
    } else {
        // No admin found with this email or user is not an admin
        $message = "Admin access denied. Invalid credentials or insufficient privileges.";
    }

    // Close the database connection
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="/OLMS/asset/style.css" />
</head>

<body>
  <?php include("../navbar.php"); ?>
  <section class="bg"></section>
  <section class="to-check">
    <div class="form-box">
      <div class="content">
        <div id="admin-login-heading">
          <h3> <i class="bi bi-shield-lock me-2"></i>Admin Access</h3>
        </div>
        <div class="warning ">
          <small> <i class="bi bi-exclamation-triangle"></i> <strong>Restricted Access:</strong> Admin credentials required.</small>
        </div>
        <?php if ($message): ?>
          <div class="alert alert-danger my-2"><?php echo $message; ?></div>
        <?php endif; ?>
        <div class="form">
          <form action="" method="POST">
            <label class="my-2 mt-1" for="exampleInputEmail1">Email address</label>
            <input
              type="email"
              name="email"
              class="form-control"
              id="exampleInputEmail1"
              aria-describedby="emailHelp"
              placeholder="Enter email" required />
            <label class="my-2" for="exampleInputPassword1">Password</label>
            <input
              type="password"
              name="password"
              class="form-control"
              id="exampleInputPassword1"
              placeholder="Password" required />
            <button type="submit" class="admin-btn btn-primary mt-3">
              <i class="bi-shield-check me-2"></i>Admin Login
            </button>
          </form>
        </div>
      </div>
    </div>
  </section>
</body>

</html>