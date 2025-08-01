<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Document</title>
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
    rel="stylesheet"
  />
  <link rel="stylesheet" href="/OLMS/asset/style.css" />
</head>

<body class="admin-dashboard">
  <?php
  include("navbar_admin.php");
  ?>
  <!-- welcome heading -->
  <div class="user-dheading container">
    <h1 style="color: #dc3545;" class="user-dtext">Welcome Library Adminstrator</h1>
    <small class="user-hsmall-text">Discover thousands of books and resources at your fingertips</small>
  </div>

  <!-- user details boxes -->
  <div class="user-details-box">
    <div class="user-dMainBox">
      <div class="user-total-books">
        <div class="user-numbers">
          <h1 style="color: #dc3545;">4</h1>
        </div>
        <small class="user-small-txt">Total Books </small>
      </div>
      <div class="user-regm">
        <div class="user-numbers">
          <h1 style="color: red;">4</h1>
        </div>
        <small class="user-small-txt">Registered Member</small>
      </div>
      <div class="user-borrowig">
        <div class="user-numbers">
          <h1 style="color: #dc3545;">4</h1>
        </div>
        <small class="user-small-txt">Active Borrowing</small>
      </div>
      <div class="user-books-categories">
        <div class="user-numbers">
          <h1 style="color: #dc3545;">4</h1>
        </div>
        <small class="user-small-txt">Unavailable Books</small>
      </div>
      <div class="user-books-categories">
        <div class="user-numbers">
          <h1 style="color: #dc3545;">4</h1>
        </div>
        <small class="user-small-txt">Overdue Books</small>
      </div>
      <div class="user-books-categories">
        <div class="user-numbers">
          <h1 style="color: #dc3545;">4</h1>
        </div>
        <small class="user-small-txt">Categories</small>
      </div>
    </div>
  </div>
  <div class="container">
    <h2>Quick Actions</h2>
    <div class="admin-action">
      <button class="admin-aBtn">Add New Book</button>
      <button class="admin-aBtn">Add New User</button>
      <button class="admin-aBtn">Manage Categories</button>
      <button class="admin-aBtn">View All Borrowing</button>
    </div>
  </div>
  <div class="container">
    <div class="admin-details" style="flex-wrap: wrap">
      <div class="mbook-table Recent-borrow">
        <h2 class="admin-action-heading">Recent Borrowings</h2>
        <table class="admin-table">
          <thead>
            <tr class="user-thead-row">
              <th>Member</th>
              <th>Book</th>
              <th>Borrow date</th>
              <th>Duedate</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <tr class="user-tbody-row">
              <td>name1</td>
              <td>book1</td>
              <td>date</td>
              <td>date</td>
              <td>active</td>


            </tr>
            <tr class="user-tbody-row">
              <td>name2</td>
              <td>book2</td>
              <td>date</td>
              <td>date</td>
              <td>active</td>

            </tr>
            <tr class="user-tbody-row">
              <td>name3</td>
              <td>book3</td>
              <td>date</td>
              <td>date</td>
              <td>active</td>
            </tr>
            <tr class="user-tbody-row">
              <td>name4</td>
              <td>book4</td>
              <td>date</td>
              <td>date</td>
              <td>active</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="overdue-books">
        <h2 class="admin-action-heading">Overdue Book</h2>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>