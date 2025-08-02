<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../auth/UserLogin.php");
    exit();
}

include("../database/config.php");

$user_id = $_SESSION['user_id'];
$message = "";
$message_type = "";

// Handle book request submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['request_book'])) {
    $book_id = $_POST['book_id'];
    
    // Check if user already has a pending request for this book
    $check_existing = "SELECT id FROM book_requests WHERE user_id = ? AND book_id = ? AND status = 'pending'";
    $stmt = mysqli_prepare($conn, $check_existing);
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $book_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        $message = "You already have a pending request for this book.";
        $message_type = "warning";
    } else {
        // Check if book is available
        $check_availability = "SELECT quantity FROM books WHERE id = ?";
        $stmt = mysqli_prepare($conn, $check_availability);
        mysqli_stmt_bind_param($stmt, "i", $book_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $book = mysqli_fetch_assoc($result);
        
        if ($book['quantity'] > 0) {
            // Insert book request
            $insert_request = "INSERT INTO book_requests (user_id, book_id) VALUES (?, ?)";
            $stmt = mysqli_prepare($conn, $insert_request);
            mysqli_stmt_bind_param($stmt, "ii", $user_id, $book_id);
            
            if (mysqli_stmt_execute($stmt)) {
                $message = "Book request submitted successfully! Admin will review your request.";
                $message_type = "success";
            } else {
                $message = "Error submitting request. Please try again.";
                $message_type = "danger";
            }
        } else {
            $message = "This book is currently out of stock.";
            $message_type = "warning";
        }
    }
}

// Get all available books
$books_query = "SELECT * FROM books WHERE quantity > 0 ORDER BY title";
$books_result = mysqli_query($conn, $books_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Book - OLMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../asset/style.css">
</head>
<body class="user-dashboard">
    <!-- Include user header -->
    <?php include("./user_header.php"); ?>
    
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <h2 class="mb-4">Request a Book</h2>
                
                <!-- Display message if any -->
                <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <!-- Available Books -->
                <div class="row">
                    <?php if (mysqli_num_rows($books_result) > 0): ?>
                        <?php while ($book = mysqli_fetch_assoc($books_result)): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <img src="<?php echo htmlspecialchars($book['cover']); ?>" 
                                     class="card-img-top" alt="<?php echo htmlspecialchars($book['title']); ?>"
                                     style="height: 200px; object-fit: cover;">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($book['title']); ?></h5>
                                    <p class="card-text">
                                        <strong>Author:</strong> <?php echo htmlspecialchars($book['author']); ?><br>
                                        <strong>Category:</strong> <?php echo htmlspecialchars($book['category']); ?><br>
                                        <strong>Available:</strong> <?php echo $book['quantity']; ?> copies<br>
                                        <strong>Description:</strong> <?php echo htmlspecialchars($book['description']); ?>
                                    </p>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                                        <button type="submit" name="request_book" class="btn btn-primary">
                                            Request Book
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="alert alert-info">
                                No books are currently available for request.
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- My Requests Section -->
                <div class="mt-5">
                    <h3>My Book Requests</h3>
                    <?php
                    $my_requests_query = "SELECT br.*, b.title, b.author 
                                        FROM book_requests br 
                                        JOIN books b ON br.book_id = b.id 
                                        WHERE br.user_id = ? 
                                        ORDER BY br.request_date DESC";
                    $stmt = mysqli_prepare($conn, $my_requests_query);
                    mysqli_stmt_bind_param($stmt, "i", $user_id);
                    mysqli_stmt_execute($stmt);
                    $my_requests = mysqli_stmt_get_result($stmt);
                    ?>
                    
                    <?php if (mysqli_num_rows($my_requests) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Book Title</th>
                                    <th>Author</th>
                                    <th>Request Date</th>
                                    <th>Status</th>
                                    <th>Admin Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($request = mysqli_fetch_assoc($my_requests)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($request['title']); ?></td>
                                    <td><?php echo htmlspecialchars($request['author']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($request['request_date'])); ?></td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            echo $request['status'] == 'pending' ? 'warning' : 
                                                ($request['status'] == 'approved' ? 'success' : 'danger'); 
                                        ?>">
                                            <?php echo ucfirst($request['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($request['admin_notes'] ?? 'No notes'); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-info">
                        You haven't made any book requests yet.
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 