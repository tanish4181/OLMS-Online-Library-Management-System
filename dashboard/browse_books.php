<?php
// Start the session to check if user is logged in
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../auth/UserLogin.php");
    exit();
}

// Include the database connection file
include("../database/config.php");

// Get all books from the database
$books_query = "SELECT * FROM books ORDER BY title";
$books_result = mysqli_query($conn, $books_query);

// Count how many books we have
$total_books = mysqli_num_rows($books_result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Books - OLMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../asset/style.css">
</head>
<body class="user-dashboard">
    <!-- Include user header -->
    <?php include("./user_header.php"); ?>
    
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Browse All Books</h2>
                    <a href="search_books.php" class="btn btn-primary">
                        <i class="bi bi-search"></i> Search Books
                    </a>
                </div>
                
                <!-- Books Count -->
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i>
                    Showing <?php echo $total_books; ?> books in our library
                </div>
                
                <!-- Books Grid -->
                <?php if ($total_books > 0): ?>
                <div class="row">
                    <?php while ($book = mysqli_fetch_assoc($books_result)): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <img src="<?php echo !empty($book['cover']) ? htmlspecialchars($book['cover']) : 'https://via.placeholder.com/190x260?text=No+Cover'; ?>" 
                                 class="card-img-top" 
                                 alt="<?php echo htmlspecialchars($book['title']); ?>"
                                 style="height: 200px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($book['title']); ?></h5>
                                <p class="card-text">
                                    <strong>Author:</strong> <?php echo htmlspecialchars($book['author']); ?><br>
                                    <strong>Category:</strong> <?php echo htmlspecialchars($book['category']); ?><br>
                                    <strong>Available:</strong> <?php echo $book['quantity']; ?> copies<br>
                                    <?php if (!empty($book['description'])): ?>
                                        <strong>Description:</strong> <?php echo htmlspecialchars(substr($book['description'], 0, 100)) . (strlen($book['description']) > 100 ? '...' : ''); ?>
                                    <?php endif; ?>
                                </p>
                                <div class="mt-auto">
                                    <span class="badge bg-<?php echo $book['quantity'] > 0 ? 'success' : 'danger'; ?>">
                                        <?php echo $book['quantity'] > 0 ? 'Available' : 'Out of Stock'; ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
                <?php else: ?>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i>
                    No books found in the library. Please check back later.
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 