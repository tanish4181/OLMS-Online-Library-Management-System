<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document</title>
    <link rel="stylesheet" href="../asset/style.css" />
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
  </head>
  <body class="addUser">
    <?php
    include("navbar_admin.php");
    ?>
    <div class="add_user-box">
      <h1>Add user</h1>
      <div class="adU-flx">
        <form class="form-control m-2 p-2" action="">
          <label for="" class="m-2"><strong>username*</strong></label>
          <input type="text" class="form-control" />
          <label for="" class="m-2"> <strong>email*</strong> </label>
          <input type="text" class="form-control" />
          <label for="" class="m-2"> <strong>Fullname*</strong> </label>
          <input type="text" class="form-control" />
          <label for="" class="m-2"> <strong>Address*</strong> </label>
          <textarea name="" class="form-control"></textarea>
          <label for="" class="m-2"><strong>Password*</strong></label>
          <input type="password" class="form-control mb-2" />
          <button class="btn btn-warning form-control mt-2" >add user </button>
        </form>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
