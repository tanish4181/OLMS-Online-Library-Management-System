<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>

    <nav
        class="navbar navbar-expand-lg"
        style="background-color: #dc3545;">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold text-white" href="#">OLMS</a>

            <button
                class="navbar-toggler text-white"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse user-dnav d-flex justify-content-between align-items-center" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="./admindashboard.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="/OLMS/Admin_dashboard/admindashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="manage_books.php">Manage Books</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="manage_users.php">Manage Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="issue_book.php">Issue Books</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="return_book.php">Return Books</a>
                    </li>
                </ul>
                <div class="logout-button">
                    <a href="../logout.php"><button class="btn btn-secondary"> Logout </button></a>

                </div>
            </div>
        </div>
    </nav>

</body>

</html>