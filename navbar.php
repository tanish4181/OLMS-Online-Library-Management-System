<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>navbar</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
        rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="asset/style.css" />
</head>

<body>
    <section class="to-check-main">
        <nav class="navbar navbar-expand-lg navbar-main">
            <div class="container-fluid">
                <a class="navbar-brand" href="/olms/index.php">OLMS</a>
                <button
                    class="navbar-toggler"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent"
                    aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a
                                class="nav-link active"
                                aria-current="page"
                                href="/olms/index.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/olms/about.php">About</a>
                        </li>
                    </ul>
                    <!-- login buttons -->
                    <div class="user lg-ms-5 mb-1 me-1">
                        <a href="/olms/auth/UserLogin.php"><button
                                type="button"
                                class="btn sm-my-2 btn-warning"
                                data-bs-toggle="modal"
                                data-bs-target="#userBackdrop">
                                User
                            </button></a>
                    </div>
                    <div class="admin ms-0 mb-1">
                        <a href="/olms/auth/adminLogin.php">
                            <button
                                type="button"
                                class="btn btn-danger"
                                data-bs-toggle="modal"
                                data-bs-target="#adminBackdrop">
                                Admin
                            </button></a>
                    </div>
                </div>
            </div>
        </nav>
    </section>


</body>

</html>