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

// Handle book operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_book'])) {
        $title = trim($_POST['title']);
        $author = trim($_POST['author']);
        $category = trim($_POST['category']);
        $quantity = (int)$_POST['quantity'];
        $description = trim($_POST['description']);
        $cover = $_POST['cover'] ?: 'https://via.placeholder.com/190x260?text=Book+Cover';
        
        if ($title && $author && $category && $quantity > 0) {
            $insert_query = "INSERT INTO books (title, author, category, quantity, description, cover) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $insert_query);
            mysqli_stmt_bind_param($stmt, "sssis", $title, $author, $category, $quantity, $description, $cover);
            
            if (mysqli_stmt_execute($stmt)) {
                $message = "Book added successfully!";
                $message_type = "success";
            } else {
                $message = "Error adding book. Please try again.";
                $message_type = "danger";
            }
        } else {
            $message = "Please fill all required fields.";
            $message_type = "warning";
        }
    } elseif (isset($_POST['edit_book'])) {
        $book_id = $_POST['book_id'];
        $title = trim($_POST['title']);
        $author = trim($_POST['author']);
        $category = trim($_POST['category']);
        $quantity = (int)$_POST['quantity'];
        $description = trim($_POST['description']);
        $cover = $_POST['cover'] ?: 'https://via.placeholder.com/190x260?text=Book+Cover';
        
        if ($title && $author && $category && $quantity >= 0) {
            $update_query = "UPDATE books SET title = ?, author = ?, category = ?, quantity = ?, description = ?, cover = ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $update_query);
            mysqli_stmt_bind_param($stmt, "sssis", $title, $author, $category, $quantity, $description, $cover, $book_id);
            
            if (mysqli_stmt_execute($stmt)) {
                $message = "Book updated successfully!";
                $message_type = "success";
            } else {
                $message = "Error updating book. Please try again.";
                $message_type = "danger";
            }
        } else {
            $message = "Please fill all required fields.";
            $message_type = "warning";
        }
    } elseif (isset($_POST['delete_book'])) {
        $book_id = $_POST['book_id'];
        
        // Check if book is currently issued
        $check_issued = "SELECT COUNT(*) as issued_count FROM book_issues WHERE book_id = ? AND status IN ('issued', 'overdue')";
        $stmt = mysqli_prepare($conn, $check_issued);
        mysqli_stmt_bind_param($stmt, "i", $book_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $issued_count = mysqli_fetch_assoc($result)['issued_count'];
        
        if ($issued_count > 0) {
            $message = "Cannot delete book. It has " . $issued_count . " active issue(s).";
            $message_type = "warning";
        } else {
            $delete_query = "DELETE FROM books WHERE id = ?";
            $stmt = mysqli_prepare($conn, $delete_query);
            mysqli_stmt_bind_param($stmt, "i", $book_id);
            
            if (mysqli_stmt_execute($stmt)) {
                $message = "Book deleted successfully!";
                $message_type = "success";
            } else {
                $message = "Error deleting book. Please try again.";
                $message_type = "danger";
            }
        }
    }
}

// Get all books
$books_query = "SELECT * FROM books ORDER BY title";
$books_result = mysqli_query($conn, $books_query);

// Get unique categories for dropdown
$categories_query = "SELECT DISTINCT category FROM books ORDER BY category";
$categories_result = mysqli_query($conn, $categories_query);
$categories = [];
while ($row = mysqli_fetch_assoc($categories_result)) {
    $categories[] = $row['category'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Books - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../asset/style.css">
</head>
<body class="admin-dashboard">
    <!-- Include admin navbar -->
    <?php include("navbar_admin.php"); ?>
    
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Manage Books</h2>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBookModal">
                        Add New Book
                    </button>
                </div>
                
                <!-- Display message if any -->
                <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <!-- Books Table -->
                <div class="card">
                    <div class="card-header">
                        <h4>All Books</h4>
                    </div>
                    <div class="card-body">
                        <?php if (mysqli_num_rows($books_result) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Cover</th>
                                        <th>Title</th>
                                        <th>Author</th>
                                        <th>Category</th>
                                        <th>Available</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($book = mysqli_fetch_assoc($books_result)): ?>
                                    <tr>
                                        <td>
                                            <img src="<?php echo htmlspecialchars($book['cover']); ?>" 
                                                 alt="<?php echo htmlspecialchars($book['title']); ?>"
                                                 style="width: 50px; height: 70px; object-fit: cover;">
                                        </td>
                                        <td><?php echo htmlspecialchars($book['title']); ?></td>
                                        <td><?php echo htmlspecialchars($book['author']); ?></td>
                                        <td><?php echo htmlspecialchars($book['category']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $book['quantity'] > 0 ? 'success' : 'danger'; ?>">
                                                <?php echo $book['quantity']; ?> available
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" 
                                                    onclick="editBook(<?php echo htmlspecialchars(json_encode($book)); ?>)">
                                                Edit
                                            </button>
                                            <button class="btn btn-sm btn-danger" 
                                                    onclick="deleteBook(<?php echo $book['id']; ?>, '<?php echo htmlspecialchars($book['title']); ?>')">
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <div class="alert alert-info">
                            No books found. Add your first book!
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Book Modal -->
    <div class="modal fade" id="addBookModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Book</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Book Title *</label>
                                    <input type="text" class="form-control" id="title" name="title" required>
                                </div>
                                <div class="mb-3">
                                    <label for="author" class="form-label">Author *</label>
                                    <input type="text" class="form-control" id="author" name="author" required>
                                </div>
                                <div class="mb-3">
                                    <label for="category" class="form-label">Category *</label>
                                    <select class="form-control" id="category" name="category" required>
                                        <option value="">Select Category</option>
                                        <option value="Fiction">Fiction</option>
                                        <option value="Non-Fiction">Non-Fiction</option>
                                        <option value="Romance">Romance</option>
                                        <option value="Fantasy">Fantasy</option>
                                        <option value="Mystery">Mystery</option>
                                        <option value="Science Fiction">Science Fiction</option>
                                        <option value="Biography">Biography</option>
                                        <option value="History">History</option>
                                        <option value="Self-Help">Self-Help</option>
                                        <option value="Academic">Academic</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="quantity" class="form-label">Quantity *</label>
                                    <input type="number" class="form-control" id="quantity" name="quantity" min="1" required>
                                </div>
                                <div class="mb-3">
                                    <label for="cover" class="form-label">Cover Image URL</label>
                                    <input type="url" class="form-control" id="cover" name="cover" 
                                           placeholder="https://example.com/image.jpg">
                                    <small class="text-muted">Leave empty for default placeholder</small>
                                </div>
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="4"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="add_book" class="btn btn-primary">Add Book</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Book Modal -->
    <div class="modal fade" id="editBookModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Book</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="book_id" id="editBookId">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editTitle" class="form-label">Book Title *</label>
                                    <input type="text" class="form-control" id="editTitle" name="title" required>
                                </div>
                                <div class="mb-3">
                                    <label for="editAuthor" class="form-label">Author *</label>
                                    <input type="text" class="form-control" id="editAuthor" name="author" required>
                                </div>
                                <div class="mb-3">
                                    <label for="editCategory" class="form-label">Category *</label>
                                    <select class="form-control" id="editCategory" name="category" required>
                                        <option value="">Select Category</option>
                                        <option value="Fiction">Fiction</option>
                                        <option value="Non-Fiction">Non-Fiction</option>
                                        <option value="Romance">Romance</option>
                                        <option value="Fantasy">Fantasy</option>
                                        <option value="Mystery">Mystery</option>
                                        <option value="Science Fiction">Science Fiction</option>
                                        <option value="Biography">Biography</option>
                                        <option value="History">History</option>
                                        <option value="Self-Help">Self-Help</option>
                                        <option value="Academic">Academic</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editQuantity" class="form-label">Quantity *</label>
                                    <input type="number" class="form-control" id="editQuantity" name="quantity" min="0" required>
                                </div>
                                <div class="mb-3">
                                    <label for="editCover" class="form-label">Cover Image URL</label>
                                    <input type="url" class="form-control" id="editCover" name="cover">
                                    <small class="text-muted">Leave empty for default placeholder</small>
                                </div>
                                <div class="mb-3">
                                    <label for="editDescription" class="form-label">Description</label>
                                    <textarea class="form-control" id="editDescription" name="description" rows="4"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="edit_book" class="btn btn-primary">Update Book</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Book Modal -->
    <div class="modal fade" id="deleteBookModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Book</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <p>Are you sure you want to delete <strong id="deleteBookTitle"></strong>?</p>
                        <p class="text-danger">This action cannot be undone.</p>
                        <input type="hidden" name="book_id" id="deleteBookId">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="delete_book" class="btn btn-danger">Delete Book</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editBook(book) {
            document.getElementById('editBookId').value = book.id;
            document.getElementById('editTitle').value = book.title;
            document.getElementById('editAuthor').value = book.author;
            document.getElementById('editCategory').value = book.category;
            document.getElementById('editQuantity').value = book.quantity;
            document.getElementById('editCover').value = book.cover;
            document.getElementById('editDescription').value = book.description;
            new bootstrap.Modal(document.getElementById('editBookModal')).show();
        }
        
        function deleteBook(bookId, bookTitle) {
            document.getElementById('deleteBookId').value = bookId;
            document.getElementById('deleteBookTitle').textContent = bookTitle;
            new bootstrap.Modal(document.getElementById('deleteBookModal')).show();
        }
    </script>
</body>
</html>