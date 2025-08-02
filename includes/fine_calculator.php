<?php
// Fine Calculator for OLMS
// This file calculates fines for overdue books
// Fine rate: ₹2 per day after 14 days

// Function to calculate fine amount for a book
// $due_date = when the book was supposed to be returned
// $current_date = today's date (optional, uses today if not provided)
function calculateFine($due_date, $current_date = null) {
    // If no current date provided, use today
    if ($current_date === null) {
        $current_date = date('Y-m-d H:i:s');
    }
    
    // Convert dates to timestamps so we can compare them
    $due_timestamp = strtotime($due_date);
    $current_timestamp = strtotime($current_date);
    
    // If book is not overdue yet (current date is before or equal to due date)
    if ($current_timestamp <= $due_timestamp) {
        return [
            'fine_amount' => 0.00,
            'days_overdue' => 0,
            'is_overdue' => false
        ];
    }
    
    // Calculate how many days the book is overdue
    $days_overdue = ceil(($current_timestamp - $due_timestamp) / (24 * 60 * 60));
    
    // Calculate fine (₹2 per day)
    $fine_amount = $days_overdue * 2.00;
    
    return [
        'fine_amount' => $fine_amount,
        'days_overdue' => $days_overdue,
        'is_overdue' => true
    ];
}

// Function to update fine amounts for all overdue books in the database
// $conn = database connection
function updateAllFines($conn) {
    $updated_count = 0;
    
    // Get all issued books that are overdue
    $query = "SELECT id, due_date FROM book_issues 
              WHERE status = 'issued' AND due_date < NOW()";
    $result = mysqli_query($conn, $query);
    
    // Go through each overdue book
    while ($row = mysqli_fetch_assoc($result)) {
        $fine_info = calculateFine($row['due_date']);
        
        if ($fine_info['is_overdue']) {
            // Update the fine amount in the database
            $update_query = "UPDATE book_issues 
                           SET fine_amount = '$fine_info[fine_amount]', status = 'overdue' 
                           WHERE id = '$row[id]'";
            
            if (mysqli_query($conn, $update_query)) {
                $updated_count++;
            }
        }
    }
    
    return $updated_count;
}

// Function to get fine information for a specific book issue
// $conn = database connection
// $issue_id = the ID of the book issue
function getFineInfo($conn, $issue_id) {
    $query = "SELECT due_date, fine_amount, status FROM book_issues WHERE id = '$issue_id'";
    $result = mysqli_query($conn, $query);
    
    if ($row = mysqli_fetch_assoc($result)) {
        $fine_info = calculateFine($row['due_date']);
        
        return [
            'current_fine' => $fine_info['fine_amount'],
            'stored_fine' => $row['fine_amount'],
            'days_overdue' => $fine_info['days_overdue'],
            'is_overdue' => $fine_info['is_overdue'],
            'status' => $row['status'],
            'due_date' => $row['due_date']
        ];
    }
    
    return null;
}

// Function to mark a fine as paid
// $conn = database connection
// $issue_id = the ID of the book issue
function markFineAsPaid($conn, $issue_id) {
    $update_query = "UPDATE book_issues SET fine_paid = 1 WHERE id = '$issue_id'";
    return mysqli_query($conn, $update_query);
}

// Function to return a book
// $conn = database connection
// $issue_id = the ID of the book issue
// $book_id = the ID of the book
function returnBook($conn, $issue_id, $book_id) {
    // Update the book issue status to returned
    $update_issue = "UPDATE book_issues SET status = 'returned' WHERE id = '$issue_id'";
    $result1 = mysqli_query($conn, $update_issue);
    
    // Increase the book quantity by 1
    $update_book = "UPDATE books SET quantity = quantity + 1 WHERE id = '$book_id'";
    $result2 = mysqli_query($conn, $update_book);
    
    // Return true if both updates worked
    return $result1 && $result2;
}

// Function to get all overdue books
// $conn = database connection
function getOverdueBooks($conn) {
    $query = "SELECT bi.*, u.fullname, u.username, b.title, b.author 
              FROM book_issues bi 
              JOIN users u ON bi.user_id = u.id 
              JOIN books b ON bi.book_id = b.id 
              WHERE bi.status = 'overdue' 
              ORDER BY bi.due_date ASC";
    
    $result = mysqli_query($conn, $query);
    $overdue_books = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $overdue_books[] = $row;
    }
    
    return $overdue_books;
}

// Function to get total fines owed by a user
// $conn = database connection
// $user_id = the ID of the user
function getUserTotalFines($conn, $user_id) {
    $query = "SELECT SUM(fine_amount) as total_fines FROM book_issues 
              WHERE user_id = '$user_id' AND fine_paid = 0 AND fine_amount > 0";
    
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    
    return $row['total_fines'] ? $row['total_fines'] : 0;
}
?> 