<?php
/**
 * Fine Calculator for OLMS
 * Calculates fines based on due date and current date
 * Fine rate: ₹2 per day after 14 days
 */

/**
 * Calculate fine amount for a book issue
 * @param string $due_date - The due date of the book
 * @param string $current_date - Current date (optional, defaults to now)
 * @return array - Array containing fine amount and days overdue
 */
function calculateFine($due_date, $current_date = null) {
    if ($current_date === null) {
        $current_date = date('Y-m-d H:i:s');
    }
    
    $due_timestamp = strtotime($due_date);
    $current_timestamp = strtotime($current_date);
    
    // If book is not overdue yet
    if ($current_timestamp <= $due_timestamp) {
        return [
            'fine_amount' => 0.00,
            'days_overdue' => 0,
            'is_overdue' => false
        ];
    }
    
    // Calculate days overdue
    $days_overdue = ceil(($current_timestamp - $due_timestamp) / (24 * 60 * 60));
    
    // Calculate fine (₹2 per day)
    $fine_amount = $days_overdue * 2.00;
    
    return [
        'fine_amount' => $fine_amount,
        'days_overdue' => $days_overdue,
        'is_overdue' => true
    ];
}

/**
 * Update fine amounts for all overdue books
 * @param mysqli $conn - Database connection
 * @return int - Number of books updated
 */
function updateAllFines($conn) {
    $updated_count = 0;
    
    // Get all issued books that are overdue
    $query = "SELECT id, due_date FROM book_issues 
              WHERE status = 'issued' AND due_date < NOW()";
    $result = mysqli_query($conn, $query);
    
    while ($row = mysqli_fetch_assoc($result)) {
        $fine_info = calculateFine($row['due_date']);
        
        if ($fine_info['is_overdue']) {
            $update_query = "UPDATE book_issues 
                           SET fine_amount = ?, status = 'overdue' 
                           WHERE id = ?";
            $stmt = mysqli_prepare($conn, $update_query);
            mysqli_stmt_bind_param($stmt, "di", $fine_info['fine_amount'], $row['id']);
            
            if (mysqli_stmt_execute($stmt)) {
                $updated_count++;
            }
        }
    }
    
    return $updated_count;
}

/**
 * Get fine information for a specific book issue
 * @param mysqli $conn - Database connection
 * @param int $issue_id - Book issue ID
 * @return array - Fine information
 */
function getFineInfo($conn, $issue_id) {
    $query = "SELECT due_date, fine_amount, status FROM book_issues WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $issue_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
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

/**
 * Mark fine as paid
 * @param mysqli $conn - Database connection
 * @param int $issue_id - Book issue ID
 * @return bool - Success status
 */
function markFineAsPaid($conn, $issue_id) {
    $query = "UPDATE book_issues SET fine_paid = 1 WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $issue_id);
    
    return mysqli_stmt_execute($stmt);
}

/**
 * Return a book and calculate final fine
 * @param mysqli $conn - Database connection
 * @param int $issue_id - Book issue ID
 * @param int $book_id - Book ID
 * @return bool - Success status
 */
function returnBook($conn, $issue_id, $book_id) {
    // Start transaction
    mysqli_begin_transaction($conn);
    
    try {
        // Calculate final fine
        $fine_info = getFineInfo($conn, $issue_id);
        $final_fine = $fine_info ? $fine_info['current_fine'] : 0.00;
        
        // Update book issue status
        $update_issue = "UPDATE book_issues 
                        SET status = 'returned', 
                            return_date = NOW(), 
                            fine_amount = ? 
                        WHERE id = ?";
        $stmt = mysqli_prepare($conn, $update_issue);
        mysqli_stmt_bind_param($stmt, "di", $final_fine, $issue_id);
        mysqli_stmt_execute($stmt);
        
        // Increase book quantity by 1
        $update_book = "UPDATE books SET quantity = quantity + 1 WHERE id = ?";
        $stmt = mysqli_prepare($conn, $update_book);
        mysqli_stmt_bind_param($stmt, "i", $book_id);
        mysqli_stmt_execute($stmt);
        
        mysqli_commit($conn);
        return true;
    } catch (Exception $e) {
        mysqli_rollback($conn);
        return false;
    }
}
?> 