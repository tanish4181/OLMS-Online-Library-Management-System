<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Document</title>

  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
    rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="/OLMS/asset/style.css" />

</head>

<body>
  <!-- navbar -->
  <?php
  include("../navbar.php");
  ?>
  <section class="bg"></section>
  <section class="to-check">

    <!-- login box -->
    <div class="form-box">
      <div class="content">
        <div id="user-login-heading">
          <h3> <i class="bi bi-shield-lock me-2"></i>User Login</h3>
        </div>

        <div class="form">
          <form class="" action="" method="post">
            <label class="my-2 mt-1" for="exampleInputEmail1">Email address</label>
            <input
              type="email"
              class="form-control"
              id="exampleInputEmail1"
              aria-describedby="emailHelp"
              placeholder="Enter email" />
            <label class="my-2" for="exampleInputPassword1">Password</label>
            <input
              type="password"
              class="form-control"
              id="exampleInputPassword1"
              placeholder="Password" />
            <button type="submit" class="user-btn btn-primary mt-3">
              <i class="bi-shield-check me-2"></i></i>User Login
            </button>
          </form>
        </div>
      </div>
    </div>
  </section>
</body>

</html>