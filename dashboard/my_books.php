<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../auth/UserLogin.php");
    exit();
}

include("../database/config.php");
include("../includes/fine_calculator.php");

$user_id = $_SESSION['user_id'];
$message = "";
$message_type = "";

// Handle book return
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['return_book'])) {
    $issue_id = $_POST['issue_id'];
    $book_id = $_POST['book_id'];
    
    if (returnBook($conn, $issue_id, $book_id)) {
        $message = "Book returned successfully!";
        $message_type = "success";
    } else {
        $message = "Error returning book. Please try again.";
        $message_type = "danger";
    }
}

// Handle fine payment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pay_fine'])) {
    $issue_id = $_POST['issue_id'];
    
    if (markFineAsPaid($conn, $issue_id)) {
        $message = "Fine paid successfully!";
        $message_type = "success";
    } else {
        $message = "Error processing payment. Please try again.";
        $message_type = "danger";
    }
}

// Update all fines first
updateAllFines($conn);

// Get user's current book issues
$current_issues_query = "SELECT bi.*, b.title, b.author, b.cover 
                        FROM book_issues bi 
                        JOIN books b ON bi.book_id = b.id 
                        WHERE bi.user_id = ? AND bi.status IN ('issued', 'overdue') 
                        ORDER BY bi.issue_date DESC";
$stmt = mysqli_prepare($conn, $current_issues_query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$current_issues = mysqli_stmt_get_result($stmt);

// Get user's book history (returned books)
$book_history_query = "SELECT bi.*, b.title, b.author, b.cover 
                      FROM book_issues bi 
                      JOIN books b ON bi.book_id = b.id 
                      WHERE bi.user_id = ? AND bi.status = 'returned' 
                      ORDER BY bi.return_date DESC 
                      LIMIT 20";
$stmt = mysqli_prepare($conn, $book_history_query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$book_history = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Books - OLMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../asset/style.css">
</head>
<body class="user-dashboard">
    <!-- Include user header -->
    <?php include("./user_header.php"); ?>
    
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <h2 class="mb-4">My Books</h2>
                
                <!-- Display message if any -->
                <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <!-- Current Issues Section -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h4>Currently Issued Books</h4>
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
                                                            Pay Fine
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
                            You don't have any books currently issued.
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Book History Section -->
                <div class="card">
                    <div class="card-header">
                        <h4>Book History</h4>
                    </div>
                    <div class="card-body">
                        <?php if (mysqli_num_rows($book_history) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Book</th>
                                        <th>Author</th>
                                        <th>Issue Date</th>
                                        <th>Return Date</th>
                                        <th>Fine Paid</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($history = mysqli_fetch_assoc($book_history)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($history['title']); ?></td>
                                        <td><?php echo htmlspecialchars($history['author']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($history['issue_date'])); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($history['return_date'])); ?></td>
                                        <td>
                                            <?php if ($history['fine_amount'] > 0): ?>
                                                <span class="badge bg-<?php echo $history['fine_paid'] ? 'success' : 'warning'; ?>">
                                                    ₹<?php echo number_format($history['fine_amount'], 2); ?>
                                                    <?php echo $history['fine_paid'] ? ' (Paid)' : ' (Unpaid)'; ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-success">No Fine</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <div class="alert alert-info">
                            No book history found.
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
