<?php
// user books
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../auth/UserLogin.php");
    exit();
}
include __DIR__ . '/../database/config.php';
include __DIR__ . '/../includes/fine_calculator.php';
$user_id = $_SESSION['user_id'];
updateAllFines($conn);
$current_issues_query = "SELECT bi.*, b.title, b.author, b.cover 
                        FROM book_issues bi 
                        JOIN books b ON bi.book_id = b.id 
                        WHERE bi.user_id = '$user_id' AND bi.status IN ('issued', 'overdue') 
                        ORDER BY bi.issue_date DESC";
$current_issues = mysqli_query($conn, $current_issues_query);
$book_history_query = "SELECT bi.*, b.title, b.author, b.cover 
                      FROM book_issues bi 
                      JOIN books b ON bi.book_id = b.id 
                      WHERE bi.user_id = '$user_id' AND bi.status = 'returned' 
                      ORDER BY bi.return_date DESC 
                      LIMIT 20";
$book_history = mysqli_query($conn, $book_history_query);
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
    <?php include __DIR__ . '/user_header.php'; ?>
    
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <h2 class="mb-4">My Books</h2>
                
                <!-- Current Issues Section -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h4>Currently Issued Books</h4>
                        <small class="text-muted">These are the books you currently have borrowed</small>
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
                                                
                                                <div class="alert alert-info py-2">
                                                    <small><i class="bi bi-info-circle"></i> Contact the library staff to return this book or pay any fines.</small>
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
                            You don't have any books currently issued.
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Book History Section -->
                <div class="card">
                    <div class="card-header">
                        <h4>Book History</h4>
                        <small class="text-muted">Books you have borrowed in the past</small>
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
                                        <th>Fine Status</th>
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
                            <i class="bi bi-info-circle"></i>
                            No book history found.
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
   <?php
    include __DIR__ . '/footer.php';
    ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
</body>
</html>
