<?php
// Start the session to check if admin is logged in
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../auth/adminLogin.php");
    exit();
}

// Include the database connection file
include('../database/config.php');

// Set default values for our variables
$total_users = 0;
$total_admins = 0;
$total_all_users = 0;
$total_books = 0;
$borrowed_books = 0;
$total_quantity = 0;

// Count total members (only users, not admins)
$sql = "SELECT COUNT(*) as total_users FROM users WHERE role = 'user'";
$result = mysqli_query($conn, $sql);
if ($result) {
    $data = mysqli_fetch_assoc($result);
    $total_users = $data['total_users'];
}

// Count total admins
$sql_admin = "SELECT COUNT(*) as total_admins FROM users WHERE role = 'admin'";
$result_admin = mysqli_query($conn, $sql_admin);
if ($result_admin) {
    $data_admin = mysqli_fetch_assoc($result_admin);
    $total_admins = $data_admin['total_admins'];
}

// Count total users (both users and admins)
$sql_total = "SELECT COUNT(*) as total_all_users FROM users";
$result_total = mysqli_query($conn, $sql_total);
if ($result_total) {
    $data_total = mysqli_fetch_assoc($result_total);
    $total_all_users = $data_total['total_all_users'];
}

// Count total books from books table
$sql_books = "SELECT COUNT(*) as total_books FROM books";
$result_books = mysqli_query($conn, $sql_books);
if ($result_books) {
    $data_books = mysqli_fetch_assoc($result_books);
    $total_books = $data_books['total_books'];
}

// Count total quantity of books (sum of all book quantities)
$sql_total_quantity = "SELECT SUM(quantity) as total_quantity FROM books";
$result_quantity = mysqli_query($conn, $sql_total_quantity);
if ($result_quantity) {
    $data_quantity = mysqli_fetch_assoc($result_quantity);
    $total_quantity = $data_quantity['total_quantity'] ? $data_quantity['total_quantity'] : 0;
}

// Check if borrowings table exists and count borrowed books
$check_borrowing_table = "SHOW TABLES LIKE 'book_issues'";
$borrowing_table_exists = mysqli_query($conn, $check_borrowing_table);
if ($borrowing_table_exists && mysqli_num_rows($borrowing_table_exists) > 0) {
  $sql_borrowed = "SELECT COUNT(*) as borrowed_books FROM book_issues WHERE status IN ('issued', 'overdue')";
  $result_borrowed = mysqli_query($conn, $sql_borrowed);
  if ($result_borrowed) {
    $data_borrowed = mysqli_fetch_assoc($result_borrowed);
    $borrowed_books = $data_borrowed['borrowed_books'];
  }
}

// Get recent books for display
$sql_recent_books = "SELECT * FROM books ORDER BY id DESC LIMIT 4";
$result_recent = mysqli_query($conn, $sql_recent_books);
$recent_books = array();
if ($result_recent) {
    while ($row = mysqli_fetch_assoc($result_recent)) {
        $recent_books[] = $row;
    }
}

// Get recent borrowings for the table
$recent_borrowings_query = "SELECT bi.*, u.fullname, u.username, b.title, b.author 
                           FROM book_issues bi 
                           JOIN users u ON bi.user_id = u.id 
                           JOIN books b ON bi.book_id = b.id 
                           WHERE bi.status IN ('issued', 'overdue') 
                           ORDER BY bi.issue_date DESC 
                           LIMIT 5";
$recent_borrowings_result = mysqli_query($conn, $recent_borrowings_query);

// Get overdue books
$overdue_books_query = "SELECT bi.*, u.fullname, u.username, b.title, b.author 
                       FROM book_issues bi 
                       JOIN users u ON bi.user_id = u.id 
                       JOIN books b ON bi.book_id = b.id 
                       WHERE bi.due_date < CURDATE() AND bi.status = 'issued'
                       ORDER BY bi.due_date ASC 
                       LIMIT 5";
$overdue_books_result = mysqli_query($conn, $overdue_books_query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard - OLMS</title>
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
    rel="stylesheet" />
  <link rel="stylesheet" href="../asset/style.css" />
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="admin-dashboard">
  <?php
  include("navbar_admin.php");
  ?>

  <div class="main-content">
  <!-- welcome heading -->
  <div class="user-dheading">
    <h1 style="color: #dc3545;" class="user-dtext">Welcome Library Administrator</h1>
    <small class="user-hsmall-text">Discover thousands of books and resources at your fingertips</small>
  </div>

  <!-- User Statistics Summary -->
  <div class="container" style="margin-bottom: 20px;">
    <div class="row">
      <div class="col-md-12">
        <div class="alert alert-info" style="text-align: center;">
          <strong>System Statistics Summary:</strong> 
          Total Users: <span class="badge bg-primary"><?php echo $total_all_users; ?></span> | 
          Regular Members: <span class="badge bg-success"><?php echo $total_users; ?></span> | 
          Administrators: <span class="badge bg-warning"><?php echo $total_admins; ?></span>
          <?php if ($total_books > 0): ?>
          | Total Books: <span class="badge bg-info"><?php echo $total_books; ?></span>
          | Total Copies: <span class="badge bg-info"><?php echo $total_quantity; ?></span>
          <?php endif; ?>
          <?php if ($borrowed_books > 0): ?>
          | Borrowed Books: <span class="badge bg-secondary"><?php echo $borrowed_books; ?></span>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- user details boxes -->
  <div class="user-details-box">
    <div class="user-dMainBox">
      <div class="user-total-books">
        <div class="user-numbers">
          <h1 style="color: #dc3545;"><?php echo $total_books; ?></h1>
        </div>
        <small class="user-small-txt">Total Books</small>
      </div>
      
      <div class="user-regm">
        <div class="user-numbers">
          <h1 style="color: red;"><?php echo $total_users; ?></h1>
        </div>
        <small class="user-small-txt">Registered Members</small>
      </div>
      
      <div class="user-borrowig">
        <div class="user-numbers">
          <h1 style="color: #dc3545;"><?php echo $total_quantity; ?></h1>
        </div>
        <small class="user-small-txt">Total Copies</small>
      </div>
      
      <div class="user-books-categories">
        <div class="user-numbers">
          <h1 style="color: #28a745;"><?php echo $borrowed_books; ?></h1>
        </div>
        <small class="user-small-txt">Borrowed Books</small>
      </div>
      
      <div class="user-books-categories">
        <div class="user-numbers">
          <h1 style="color: #ffc107;"><?php echo $total_admins; ?></h1>
        </div>
        <small class="user-small-txt">Administrators</small>
      </div>
      
      <div class="user-books-categories">
        <div class="user-numbers">
          <h1 style="color: #17a2b8;"><?php echo $total_all_users; ?></h1>
        </div>
        <small class="user-small-txt">Total Users</small>
      </div>
    </div>
  </div>

  <!-- Book Information -->
  <?php if ($total_books > 0): ?>
  <div class="container" style="margin-bottom: 20px;">
    <div class="row">
      <div class="col-md-12">
        <div class="alert alert-success" style="text-align: center;">
          <strong>Books Database Active:</strong><br>
          <small>Total Books: <?php echo $total_books; ?> | Total Copies: <?php echo $total_quantity; ?> | Borrowed: <?php echo $borrowed_books; ?></small>
        </div>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <!-- Recent Books Section -->
  <?php if (!empty($recent_books)): ?>
  <div class="container" style="margin-bottom: 20px;">
    <h2>Recent Books in Database</h2>
    <div class="row">
      <?php foreach ($recent_books as $book): ?>
      <div class="col-md-3 mb-3">
        <div class="card">
          <div class="card-body text-center">
            <h6 class="card-title"><?php echo htmlspecialchars($book['title']); ?></h6>
            <p class="card-text small">
              <strong>Author:</strong> <?php echo htmlspecialchars($book['author']); ?><br>
              <strong>Category:</strong> <?php echo htmlspecialchars($book['category']); ?><br>
              <strong>Quantity:</strong> <?php echo $book['quantity']; ?>
            </p>
            <span class="badge bg-<?php echo $book['quantity'] > 0 ? 'success' : 'danger'; ?>">
              <?php echo $book['quantity'] > 0 ? 'Available' : 'Out of Stock'; ?>
            </span>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

  <div class="container">
    <h2>Quick Actions</h2>
    <div class="admin-action">
       <a href="./add_book.php"><button class="admin-aBtn">Add New Book</button></a> 
       <a href="./add_user.php"><button class="admin-aBtn">Add New User</button></a> 
       <a href="./return_book.php"><button class="admin-aBtn">View All Borrowing</button></a> 
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
              <th>Issue Date</th>
              <th>Due Date</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php if (mysqli_num_rows($recent_borrowings_result) > 0): ?>
              <?php while ($borrowing = mysqli_fetch_assoc($recent_borrowings_result)): ?>
              <tr class="user-tbody-row">
                <td><?php echo htmlspecialchars($borrowing['fullname']); ?></td>
                <td><?php echo htmlspecialchars($borrowing['title']); ?></td>
                <td><?php echo date('M d, Y', strtotime($borrowing['issue_date'])); ?></td>
                <td><?php echo date('M d, Y', strtotime($borrowing['due_date'])); ?></td>
                <td>
                  <span class="badge bg-<?php echo $borrowing['status'] == 'issued' ? 'success' : 'danger'; ?>">
                    <?php echo ucfirst($borrowing['status']); ?>
                  </span>
                </td>
              </tr>
              <?php endwhile; ?>
            <?php else: ?>
            <tr class="user-tbody-row">
              <td colspan="5" style="text-align: center; color: #6c757d;">No recent borrowings found</td>
            </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
      
      <div class="overdue-books">
        <h2 class="admin-action-heading">Overdue Books</h2>
        <?php if (mysqli_num_rows($overdue_books_result) > 0): ?>
        <table class="admin-table">
          <thead>
            <tr class="user-thead-row">
              <th>Member</th>
              <th>Book</th>
              <th>Due Date</th>
              <th>Days Overdue</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($overdue = mysqli_fetch_assoc($overdue_books_result)): ?>
            <tr class="user-tbody-row">
              <td><?php echo htmlspecialchars($overdue['fullname']); ?></td>
              <td><?php echo htmlspecialchars($overdue['title']); ?></td>
              <td><?php echo date('M d, Y', strtotime($overdue['due_date'])); ?></td>
              <td>
                <span class="badge bg-danger">
                  <?php 
                  $days_overdue = (strtotime(date('Y-m-d')) - strtotime($overdue['due_date'])) / (60 * 60 * 24);
                  echo floor($days_overdue) . ' days';
                  ?>
                </span>
              </td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
        <?php else: ?>
        <div style="padding: 20px; text-align: center; color: #6c757d;">
          No overdue books found
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
  </div>
  <?php
  include("footer.php");
  ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>