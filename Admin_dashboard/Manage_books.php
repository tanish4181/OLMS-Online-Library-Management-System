<?php
include("config.php");

$limit = 5; // books per page
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start_from = ($page - 1) * $limit;

// Fetch books with pagination
$sql = "SELECT * FROM books LIMIT $start_from, $limit";
$result = mysqli_query($conn, $sql);
?>

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

<body>

  <?php
  include("navbar_admin.php");
  ?>
  <div class="mBook-main-box">
    <div
      style="display: flex; justify-content: space-between"
      class="mbook-heading">
      <h1>Manage Books</h1>
      <div>
        <a href="./add_book.php"><button class="btn btn-danger">add Book</button></a>
      </div>
    </div>
    <div class="mbook-table">
      <table class="user-table">
        <thead>
          <tr class="user-thead-row">
            <th>Title</th>
            <th>Author</th>
            <th>Quantity</th>
            <th>Available</th>
            <th>Category</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
          ?>
              <tr class="user-tbody-row">
                <td><?php echo $row['title']; ?></td>
                <td><?php echo $row['author']; ?></td>
                <td><?php echo $row['quantity']; ?></td>
                <td><?php echo $row['quantity']; ?></td> <!-- You can modify if "available" is different -->
                <td><?php echo $row['category']; ?></td>
                <td>
                  <button style="background-color: #dc3545; color: white">edit</button>
                  <button style="background-color: #dc3545; color: white">delete</button>
                </td>
              </tr>
          <?php
            }
          } else {
            echo "<tr><td colspan='6'>No books found</td></tr>";
          }
          ?>
        </tbody>

      </table>
      <?php
      $sql2 = "SELECT COUNT(*) AS total FROM books";
      $result2 = mysqli_query($conn, $sql2);
      $row2 = mysqli_fetch_assoc($result2);
      $total_books = $row2['total'];
      $total_pages = ceil($total_books / $limit);
      ?>

      <div class="pagination mt-3 d-flex justify-content-center">
        <?php if ($page > 1): ?>
          <a class="btn btn-secondary mx-1" href="?page=<?php echo $page - 1; ?>">Previous</a>
        <?php endif; ?>

        <?php if ($page < $total_pages): ?>
          <a class="btn btn-secondary mx-1" href="?page=<?php echo $page + 1; ?>">Next</a>
        <?php endif; ?>
      </div>

    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>