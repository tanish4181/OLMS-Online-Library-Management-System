<?php
include("config.php");

if (isset($_POST['return_book'])) {
    $user_id = $_POST['user_id'];
    $book_id = $_POST['book_id'];

    // Delete from borrowed_books
    mysqli_query($conn, "DELETE FROM borrowed_books WHERE user_id = $user_id AND book_id = $book_id");

    // Increase quantity in books table
    mysqli_query($conn, "UPDATE books SET quantity = quantity + 1 WHERE id = $book_id");

    // Decrease books_borrowed in users table
    mysqli_query($conn, "UPDATE users SET books_borrowed = books_borrowed - 1 WHERE id = $user_id");

    // Redirect back
    header("Location: Manage_user.php?success=1");
    exit();
} else {
    echo "Invalid access.";
}
?>
