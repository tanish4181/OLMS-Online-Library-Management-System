<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../auth/adminLogin.php");
    exit();
}

include("../database/config.php");

$message = "";
$message_type = "";

// Handle book issue form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['issue_book'])) {
    $user_id = $_POST['user_id'];
    $book_id = $_POST['book_id'];
    $due_date = $_POST['due_date'];
    
    // Validate inputs
    if ($user_id && $book_id && $due_date) {
        // Check if book is available
        $check_availability = "SELECT quantity FROM books WHERE id = ?";
        $stmt = mysqli_prepare($conn, $check_availability);
        mysqli_stmt_bind_param($stmt, "i", $book_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $book = mysqli_fetch_assoc($result);
        
        if ($book['quantity'] > 0) {
            // Start transaction for data consistency
            mysqli_begin_transaction($conn);
            
            try {
                // Insert book issue record
                $issue_query = "INSERT INTO book_issues (user_id, book_id, due_date, status) VALUES (?, ?, ?, 'issued')";
                $stmt = mysqli_prepare($conn, $issue_query);
                mysqli_stmt_bind_param($stmt, "iis", $user_id, $book_id, $due_date);
                
                if (mysqli_stmt_execute($stmt)) {
                    // Reduce book quantity by 1
                    $update_quantity = "UPDATE books SET quantity = quantity - 1 WHERE id = ?";
                    $stmt = mysqli_prepare($conn, $update_quantity);
                    mysqli_stmt_bind_param($stmt, "i", $book_id);
                    
                    if (mysqli_stmt_execute($stmt)) {
                        mysqli_commit($conn);
                        $message = "Book issued successfully!";
                        $message_type = "success";
                    } else {
                        throw new Exception("Error updating book quantity");
                    }
                } else {
                    throw new Exception("Error issuing book");
                }
            } catch (Exception $e) {
                mysqli_rollback($conn);
                $message = "Error issuing book: " . $e->getMessage();
                $message_type = "danger";
            }
        } else {
            $message = "This book is currently out of stock.";
            $message_type = "warning";
        }
    } else {
        $message = "Please fill all required fields.";
        $message_type = "warning";
    }
}

// Get all users for dropdown
$users_query = "SELECT id, fullname, username FROM users WHERE role = 'user' ORDER BY fullname";
$users_result = mysqli_query($conn, $users_query);

// Get all available books for dropdown
$books_query = "SELECT id, title, author, quantity FROM books WHERE quantity > 0 ORDER BY title";
$books_result = mysqli_query($conn, $books_query);

// Set default due date (14 days from now)
$default_due_date = date('Y-m-d', strtotime('+14 days'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Issue Book - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../asset/style.css">
</head>
<body class="admin-dashboard">
    <!-- Include admin navbar -->
    <?php include("navbar_admin.php"); ?>
    
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <h2 class="mb-4">Issue Book to User</h2>
                
                <!-- Display message if any -->
                <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <!-- Issue Book Form -->
                <div class="card">
                    <div class="card-header">
                        <h4>Issue Book</h4>
                        <small class="text-muted">Select a user and book to issue</small>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="user_id" class="form-label">Select User *</label>
                                    <select class="form-control" id="user_id" name="user_id" required>
                                        <option value="">Choose a user...</option>
                                        <?php while ($user = mysqli_fetch_assoc($users_result)): ?>
                                        <option value="<?php echo $user['id']; ?>">
                                            <?php echo htmlspecialchars($user['fullname'] . ' (' . $user['username'] . ')'); ?>
                                        </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="book_id" class="form-label">Select Book *</label>
                                    <select class="form-control" id="book_id" name="book_id" required>
                                        <option value="">Choose a book...</option>
                                        <?php while ($book = mysqli_fetch_assoc($books_result)): ?>
                                        <option value="<?php echo $book['id']; ?>">
                                            <?php echo htmlspecialchars($book['title'] . ' by ' . $book['author'] . ' (' . $book['quantity'] . ' available)'); ?>
                                        </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="due_date" class="form-label">Due Date *</label>
                                    <input type="date" 
                                           class="form-control" 
                                           id="due_date" 
                                           name="due_date" 
                                           value="<?php echo $default_due_date; ?>"
                                           min="<?php echo date('Y-m-d'); ?>"
                                           required>
                                    <small class="text-muted">Default is 14 days from today</small>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <button type="submit" name="issue_book" class="btn btn-primary">
                                    <i class="bi bi-book"></i> Issue Book
                                </button>
                                <a href="admindashboard.php" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Current Issues Table -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h4>Recent Book Issues</h4>
                    </div>
                    <div class="card-body">
                        <?php
                        $recent_issues_query = "SELECT bi.*, u.fullname, u.username, b.title, b.author 
                                              FROM book_issues bi 
                                              JOIN users u ON bi.user_id = u.id 
                                              JOIN books b ON bi.book_id = b.id 
                                              WHERE bi.status IN ('issued', 'overdue') 
                                              ORDER BY bi.issue_date DESC 
                                              LIMIT 10";
                        $recent_issues = mysqli_query($conn, $recent_issues_query);
                        ?>
                        
                        <?php if (mysqli_num_rows($recent_issues) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Book</th>
                                        <th>Issue Date</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($issue = mysqli_fetch_assoc($recent_issues)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($issue['fullname'] . ' (' . $issue['username'] . ')'); ?></td>
                                        <td><?php echo htmlspecialchars($issue['title'] . ' by ' . $issue['author']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($issue['issue_date'])); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($issue['due_date'])); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $issue['status'] == 'issued' ? 'success' : 'danger'; ?>">
                                                <?php echo ucfirst($issue['status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <div class="alert alert-info">
                            No recent book issues found.
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 