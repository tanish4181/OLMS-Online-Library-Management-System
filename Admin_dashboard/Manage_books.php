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
              $book_id = $row['id'];
          ?>
              <tr class="user-tbody-row">
                <td><?php echo $row['title']; ?></td>
                <td><?php echo $row['author']; ?></td>
                <td><?php echo $row['quantity']; ?></td>
                <td><?php echo $row['quantity']; ?></td>
                <td><?php echo $row['category']; ?></td>
                <td>
                  <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $book_id; ?>">Edit</button>
                  <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $book_id; ?>">Delete</button>
                </td>
              </tr>

              <!--EDIT MODAL -->
              <div class="modal fade" id="editModal<?php echo $book_id; ?>" tabindex="-1">
                <div class="modal-dialog">
                  <form action="edit_book.php" method="POST">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5>Edit Book</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>
                      <div class="modal-body">
                        <input type="hidden" name="id" value="<?php echo $book_id; ?>">
                        <div class="mb-2">
                          <label>Title</label>
                          <input type="text" name="title" class="form-control" value="<?php echo $row['title']; ?>" required>
                        </div>
                        <div class="mb-2">
                          <label>Author</label>
                          <input type="text" name="author" class="form-control" value="<?php echo $row['author']; ?>" required>
                        </div>
                        <div class="mb-2">
                          <label>Quantity</label>
                          <input type="number" name="quantity" class="form-control" value="<?php echo $row['quantity']; ?>" required>
                        </div>
                        <div class="mb-2">
                          <label>Category</label>
                          <input type="text" name="category" class="form-control" value="<?php echo $row['category']; ?>" required>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="submit" name="edit_book" class="btn btn-success">Save</button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>

              <!--DELETE MODAL -->
              <div class="modal fade" id="deleteModal<?php echo $book_id; ?>" tabindex="-1">
                <div class="modal-dialog">
                  <form action="delete_book.php" method="POST">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5>Delete Book</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>
                      <div class="modal-body">
                        Are you sure you want to delete "<strong><?php echo $row['title']; ?></strong>"?
                        <input type="hidden" name="id" value="<?php echo $book_id; ?>">
                      </div>
                      <div class="modal-footer">
                        <button type="submit" name="delete_book" class="btn btn-danger">Delete</button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
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