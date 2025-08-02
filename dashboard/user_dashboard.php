<?php
// Initialize variables with default values
$totalBooks = 0;
$activeBorrow = 0;
$categories = 0;
$db_connected = false;

// Try to connect to database
try {
    include("../database/config.php");
    $db_connected = true;
    
    // Total number of books
    $totalBooksQuery = "SELECT COUNT(*) as total FROM books";
    $totalBooksResult = mysqli_query($conn, $totalBooksQuery);
    if ($totalBooksResult) {
        $totalBooks = mysqli_fetch_assoc($totalBooksResult)['total'];
    }

    // Count categories
    $categoriesQuery = "SELECT COUNT(DISTINCT category) as total_categories FROM books";
    $categoriesResult = mysqli_query($conn, $categoriesQuery);
    if ($categoriesResult) {
        $categories = mysqli_fetch_assoc($categoriesResult)['total_categories'];
    }

    // Check if user is logged in
    session_start();
    if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'user') {
        $user_id = $_SESSION['user_id'];
        
        // Check if borrowings table exists
        $check_borrowing_table = "SHOW TABLES LIKE 'borrowings'";
        $borrowing_table_exists = mysqli_query($conn, $check_borrowing_table);
        
        if ($borrowing_table_exists && mysqli_num_rows($borrowing_table_exists) > 0) {
            // Number of books borrowed by this user (active borrowings)
            $activeBorrowQuery = "SELECT COUNT(*) as active FROM borrowings WHERE user_id = ? AND status = 'borrowed'";
            $stmt = mysqli_prepare($conn, $activeBorrowQuery);
            mysqli_stmt_bind_param($stmt, "i", $user_id);
            mysqli_stmt_execute($stmt);
            $activeBorrowResult = mysqli_stmt_get_result($stmt);
            if ($activeBorrowResult) {
                $activeBorrow = mysqli_fetch_assoc($activeBorrowResult)['active'];
            }
        }
    }
} catch (Exception $e) {
    $db_connected = false;
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>User Dashboard - OLMS</title>
    <link rel="stylesheet" href="../asset/style.css" />
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
  </head>
  <body class="user-dashboard">
    <!-- Navbar -->
<?php include("./user_header.php"); ?>
    <!-- welcome heading -->
    <div class="user-dheading container">
      <h1 class="user-dtext">Welcome to Your Dashboard!</h1>
      <small class="user-hsmall-text"
        >Discover thousands of books and resources at your fingertips</small
      >
    </div>

    <!-- Database Connection Status -->
    <?php if (!$db_connected): ?>
    <div class="container" style="margin-bottom: 20px;">
      <div class="row">
        <div class="col-md-12">
          <div class="alert alert-warning" style="text-align: center;">
            <strong>⚠️ Database Connection Issue:</strong><br>
            <small>Some statistics may not be available. Please ensure the database is connected.</small>
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <!-- user details boxes -->
    <div class="user-details-box">
      <div class="user-dMainBox">
        <div class="user-total-books">
          <div class="user-numbers">
            <h1><?php echo $db_connected ? $totalBooks : 'N/A'; ?></h1>
          </div>
          <small class="user-small-txt">Total Books</small>
        </div>
        
        <div class="user-borrowig">
          <div class="user-numbers">
            <h1><?php echo $db_connected ? $activeBorrow : 'N/A'; ?></h1>
          </div>
          <small class="user-small-txt">Active Borrowing</small>
        </div>
        <div class="user-books-categories">
          <div class="user-numbers">
            <h1><?php echo $db_connected ? $categories : 'N/A'; ?></h1>
          </div>
          <small class="user-small-txt">Categories</small>
        </div>
      </div>
    </div>

    <!-- search book box -->

    <div class="user-search-box">
      <div class="user-internal-box">
        <div class="search box">
          <h4>search book</h4>
          <form action="">
            <label class="" for=""
              ><strong> <small>Search books</small> </strong></label
            >
            <br />
            <input
              class="user-form"
              placeholder="Enter book title"
              type="text"
            />
          </form>
        </div>
        <div class="category">
          <form action="">
            <label class="user-cat-lab" for=""
              ><strong> <small>Category</small> </strong></label
            >
            <br />
            <select class="user-form">
              <option value="all-categories">All categories</option>
              <option value="fiction">Fiction</option>
              <option value="non-fiction">Non-fiction</option>
              <option value="science">Science</option>
            </select>
          </form>
        </div>
        <div class="user-srcbx-btn">
          <button>Search</button>
          <button>Browse all</button>
        </div>
      </div>
    </div>

    <!-- Recently added book -->
    <div class="container-fluid col-11 ps-5 ms-5 mt-5">
      <h1>Latest Books</h1>
    </div>

    <div class="user-latest-book pt-2">
      <?php if ($db_connected): ?>
        <?php
        // Get recent books from database
        $recent_books_query = "SELECT * FROM books ORDER BY id DESC LIMIT 4";
        $recent_books_result = mysqli_query($conn, $recent_books_query);
        
        if ($recent_books_result && mysqli_num_rows($recent_books_result) > 0):
          while ($book = mysqli_fetch_assoc($recent_books_result)):
        ?>
        <div class="user-book-box">
          <img
            src="<?php echo !empty($book['cover']) ? $book['cover'] : 'https://via.placeholder.com/190x260?text=No+Cover'; ?>"
            height="260px"
            width="190px"
            alt="<?php echo htmlspecialchars($book['title']); ?>"
          />
          <h2><?php echo htmlspecialchars($book['title']); ?></h2>
          <p><?php echo htmlspecialchars($book['author'] . ' / ' . $book['category']); ?></p>
          <div class="user-avail">
            <h6><?php echo $book['quantity'] > 0 ? 'Available' : 'Out of Stock'; ?></h6>
          </div>
        </div>
        <?php 
          endwhile;
        else:
        ?>
        <!-- Fallback books when no books in database -->
        <div class="user-book-box">
          <img
            src="https://cdn.prod.website-files.com/5dc42e9651595b6016ab6149/5eddb131992873516c7ea7c4_48-laws-of-power-robert-greene-book-cover.png"
            height="260px"
            width="190px"
            alt=""
          />
          <h2>The 48 Laws of Power</h2>
          <p>Strategy / Politics / Psychology</p>
          <div class="user-avail">
            <h6>available</h6>
          </div>
        </div>
        <div class="user-book-box">
          <img
            src="https://5.imimg.com/data5/FL/IL/MY-49937457/selection_198.png"
            height="260px"
            width="190px"
            alt=""
          />
          <h2>Let Us C</h2>
          <p>Programming / Computer Science</p>
          <div class="user-avail">
            <h6>available</h6>
          </div>
        </div>
        <div class="user-book-box">
          <img
            src="https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1517755071i/3282557.jpg"
            height="260px"
            width="190px"
            alt=""
          />
          <h2>Gunaho ka devta</h2>
          <p>exciting novel</p>
          <div class="user-avail">
            <h6>available</h6>
          </div>
        </div>
        <div class="user-book-box">
          <img
            src="https://upload.wikimedia.org/wikipedia/commons/0/06/Atomic_habits.jpg"
            height="260px"
            width="190px"
            alt=""
          />
          <h2>Atomic Habits</h2>
          <p>Self-help / Personal Development</p>
          <div class="user-avail">
            <h6>available</h6>
          </div>
        </div>
        <?php endif; ?>
      <?php else: ?>
        <!-- Fallback when database not connected -->
        <div class="user-book-box">
          <img
            src="https://cdn.prod.website-files.com/5dc42e9651595b6016ab6149/5eddb131992873516c7ea7c4_48-laws-of-power-robert-greene-book-cover.png"
            height="260px"
            width="190px"
            alt=""
          />
          <h2>The 48 Laws of Power</h2>
          <p>Strategy / Politics / Psychology</p>
          <div class="user-avail">
            <h6>available</h6>
          </div>
        </div>
        <div class="user-book-box">
          <img
            src="https://5.imimg.com/data5/FL/IL/MY-49937457/selection_198.png"
            height="260px"
            width="190px"
            alt=""
          />
          <h2>Let Us C</h2>
          <p>Programming / Computer Science</p>
          <div class="user-avail">
            <h6>available</h6>
          </div>
        </div>
        <div class="user-book-box">
          <img
            src="https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1517755071i/3282557.jpg"
            height="260px"
            width="190px"
            alt=""
          />
          <h2>Gunaho ka devta</h2>
          <p>exciting novel</p>
          <div class="user-avail">
            <h6>available</h6>
          </div>
        </div>
        <div class="user-book-box">
          <img
            src="https://upload.wikimedia.org/wikipedia/commons/0/06/Atomic_habits.jpg"
            height="260px"
            width="190px"
            alt=""
          />
          <h2>Atomic Habits</h2>
          <p>Self-help / Personal Development</p>
          <div class="user-avail">
            <h6>available</h6>
          </div>
        </div>
      <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
