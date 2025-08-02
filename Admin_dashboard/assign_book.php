<?php
include("../database/config.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'];
    $bookId = $_POST['book_id'];

    // 1. Check if user already has 3 books
    $checkCount = mysqli_query($conn, "SELECT COUNT(*) AS total FROM borrowed_books WHERE user_id = $userId");
    $countResult = mysqli_fetch_assoc($checkCount);
    
    if ($countResult['total'] >= 3) {
        echo "User already has 3 books.";
        exit;
    }

    // 2. Prevent assigning the same book to the same user
    $checkSame = mysqli_query($conn, "SELECT * FROM borrowed_books WHERE user_id = $userId AND book_id = $bookId");
    if (mysqli_num_rows($checkSame) > 0) {
        echo "User already has this book.";
        exit;
    }

    // 3. Check if the book is available
    $bookCheck = mysqli_query($conn, "SELECT quantity FROM books WHERE id = $bookId");
    $book = mysqli_fetch_assoc($bookCheck);
    
    if (!$book || $book['quantity'] <= 0) {
        echo "Book not available.";
        exit;
    }

    // 4. Assign the book
    $assign = mysqli_query($conn, "INSERT INTO borrowed_books (user_id, book_id) VALUES ($userId, $bookId)");

    if ($assign) {
        // 5. Reduce book quantity
        mysqli_query($conn, "UPDATE books SET quantity = quantity - 1 WHERE id = $bookId");

        // 6. (Optional) Update user's borrowed count (if tracking)
        mysqli_query($conn, "UPDATE users SET books_borrowed = books_borrowed + 1 WHERE id = $userId");

        echo "Book assigned successfully.";
    } else {
        echo "Failed to assign book.";
    }
}
?>
