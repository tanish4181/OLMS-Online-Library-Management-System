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

// Handle request approval/rejection
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['approve_request'])) {
        $request_id = $_POST['request_id'];
        $book_id = $_POST['book_id'];
        $user_id = $_POST['user_id'];
        $admin_notes = $_POST['admin_notes'] ?? '';
        
        // Start transaction
        mysqli_begin_transaction($conn);
        
        try {
            // Update request status to approved
            $update_request = "UPDATE book_requests SET status = 'approved', admin_notes = ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $update_request);
            mysqli_stmt_bind_param($stmt, "si", $admin_notes, $request_id);
            mysqli_stmt_execute($stmt);
            
            // Reduce book quantity by 1
            $update_quantity = "UPDATE books SET quantity = quantity - 1 WHERE id = ? AND quantity > 0";
            $stmt = mysqli_prepare($conn, $update_quantity);
            mysqli_stmt_bind_param($stmt, "i", $book_id);
            mysqli_stmt_execute($stmt);
            
            if (mysqli_affected_rows($conn) > 0) {
                // Create book issue record
                $due_date = date('Y-m-d H:i:s', strtotime('+14 days'));
                $insert_issue = "INSERT INTO book_issues (user_id, book_id, request_id, due_date) VALUES (?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $insert_issue);
                mysqli_stmt_bind_param($stmt, "iiis", $user_id, $book_id, $request_id, $due_date);
                mysqli_stmt_execute($stmt);
                
                mysqli_commit($conn);
                $message = "Request approved successfully! Book has been issued to the user.";
                $message_type = "success";
            } else {
                mysqli_rollback($conn);
                $message = "Book is no longer available. Request cannot be approved.";
                $message_type = "danger";
            }
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $message = "Error processing request. Please try again.";
            $message_type = "danger";
        }
    } elseif (isset($_POST['reject_request'])) {
        $request_id = $_POST['request_id'];
        $admin_notes = $_POST['admin_notes'] ?? '';
        
        $update_request = "UPDATE book_requests SET status = 'rejected', admin_notes = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $update_request);
        mysqli_stmt_bind_param($stmt, "si", $admin_notes, $request_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $message = "Request rejected successfully.";
            $message_type = "success";
        } else {
            $message = "Error rejecting request. Please try again.";
            $message_type = "danger";
        }
    }
}

// Get all pending requests
$pending_requests_query = "SELECT br.*, b.title, b.author, b.quantity, u.fullname, u.email 
                         FROM book_requests br 
                         JOIN books b ON br.book_id = b.id 
                         JOIN users u ON br.user_id = u.id 
                         WHERE br.status = 'pending' 
                         ORDER BY br.request_date ASC";
$pending_requests = mysqli_query($conn, $pending_requests_query);

// Get all requests (for admin reference)
$all_requests_query = "SELECT br.*, b.title, b.author, u.fullname 
                      FROM book_requests br 
                      JOIN books b ON br.book_id = b.id 
                      JOIN users u ON br.user_id = u.id 
                      ORDER BY br.request_date DESC 
                      LIMIT 50";
$all_requests = mysqli_query($conn, $all_requests_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Book Requests - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../asset/style.css">
</head>
<body class="admin-dashboard">
    <!-- Include admin navbar -->
    <?php include("navbar_admin.php"); ?>
    
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <h2 class="mb-4">Manage Book Requests</h2>
                
                <!-- Display message if any -->
                <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <!-- Pending Requests Section -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h4>Pending Requests</h4>
                    </div>
                    <div class="card-body">
                        <?php if (mysqli_num_rows($pending_requests) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Book</th>
                                        <th>Author</th>
                                        <th>Available Copies</th>
                                        <th>Request Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($request = mysqli_fetch_assoc($pending_requests)): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($request['fullname']); ?></strong><br>
                                            <small class="text-muted"><?php echo htmlspecialchars($request['email']); ?></small>
                                        </td>
                                        <td><?php echo htmlspecialchars($request['title']); ?></td>
                                        <td><?php echo htmlspecialchars($request['author']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $request['quantity'] > 0 ? 'success' : 'danger'; ?>">
                                                <?php echo $request['quantity']; ?> available
                                            </span>
                                        </td>
                                        <td><?php echo date('M d, Y H:i', strtotime($request['request_date'])); ?></td>
                                        <td>
                                            <?php if ($request['quantity'] > 0): ?>
                                            <button class="btn btn-success btn-sm" 
                                                    onclick="showApproveModal(<?php echo $request['id']; ?>, '<?php echo htmlspecialchars($request['title']); ?>', <?php echo $request['book_id']; ?>, <?php echo $request['user_id']; ?>)">
                                                Approve
                                            </button>
                                            <?php endif; ?>
                                            <button class="btn btn-danger btn-sm" 
                                                    onclick="showRejectModal(<?php echo $request['id']; ?>, '<?php echo htmlspecialchars($request['title']); ?>')">
                                                Reject
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <div class="alert alert-info">
                            No pending requests at the moment.
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- All Requests Section -->
                <div class="card">
                    <div class="card-header">
                        <h4>Recent Requests (All Status)</h4>
                    </div>
                    <div class="card-body">
                        <?php if (mysqli_num_rows($all_requests) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Book</th>
                                        <th>Request Date</th>
                                        <th>Status</th>
                                        <th>Admin Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($request = mysqli_fetch_assoc($all_requests)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($request['fullname']); ?></td>
                                        <td><?php echo htmlspecialchars($request['title']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($request['request_date'])); ?></td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo $request['status'] == 'pending' ? 'warning' : 
                                                    ($request['status'] == 'approved' ? 'success' : 'danger'); 
                                            ?>">
                                                <?php echo ucfirst($request['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($request['admin_notes'] ?? 'No notes'); ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <div class="alert alert-info">
                            No requests found.
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Approve Request Modal -->
    <div class="modal fade" id="approveModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Approve Book Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <p>Are you sure you want to approve the request for <strong id="approveBookTitle"></strong>?</p>
                        <div class="mb-3">
                            <label for="adminNotes" class="form-label">Admin Notes (Optional)</label>
                            <textarea class="form-control" id="adminNotes" name="admin_notes" rows="3" placeholder="Add any notes for the user..."></textarea>
                        </div>
                        <input type="hidden" name="request_id" id="approveRequestId">
                        <input type="hidden" name="book_id" id="approveBookId">
                        <input type="hidden" name="user_id" id="approveUserId">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="approve_request" class="btn btn-success">Approve Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reject Request Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reject Book Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <p>Are you sure you want to reject the request for <strong id="rejectBookTitle"></strong>?</p>
                        <div class="mb-3">
                            <label for="rejectNotes" class="form-label">Reason for Rejection (Optional)</label>
                            <textarea class="form-control" id="rejectNotes" name="admin_notes" rows="3" placeholder="Please provide a reason for rejection..."></textarea>
                        </div>
                        <input type="hidden" name="request_id" id="rejectRequestId">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="reject_request" class="btn btn-danger">Reject Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showApproveModal(requestId, bookTitle, bookId, userId) {
            document.getElementById('approveRequestId').value = requestId;
            document.getElementById('approveBookTitle').textContent = bookTitle;
            document.getElementById('approveBookId').value = bookId;
            document.getElementById('approveUserId').value = userId;
            new bootstrap.Modal(document.getElementById('approveModal')).show();
        }
        
        function showRejectModal(requestId, bookTitle) {
            document.getElementById('rejectRequestId').value = requestId;
            document.getElementById('rejectBookTitle').textContent = bookTitle;
            new bootstrap.Modal(document.getElementById('rejectModal')).show();
        }
    </script>
</body>
</html> 