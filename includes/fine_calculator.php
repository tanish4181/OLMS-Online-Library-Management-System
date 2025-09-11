<?php

function calculateFine($due_date, $current_date = null) {
    if ($current_date === null) {
        $current_date = date('Y-m-d H:i:s');
    }
    $due_timestamp = strtotime($due_date);
    $current_timestamp = strtotime($current_date);
    if ($current_timestamp <= $due_timestamp) {
        return [
            'fine_amount' => 0.00,
            'days_overdue' => 0,
            'is_overdue' => false
        ];
    }
    $days_overdue = ceil(($current_timestamp - $due_timestamp) / (24 * 60 * 60));
    $fine_amount = $days_overdue * 2.00;
    return [
        'fine_amount' => $fine_amount,
        'days_overdue' => $days_overdue,
        'is_overdue' => true
    ];
}

function updateAllFines($conn) {
    $updated_count = 0;
    $query = "SELECT id, due_date FROM book_issues 
              WHERE status = 'issued' AND due_date < NOW()";
    $result = mysqli_query($conn, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        $fine_info = calculateFine($row['due_date']);
        if ($fine_info['is_overdue']) {
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

function markFineAsPaid($conn, $issue_id) {
    $update_query = "UPDATE book_issues SET fine_paid = 1 WHERE id = '$issue_id'";
    return mysqli_query($conn, $update_query);
}

function returnBook($conn, $issue_id, $book_id) {
    $update_issue = "UPDATE book_issues SET status = 'returned' WHERE id = '$issue_id'";
    $result1 = mysqli_query($conn, $update_issue);
    $update_book = "UPDATE books SET quantity = quantity + 1 WHERE id = '$book_id'";
    $result2 = mysqli_query($conn, $update_book);
    return $result1 && $result2;
}

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

function getUserTotalFines($conn, $user_id) {
    $query = "SELECT SUM(fine_amount) as total_fines FROM book_issues 
              WHERE user_id = '$user_id' AND fine_paid = 0 AND fine_amount > 0";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['total_fines'] ? $row['total_fines'] : 0;
}
?>