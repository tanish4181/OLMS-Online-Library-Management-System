<?php
// Start the session to check if admin is logged in
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../auth/adminLogin.php");
    exit();
}

// Include the database connection file
include("../database/config.php");

// Variables to store messages
$message = "";
$message_type = "";

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle editing a book
    if (isset($_POST['edit_book'])) {
        $book_id = $_POST['book_id'];
        $title = trim($_POST['title']);
        $author = trim($_POST['author']);
        $category = trim($_POST['category']);
        $quantity = (int)$_POST['quantity'];
        $description = trim($_POST['description']);

        // Handle file upload for cover image
        $cover = $_POST['current_cover']; // Keep current cover by default

        if (isset($_FILES['cover']) && $_FILES['cover']['error'] == 0) {
            $upload_dir = '../uploads/covers/';

            // Create directory if it doesn't exist
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $file_extension = strtolower(pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

            if (in_array($file_extension, $allowed_extensions)) {
                $new_filename = 'book_' . $book_id . '_' . time() . '.' . $file_extension;
                $target_path = $upload_dir . $new_filename;

                if (move_uploaded_file($_FILES['cover']['tmp_name'], $target_path)) {
                    // Delete old cover file if it exists and is not a placeholder
                    if ($cover && $cover != 'https://via.placeholder.com/190x260?text=Book+Cover' && file_exists($cover)) {
                        unlink($cover);
                    }
                    $cover = $target_path;
                } else {
                    $message = "Error uploading cover image.";
                    $message_type = "warning";
                }
            } else {
                $message = "Invalid file type. Please upload JPG, JPEG, PNG, or GIF files only.";
                $message_type = "warning";
            }
        }

        // If no cover is set, use placeholder
        if (empty($cover)) {
            $cover = 'https://via.placeholder.com/190x260?text=Book+Cover';
        }

        // Check if all required fields are filled
        if ($title && $author && $category && $quantity >= 0) {
            $update_query = "UPDATE books SET title = '$title', author = '$author', category = '$category', quantity = '$quantity', description = '$description', cover = '$cover' WHERE id = '$book_id'";

            if (mysqli_query($conn, $update_query)) {
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
    }
    // Handle book deletion
    elseif (isset($_POST['delete_book'])) {
        $book_id = $_POST['book_id'];

        // Check if book is currently issued
        $check_issued = "SELECT COUNT(*) as count FROM book_issues WHERE book_id = '$book_id' AND status IN ('issued', 'overdue')";
        $check_result = mysqli_query($conn, $check_issued);
        $check_data = mysqli_fetch_assoc($check_result);

        if ($check_data['count'] > 0) {
            $message = "Cannot delete book. It is currently issued to users.";
            $message_type = "danger";
        } else {
            // Get book details to delete cover file
            $book_query = "SELECT cover FROM books WHERE id = '$book_id'";
            $book_result = mysqli_query($conn, $book_query);
            $book_data = mysqli_fetch_assoc($book_result);

            $delete_query = "DELETE FROM books WHERE id = '$book_id'";
            if (mysqli_query($conn, $delete_query)) {
                // Delete cover file if it's not a placeholder
                if ($book_data['cover'] && $book_data['cover'] != 'https://via.placeholder.com/190x260?text=Book+Cover' && file_exists($book_data['cover'])) {
                    unlink($book_data['cover']);
                }
                $message = "Book deleted successfully!";
                $message_type = "success";
            } else {
                $message = "Error deleting book: " . mysqli_error($conn);
                $message_type = "danger";
            }
        }
    }
}

// Get filter parameters
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';
$availability_filter = isset($_GET['availability']) ? $_GET['availability'] : '';
$search_query = isset($_GET['search']) ? $_GET['search'] : '';

// Build the main query
$books_query = "SELECT * FROM books WHERE 1=1";

// Add filters
if (!empty($category_filter)) {
    $books_query .= " AND category = '" . mysqli_real_escape_string($conn, $category_filter) . "'";
}

if (!empty($availability_filter)) {
    if ($availability_filter == 'available') {
        $books_query .= " AND quantity > 0";
    } elseif ($availability_filter == 'out_of_stock') {
        $books_query .= " AND quantity = 0";
    }
}

if (!empty($search_query)) {
    $search_escaped = mysqli_real_escape_string($conn, $search_query);
    $books_query .= " AND (title LIKE '%$search_escaped%' OR author LIKE '%$search_escaped%' OR description LIKE '%$search_escaped%')";
}

$books_query .= " ORDER BY title";
$books_result = mysqli_query($conn, $books_query);

// Count how many books we have
$total_books = mysqli_num_rows($books_result);

// Get all categories for filter dropdown
$categories_query = "SELECT DISTINCT category FROM books ORDER BY category";
$categories_result = mysqli_query($conn, $categories_query);

// Get statistics
$stats_query = "SELECT 
                COUNT(*) as total_books,
                SUM(quantity) as total_copies,
                COUNT(CASE WHEN quantity > 0 THEN 1 END) as available_books,
                COUNT(CASE WHEN quantity = 0 THEN 1 END) as out_of_stock_books
                FROM books";
$stats_result = mysqli_query($conn, $stats_query);
$stats = mysqli_fetch_assoc($stats_result);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Books - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../asset/style.css">
</head>

<body class="admin-dashboard">
    <!-- Include admin navbar -->
    <?php include("navbar_admin.php"); ?>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <!-- Header Section -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Browse All Books</h2>
                    <div>
                        <a href="add_book.php" class="btn btn-success me-2">
                            <i class="fas fa-plus"></i> Add New Book
                        </a>
                    </div>
                </div>

                <!-- Display message if any -->
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                        <?php echo $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Books</h5>
                                <h3><?php echo $stats['total_books']; ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Copies</h5>
                                <h3><?php echo $stats['total_copies']; ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">Available Books</h5>
                                <h3><?php echo $stats['available_books']; ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body">
                                <h5 class="card-title">Out of Stock</h5>
                                <h3><?php echo $stats['out_of_stock_books']; ?></h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters and Search -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Search and Filter Books</h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="search" class="form-label">Search Books</label>
                                    <input type="text" class="form-control" id="search" name="search"
                                        placeholder="Search by title, author, or description..."
                                        value="<?php echo htmlspecialchars($search_query); ?>">
                                </div>
                                <div class="col-md-3">
                                    <label for="category" class="form-label">Category</label>
                                    <select class="form-select" id="category" name="category">
                                        <option value="">All Categories</option>
                                        <?php while ($cat = mysqli_fetch_assoc($categories_result)): ?>
                                            <option value="<?php echo htmlspecialchars($cat['category']); ?>"
                                                <?php echo $category_filter == $cat['category'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($cat['category']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="availability" class="form-label">Availability</label>
                                    <select class="form-select" id="availability" name="availability">
                                        <option value="">All Books</option>
                                        <option value="available" <?php echo $availability_filter == 'available' ? 'selected' : ''; ?>>Available</option>
                                        <option value="out_of_stock" <?php echo $availability_filter == 'out_of_stock' ? 'selected' : ''; ?>>Out of Stock</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search"></i> Search
                                        </button>
                                        <a href="?" class="btn btn-secondary">
                                            <i class="fas fa-times"></i> Clear
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Books Count -->
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    Showing <?php echo $total_books; ?> books
                    <?php if (!empty($search_query) || !empty($category_filter) || !empty($availability_filter)): ?>
                        (filtered results)
                    <?php endif; ?>
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
                                                <strong>Description:</strong> <?php echo htmlspecialchars(substr($book['description'], 0, 80)) . (strlen($book['description']) > 80 ? '...' : ''); ?>
                                            <?php endif; ?>
                                        </p>
                                        <div class="mt-auto">
                                            <div class="mb-2">
                                                <span class="badge bg-<?php echo $book['quantity'] > 0 ? 'success' : 'danger'; ?>">
                                                    <?php echo $book['quantity'] > 0 ? 'Available' : 'Out of Stock'; ?>
                                                </span>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <button class="btn btn-warning btn-sm"
                                                    onclick="editBook(<?php echo htmlspecialchars(json_encode($book)); ?>)">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm"
                                                    onclick="confirmDelete(<?php echo $book['id']; ?>, '<?php echo htmlspecialchars($book['title']); ?>')">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        No books found matching your criteria.
                        <?php if (!empty($search_query) || !empty($category_filter) || !empty($availability_filter)): ?>
                            Try adjusting your search filters.
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
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
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="book_id" id="editBookId">
                        <input type="hidden" name="current_cover" id="editCurrentCover">
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
                                    <input list="categoryOptions" class="form-control" id="editCategory" name="category" required placeholder="Select or type category">
                                    <datalist id="categoryOptions">
                                        <option value="Fiction">
                                        <option value="Non-Fiction">
                                        <option value="Romance">
                                        <option value="Fantasy">
                                        <option value="Mystery">
                                        <option value="Science Fiction">
                                        <option value="Biography">
                                        <option value="History">
                                        <option value="Self-Help">
                                        <option value="Academic">
                                    </datalist>

                                </div>
                                <div class="mb-3">
                                    <label for="editQuantity" class="form-label">Quantity *</label>
                                    <input type="number" class="form-control" id="editQuantity" name="quantity" min="0" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editCover" class="form-label">Cover Image</label>
                                    <input type="file" class="form-control" id="editCover" name="cover" accept="image/*">
                                    <small class="text-muted">Upload JPG, JPEG, PNG, or GIF files only. Leave empty to keep current cover.</small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Current Cover Preview</label>
                                    <div id="currentCoverPreview" class="border rounded p-2 text-center" style="height: 120px;">
                                        <img id="currentCoverImg" src="" alt="Current Cover" style="max-height: 100px; max-width: 100px; object-fit: cover;">
                                    </div>
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

    <!-- Hidden form for deletion -->
    <form id="deleteForm" method="POST" style="display: none;">
        <input type="hidden" name="book_id" id="deleteBookId">
        <input type="hidden" name="delete_book" value="1">
    </form>

    <?php include("footer.php"); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function editBook(book) {
            document.getElementById('editBookId').value = book.id;
            document.getElementById('editTitle').value = book.title;
            document.getElementById('editAuthor').value = book.author;
            document.getElementById('editCategory').value = book.category;
            document.getElementById('editQuantity').value = book.quantity;
            document.getElementById('editCurrentCover').value = book.cover;
            document.getElementById('editDescription').value = book.description;

            // Show current cover preview
            const currentCoverImg = document.getElementById('currentCoverImg');
            if (book.cover && book.cover !== 'https://via.placeholder.com/190x260?text=Book+Cover') {
                currentCoverImg.src = book.cover;
                currentCoverImg.style.display = 'block';
            } else {
                currentCoverImg.src = 'https://via.placeholder.com/100x100?text=No+Cover';
                currentCoverImg.style.display = 'block';
            }

            new bootstrap.Modal(document.getElementById('editBookModal')).show();
        }

        function confirmDelete(bookId, bookTitle) {
            if (confirm(`Are you sure you want to delete the book "${bookTitle}"?\n\nThis action cannot be undone.`)) {
                document.getElementById('deleteBookId').value = bookId;
                document.getElementById('deleteForm').submit();
            }
        }

        // Preview selected image in edit modal
        document.getElementById('editCover').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('currentCoverImg').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>

</html>