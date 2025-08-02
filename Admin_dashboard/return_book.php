<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../auth/adminLogin.php");
    exit();
}

include("../database/config.php");
include("../includes/fine_calculator.php");

$message = "";
$message_type = "";

// Handle book return
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['return_book'])) {
    $issue_id = $_POST['issue_id'];
    $book_id = $_POST['book_id'];
    
    // Start transaction for data consistency
    mysqli_begin_transaction($conn);
    
    try {
        // Get issue details
        $get_issue = "SELECT * FROM book_issues WHERE id = ?";
        $stmt = mysqli_prepare($conn, $get_issue);
        mysqli_stmt_bind_param($stmt, "i", $issue_id);
        mysqli_stmt_execute($stmt);
        $issue = mysqli_stmt_get_result($stmt)->fetch_assoc();
        
        if ($issue) {
            // Calculate final fine
            $fine_info = getFineInfo($conn, $issue_id);
            $final_fine = $fine_info ? $fine_info['current_fine'] : 0;
            
            // Update book issue record
            $update_issue = "UPDATE book_issues SET 
                           status = 'returned', 
                           return_date = NOW(), 
                           fine_amount = ? 
                           WHERE id = ?";
            $stmt = mysqli_prepare($conn, $update_issue);
            mysqli_stmt_bind_param($stmt, "di", $final_fine, $issue_id);
            
            if (mysqli_stmt_execute($stmt)) {
                // Increase book quantity by 1
                $update_quantity = "UPDATE books SET quantity = quantity + 1 WHERE id = ?";
                $stmt = mysqli_prepare($conn, $update_quantity);
                mysqli_stmt_bind_param($stmt, "i", $book_id);
                
                if (mysqli_stmt_execute($stmt)) {
                    mysqli_commit($conn);
                    $message = "Book returned successfully!";
                    if ($final_fine > 0) {
                        $message .= " Fine amount: ₹" . number_format($final_fine, 2);
                    }
                    $message_type = "success";
                } else {
                    throw new Exception("Error updating book quantity");
                }
            } else {
                throw new Exception("Error updating issue record");
            }
        } else {
            throw new Exception("Issue record not found");
        }
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $message = "Error returning book: " . $e->getMessage();
        $message_type = "danger";
    }
}

// Handle fine payment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pay_fine'])) {
    $issue_id = $_POST['issue_id'];
    
    if (markFineAsPaid($conn, $issue_id)) {
        $message = "Fine marked as paid successfully!";
        $message_type = "success";
    } else {
        $message = "Error processing fine payment.";
        $message_type = "danger";
    }
}

// Update all fines first
updateAllFines($conn);

// Get all current book issues
$current_issues_query = "SELECT bi.*, u.fullname, u.username, b.title, b.author, b.cover 
                        FROM book_issues bi 
                        JOIN users u ON bi.user_id = u.id 
                        JOIN books b ON bi.book_id = b.id 
                        WHERE bi.status IN ('issued', 'overdue') 
                        ORDER BY bi.due_date ASC";
$current_issues = mysqli_query($conn, $current_issues_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Return Books - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../asset/style.css">
</head>
<body class="admin-dashboard">
    <!-- Include admin navbar -->
    <?php include("navbar_admin.php"); ?>
    
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <h2 class="mb-4">Return Books</h2>
                
                <!-- Display message if any -->
                <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <!-- Current Issues -->
                <div class="card">
                    <div class="card-header">
                        <h4>Currently Issued Books</h4>
                        <small class="text-muted">Click "Return Book" to process returns and handle fines</small>
                    </div>
                    <div class="card-body">
                        <?php if (mysqli_num_rows($current_issues) > 0): ?>
                        <div class="row">
                            <?php while ($issue = mysqli_fetch_assoc($current_issues)): ?>
                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="row g-0">
                                        <div class="col-md-4">
                                            <img src="<?php echo htmlspecialchars($issue['cover']); ?>" 
                                                 class="img-fluid rounded-start h-100" 
                                                 alt="<?php echo htmlspecialchars($issue['title']); ?>"
                                                 style="object-fit: cover;">
                                        </div>
                                        <div class="col-md-8">
                                            <div class="card-body">
                                                <h5 class="card-title"><?php echo htmlspecialchars($issue['title']); ?></h5>
                                                <p class="card-text">
                                                    <strong>User:</strong> <?php echo htmlspecialchars($issue['fullname']); ?><br>
                                                    <strong>Author:</strong> <?php echo htmlspecialchars($issue['author']); ?><br>
                                                    <strong>Issued:</strong> <?php echo date('M d, Y', strtotime($issue['issue_date'])); ?><br>
                                                    <strong>Due Date:</strong> <?php echo date('M d, Y', strtotime($issue['due_date'])); ?><br>
                                                    <strong>Status:</strong> 
                                                    <span class="badge bg-<?php echo $issue['status'] == 'issued' ? 'success' : 'danger'; ?>">
                                                        <?php echo ucfirst($issue['status']); ?>
                                                    </span>
                                                </p>
                                                
                                                <?php
                                                $fine_info = getFineInfo($conn, $issue['id']);
                                                if ($fine_info && $fine_info['is_overdue']):
                                                ?>
                                                <div class="alert alert-warning py-2">
                                                    <strong>Fine:</strong> ₹<?php echo number_format($fine_info['current_fine'], 2); ?><br>
                                                    <small>Days overdue: <?php echo $fine_info['days_overdue']; ?></small>
                                                </div>
                                                <?php endif; ?>
                                                
                                                <div class="d-flex gap-2">
                                                    <form method="POST" style="display: inline;">
                                                        <input type="hidden" name="issue_id" value="<?php echo $issue['id']; ?>">
                                                        <input type="hidden" name="book_id" value="<?php echo $issue['book_id']; ?>">
                                                        <button type="submit" name="return_book" class="btn btn-primary btn-sm">
                                                            Return Book
                                                        </button>
                                                    </form>
                                                    
                                                    <?php if ($fine_info && $fine_info['is_overdue'] && !$issue['fine_paid']): ?>
                                                    <form method="POST" style="display: inline;">
                                                        <input type="hidden" name="issue_id" value="<?php echo $issue['id']; ?>">
                                                        <button type="submit" name="pay_fine" class="btn btn-warning btn-sm">
                                                            Mark Fine as Paid
                                                        </button>
                                                    </form>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                        <?php else: ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            No books are currently issued.
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Returned Books History -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h4>Recently Returned Books</h4>
                    </div>
                    <div class="card-body">
                        <?php
                        $returned_books_query = "SELECT bi.*, u.fullname, u.username, b.title, b.author 
                                               FROM book_issues bi 
                                               JOIN users u ON bi.user_id = u.id 
                                               JOIN books b ON bi.book_id = b.id 
                                               WHERE bi.status = 'returned' 
                                               ORDER BY bi.return_date DESC 
                                               LIMIT 10";
                        $returned_books = mysqli_query($conn, $returned_books_query);
                        ?>
                        
                        <?php if (mysqli_num_rows($returned_books) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Book</th>
                                        <th>Issue Date</th>
                                        <th>Return Date</th>
                                        <th>Fine Amount</th>
                                        <th>Fine Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($returned = mysqli_fetch_assoc($returned_books)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($returned['fullname'] . ' (' . $returned['username'] . ')'); ?></td>
                                        <td><?php echo htmlspecialchars($returned['title'] . ' by ' . $returned['author']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($returned['issue_date'])); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($returned['return_date'])); ?></td>
                                        <td>
                                            <?php if ($returned['fine_amount'] > 0): ?>
                                                ₹<?php echo number_format($returned['fine_amount'], 2); ?>
                                            <?php else: ?>
                                                No Fine
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($returned['fine_amount'] > 0): ?>
                                                <span class="badge bg-<?php echo $returned['fine_paid'] ? 'success' : 'warning'; ?>">
                                                    <?php echo $returned['fine_paid'] ? 'Paid' : 'Unpaid'; ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-success">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <div class="alert alert-info">
                            No recently returned books found.
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
