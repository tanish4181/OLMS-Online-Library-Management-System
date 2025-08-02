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

// Set default values for our variables
$search_results = [];
$search_performed = false;
$search_term = "";

// Check if the search form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search'])) {
    // Get the search term from the form
    $search_term = trim($_POST['search_term']);
    $search_performed = true;
    
    // Only search if the search term is not empty
    if (!empty($search_term)) {
        // Search by title or author using LIKE for partial matches
        $search_query = "SELECT * FROM books WHERE 
                        title LIKE '%$search_term%' OR 
                        author LIKE '%$search_term%' 
                        ORDER BY title";
        
        $search_results = mysqli_query($conn, $search_query);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Books - OLMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../asset/style.css">
</head>
<body class="user-dashboard">
    <!-- Include user header -->
    <?php include("./user_header.php"); ?>
    
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <h2 class="mb-4">Search Books</h2>
                
                <!-- Search Form -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="POST" class="row g-3">
                            <div class="col-md-8">
                                <label for="search_term" class="form-label">Search by Book Title or Author</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="search_term" 
                                       name="search_term" 
                                       value="<?php echo htmlspecialchars($search_term); ?>"
                                       placeholder="Enter book title or author name..."
                                       required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" name="search" class="btn btn-primary w-100">
                                    <i class="bi bi-search"></i> Search Books
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Search Results -->
                <?php if ($search_performed): ?>
                    <div class="card">
                        <div class="card-header">
                            <h4>Search Results</h4>
                            <?php if (!empty($search_term)): ?>
                                <small class="text-muted">Searching for: "<?php echo htmlspecialchars($search_term); ?>"</small>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <?php if (mysqli_num_rows($search_results) > 0): ?>
                                <div class="row">
                                    <?php while ($book = mysqli_fetch_assoc($search_results)): ?>
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
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle"></i>
                                    No books found matching your search criteria. Try different keywords.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Browse All Books Link -->
                <div class="text-center mt-4">
                    <a href="browse_books.php" class="btn btn-outline-primary">
                        <i class="bi bi-grid"></i> Browse All Books
                    </a>
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