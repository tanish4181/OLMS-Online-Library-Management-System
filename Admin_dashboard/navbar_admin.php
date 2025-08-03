    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #dc3545;">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="#">OLMS</a>

            <button
                class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarNav"
                aria-controls="navbarNav"
                aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Centered navigation tabs -->
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="./admindashboard.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./admin_browse_book.php">Browse Books</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_users.php">Manage Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="issue_book.php">Issue Books</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="return_book.php">Return Books</a>
                    </li>
                </ul>
                
                <!-- Logout button on the right -->
                <div class="d-flex">
                    <a href="../logout.php" class="btn btn-outline-light">Logout</a>
                </div>
            </div>
        </div>
    </nav>