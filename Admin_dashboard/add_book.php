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
    <div class="add_user-box" style="height: 120vh;">
      <h1>Add book</h1>
      <div class="adU-flx">
        <form class="form-control m-2 p-2" action="">
          <label for="" class="m-2"><strong>Title*</strong></label>
          <input type="text" class="form-control" />
          <label for="" class="m-2"><Strong>Cover page*</Strong></label>
          <input type="file" class="form-control">
          <label for="" class="m-2"> <strong>Author*</strong> </label>
          <input type="text" class="form-control" />
          <label for="" class="m-2"><strong>category*</strong></label>
          <input type="password" class="form-control mb-2" />
          <label for="" class="m-2"> <strong>Quantity*</strong> </label>
          <input type="text" class="form-control" />
          <label for="" class="m-2"> <strong>Description*</strong> </label>
          <textarea name="" class="form-control mb-2"></textarea>
          <button class="btn btn-success form-control mx-2">add book</button>
        </form>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
