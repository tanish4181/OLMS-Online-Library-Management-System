<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Document</title>
  <link rel="stylesheet" href="../asset/style.css" />
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
    rel="stylesheet" />
</head>

<body class="admin-dashboard">
  <?php
  include("navbar_admin.php");
  ?>
  <!-- manage user -->

  <div class="mBook-main-box">
    <div
      style="display: flex; justify-content: space-between"
      class="mbook-heading">
      <h1>Manage users</h1>
      <div> <a href="./add_user.html"><button class="btn btn-danger"> add user</button></a> </div>
    </div>
    <div class="mbook-table">
      <table class="user-table">
        <thead>
          <tr class="user-thead-row">
            <th>Username</th>
            <th>Email</th>
            <th>User type</th>
            <th>Books borrowed</th>
            <th>Created</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <tr class="user-tbody-row">
            <td>admin</td>

            <td>email</td>
            <td>member</td>

            <td>0rs</td>
            <td>date</td>
            <td><button style="background-color:#dc3545; color: white;">edit</button>
              <button style="background-color:#dc3545; color: white;">Assign book</button>
              <button style="background-color:#dc3545; color: white;">Return book</button>
            </td>
          </tr>
          <tr class="user-tbody-row">
            <td>unames</td>

            <td>email</td>
            <td>member</td>

            <td>0rs</td>
            <td>date</td>
            <td><button style="background-color:#dc3545; color: white;">edit</button>
              <button style="background-color:#dc3545; color: white;">Assign book</button>
              <button style="background-color:#dc3545; color: white;">Return book</button>
          </tr>
          <tr class="user-tbody-row">
            <td>unames</td>

            <td>email</td>
            <td>member</td>

            <td>0rs</td>
            <td>date</td>
            <td><button style="background-color:#dc3545; color: white;">edit</button>
              <button style="background-color:#dc3545; color: white;">Assign book</button>
              <button style="background-color:#dc3545; color: white;">Return book</button>
          </tr>
          <tr class="user-tbody-row">
            <td>unames</td>

            <td>email</td>
            <td>member</td>

            <td>0rs</td>
            <td>date</td>
            <td><button style="background-color:#dc3545; color: white;">edit</button>
              <button style="background-color:#dc3545; color: white;">Assign book</button>
              <button style="background-color:#dc3545; color: white;">Return book</button>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</body>

</html>