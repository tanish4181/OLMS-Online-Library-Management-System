<?php
// user login
session_start();
$success = "";
$error = "";
require __DIR__ . '/../database/config.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = $_POST['email'];
  $password = $_POST['password'];
  $sql = "SELECT * FROM users WHERE email = '$email'";
  $result = mysqli_query($conn, $sql);
  if ($result && mysqli_num_rows($result) == 1) {
    $user = mysqli_fetch_assoc($result);
    if (password_verify($password, $user['password'])) {
      $_SESSION['username'] = $user['username'];
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['role'] = 'user'; 
      $success = "Login successful! Redirecting...";
      header("refresh:2; url=../dashboard/user_dashboard.php"); 
      exit();
    } else {
      $error = "Invalid password!";
    }
  } else {
    $error = "User not found!";
  }
  mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>User Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="/olms/asset/style.css" />
</head>

<body>
  <?php include __DIR__ . '/../navbar.php'; ?>
  <section class="bg"></section>

  <section class="to-check">
    <div class="form-box">
      <div class="content">
        <div id="user-login-heading">
          <h3><i class="bi bi-shield-lock me-2"></i>User Login</h3>
        </div>

        <?php if ($success): ?>
          <div class="alert alert-success"><?= $success ?></div>
        <?php elseif ($error): ?>
          <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <div class="form">
          <form action="" method="post">
            <label class="my-2 mt-1" for="email">Email address</label>
            <input type="email" name="email" class="form-control" required placeholder="Enter email" />

            <label class="my-2" for="password">Password</label>
            <input type="password" name="password" class="form-control" required placeholder="Password" />

            <button type="submit" class="user-btn btn btn-warning mt-3">
              <i class="bi-shield-check me-2"></i>User Login
            </button>
          </form>
        </div>
      </div>
    </div>
  </section>
</body>
</html>
