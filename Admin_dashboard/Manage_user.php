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
      <div> <a href="./add_user.php"><button class="btn btn-danger"> add user</button></a> </div>
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
        <?php
        include("config.php");

        // Pagination setup
        $limit = 5;
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $start_from = ($page - 1) * $limit;

        // Fetch users
        $query = "SELECT * FROM users LIMIT $start_from, $limit";
        $result = mysqli_query($conn, $query);

        // Store users for modal generation
        $users = [];

        // Display rows
        while ($row = mysqli_fetch_assoc($result)) {
          $users[] = $row; // Store for later modal generation
          echo "<tr class='user-tbody-row'>
    <td>{$row['username']}</td>
    <td>{$row['email']}</td>
    <td>{$row['role']}</td>
    <td>{$row['books_borrowed']}</td>
    <td>{$row['created_at']}</td>
    <td>
      <button class='btn btn-sm btn-primary' data-bs-toggle='modal' data-bs-target='#editModal' data-id='{$row['id']}'>Edit</button>
      <button class='btn btn-sm btn-warning assignBtn' 
        data-bs-toggle='modal' 
        data-bs-target='#assignBookModal' 
        data-id='{$row['id']}'>Assign Book</button>
      <button class='btn btn-sm btn-danger returnBtn' 
        data-bs-toggle='modal' 
        data-bs-target='#returnModal{$row['id']}' 
        data-id='{$row['id']}'>Return Book</button>
    </td>
  </tr>";
        }
        ?>
        </tbody>
      </table>
      
      <?php
      // Pagination
      $sql = "SELECT COUNT(id) FROM users";
      $rs_result = mysqli_query($conn, $sql);
      $row = mysqli_fetch_row($rs_result);
      $total_records = $row[0];
      $total_pages = ceil($total_records / $limit);

      echo '<nav><ul class="pagination">';
      for ($i = 1; $i <= $total_pages; $i++) {
        echo "<li class='page-item'><a class='page-link' href='?page=" . $i . "'>" . $i . "</a></li>";
      }
      echo '</ul></nav>';
      ?>

    </div>
  </div>

  <!-- Edit Modal -->
  <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <!-- Add your edit form here -->
          <p>Edit modal content goes here</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Assign Book Modal -->
  <div class="modal fade" id="assignBookModal" tabindex="-1" aria-labelledby="assignBookLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form action="assign_book.php" method="POST">
          <div class="modal-header">
            <h5 class="modal-title" id="assignBookLabel">Assign Book to User</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="user_id" id="assign-user-id">
            <div class="mb-3">
              <label for="book_id" class="form-label">Select Book</label>
              <select name="book_id" class="form-control" required>
                <option value="">-- Select a Book --</option>
                <?php
                $book_result = mysqli_query($conn, "SELECT id, title FROM books WHERE quantity > 0");
                while ($book_row = mysqli_fetch_assoc($book_result)) {
                  echo "<option value='{$book_row['id']}'>{$book_row['title']}</option>";
                }
                ?>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-success">Assign Book</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Return Book Modals - Generate one for each user -->
  <?php foreach ($users as $user): ?>
  <div class="modal fade" id="returnModal<?= $user['id'] ?>" tabindex="-1" aria-labelledby="returnModalLabel<?= $user['id'] ?>" aria-hidden="true">
    <div class="modal-dialog">
      <form method="POST" action="return_book.php">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Return Book - <?= htmlspecialchars($user['username']) ?></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
            <label for="book_id_<?= $user['id'] ?>">Select Book to Return:</label>
            <select name="book_id" id="book_id_<?= $user['id'] ?>" class="form-select" required>
              <option value="">-- Select a Book --</option>
              <?php
              $uid = $user['id'];
              $borrowed_query = "SELECT b.id, b.title FROM borrowed_books bb JOIN books b ON bb.book_id = b.id WHERE bb.user_id = $uid AND bb.returned_at IS NULL";
              $borrowed_result = mysqli_query($conn, $borrowed_query);
              while($book = mysqli_fetch_assoc($borrowed_result)) {
                echo "<option value='{$book['id']}'>" . htmlspecialchars($book['title']) . "</option>";
              }
              ?>
            </select>
          </div>
          <div class="modal-footer">
            <button type="submit" name="return_book" class="btn btn-success">Return</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          </div>
        </div>
      </form>
    </div>
  </div>
  <?php endforeach; ?>

  <script>
  document.addEventListener("DOMContentLoaded", function () {
    // Handle assign book modal
    const assignButtons = document.querySelectorAll(".assignBtn");
    assignButtons.forEach((btn) => {
      btn.addEventListener("click", function () {
        const userId = this.getAttribute("data-id");
        document.getElementById("assign-user-id").value = userId;
      });
    });

    // Handle edit modal if needed
    const editButtons = document.querySelectorAll("[data-bs-target='#editModal']");
    editButtons.forEach((btn) => {
      btn.addEventListener("click", function () {
        const userId = this.getAttribute("data-id");
        // You can add logic here to populate edit form with user data
        console.log("Edit user ID:", userId);
      });
    });
  });
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>