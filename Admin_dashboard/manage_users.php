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
    // Handle adding a new user
    if (isset($_POST['add_user'])) {
        $fullname = trim($_POST['fullname']);
        $email = trim($_POST['email']);
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        $address = trim($_POST['address']);
        $role = $_POST['role'];
        
        // Check if all required fields are filled
        if ($fullname && $email && $username && $password) {
            // Check if username or email already exists
            $check_query = "SELECT id FROM users WHERE username = '$username' OR email = '$email'";
            $result = mysqli_query($conn, $check_query);
            
            if (mysqli_num_rows($result) > 0) {
                $message = "Username or email already exists. Please choose different credentials.";
                $message_type = "warning";
            } else {
                // Hash the password before storing
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                $insert_query = "INSERT INTO users (fullname, email, username, password, address, role) VALUES ('$fullname', '$email', '$username', '$hashed_password', '$address', '$role')";
                
                if (mysqli_query($conn, $insert_query)) {
                    $message = "User added successfully!";
                    $message_type = "success";
                } else {
                    $message = "Error adding user. Please try again.";
                    $message_type = "danger";
                }
            }
        } else {
            $message = "Please fill all required fields.";
            $message_type = "warning";
        }
    } 
    // Handle editing a user
    elseif (isset($_POST['edit_user'])) {
        $user_id = $_POST['user_id'];
        $fullname = trim($_POST['fullname']);
        $email = trim($_POST['email']);
        $username = trim($_POST['username']);
        $address = trim($_POST['address']);
        $role = $_POST['role'];
        
        // Check if all required fields are filled
        if ($fullname && $email && $username) {
            $update_query = "UPDATE users SET fullname = '$fullname', email = '$email', username = '$username', address = '$address', role = '$role' WHERE id = '$user_id'";
            
            if (mysqli_query($conn, $update_query)) {
                $message = "User updated successfully!";
                $message_type = "success";
            } else {
                $message = "Error updating user. Please try again.";
                $message_type = "danger";
            }
        } else {
            $message = "Please fill all required fields.";
            $message_type = "warning";
        }
    } 
    // Handle deleting a user
    elseif (isset($_POST['delete_user'])) {
        $user_id = $_POST['user_id'];
        
        // Check if user has active book issues
        $check_issues = "SELECT COUNT(*) as issue_count FROM book_issues WHERE user_id = '$user_id' AND status IN ('issued', 'overdue')";
        $result = mysqli_query($conn, $check_issues);
        $issue_count = mysqli_fetch_assoc($result)['issue_count'];
        
        if ($issue_count > 0) {
            $message = "Cannot delete user. They have " . $issue_count . " active book issue(s).";
            $message_type = "warning";
        } else {
            $delete_query = "DELETE FROM users WHERE id = '$user_id'";
            
            if (mysqli_query($conn, $delete_query)) {
                $message = "User deleted successfully!";
                $message_type = "success";
            } else {
                $message = "Error deleting user. Please try again.";
                $message_type = "danger";
            }
        }
    }
}

// Get all users from the database
$users_query = "SELECT * FROM users ORDER BY fullname";
$users_result = mysqli_query($conn, $users_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../asset/style.css">
</head>
<body class="admin-dashboard">
    <!-- Include admin navbar -->
    <?php include("navbar_admin.php"); ?>
    
    <div class="main-content">
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Manage Users</h2>
                    <button class="btn btn-success" onclick="addUser()">
                        <i class="bi bi-plus-circle"></i> Add New User
                    </button>
                </div>
                
                <!-- Display message if any -->
                <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <!-- Users Table -->
                <div class="card">
                    <div class="card-header">
                        <h4>All Users</h4>
                    </div>
                    <div class="card-body">
                        <?php if (mysqli_num_rows($users_result) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Full Name</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Address</th>
                                        <th>Role</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($user = mysqli_fetch_assoc($users_result)): ?>
                                    <tr>
                                        <td><?php echo $user['id']; ?></td>
                                        <td><?php echo htmlspecialchars($user['fullname']); ?></td>
                                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td><?php echo htmlspecialchars($user['address']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $user['role'] == 'admin' ? 'danger' : 'primary'; ?>">
                                                <?php echo ucfirst($user['role']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" 
                                                    onclick="editUser(<?php echo htmlspecialchars(json_encode($user)); ?>)">
                                                Edit
                                            </button>
                                            <?php if ($user['role'] != 'admin'): ?>
                                            <button class="btn btn-sm btn-danger" 
                                                    onclick="deleteUser(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['fullname']); ?>')">
                                                Delete
                                            </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <div class="alert alert-info">
                            No users found.
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="addFullname" class="form-label">Full Name *</label>
                            <input type="text" class="form-control" id="addFullname" name="fullname" required>
                        </div>
                        <div class="mb-3">
                            <label for="addUsername" class="form-label">Username *</label>
                            <input type="text" class="form-control" id="addUsername" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="addEmail" class="form-label">Email *</label>
                            <input type="email" class="form-control" id="addEmail" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="addPassword" class="form-label">Password *</label>
                            <input type="password" class="form-control" id="addPassword" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="addAddress" class="form-label">Address</label>
                            <textarea class="form-control" id="addAddress" name="address" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="addRole" class="form-label">Role *</label>
                            <select class="form-control" id="addRole" name="role" required>
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="add_user" class="btn btn-success">Add User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="user_id" id="editUserId">
                        <div class="mb-3">
                            <label for="editFullname" class="form-label">Full Name *</label>
                            <input type="text" class="form-control" id="editFullname" name="fullname" required>
                        </div>
                        <div class="mb-3">
                            <label for="editUsername" class="form-label">Username *</label>
                            <input type="text" class="form-control" id="editUsername" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="editEmail" class="form-label">Email *</label>
                            <input type="email" class="form-control" id="editEmail" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="editAddress" class="form-label">Address</label>
                            <textarea class="form-control" id="editAddress" name="address" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="editRole" class="form-label">Role *</label>
                            <select class="form-control" id="editRole" name="role" required>
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="edit_user" class="btn btn-primary">Update User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete User Modal -->
    <div class="modal fade" id="deleteUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <p>Are you sure you want to delete <strong id="deleteUserName"></strong>?</p>
                        <p class="text-danger">This action cannot be undone.</p>
                        <input type="hidden" name="user_id" id="deleteUserId">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="delete_user" class="btn btn-danger">Delete User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>
  <?php
  include("footer.php");
  ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function addUser() {
            // Clear the form
            document.getElementById('addFullname').value = '';
            document.getElementById('addUsername').value = '';
            document.getElementById('addEmail').value = '';
            document.getElementById('addPassword').value = '';
            document.getElementById('addAddress').value = '';
            document.getElementById('addRole').value = 'user';
            new bootstrap.Modal(document.getElementById('addUserModal')).show();
        }
        
        function editUser(user) {
            document.getElementById('editUserId').value = user.id;
            document.getElementById('editFullname').value = user.fullname;
            document.getElementById('editUsername').value = user.username;
            document.getElementById('editEmail').value = user.email;
            document.getElementById('editAddress').value = user.address;
            document.getElementById('editRole').value = user.role;
            new bootstrap.Modal(document.getElementById('editUserModal')).show();
        }
        
        function deleteUser(userId, userName) {
            document.getElementById('deleteUserId').value = userId;
            document.getElementById('deleteUserName').textContent = userName;
            new bootstrap.Modal(document.getElementById('deleteUserModal')).show();
        }
    </script>
</body>
</html> 