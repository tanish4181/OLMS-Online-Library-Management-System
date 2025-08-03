<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>OLMS</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="asset/style.css" />
  </head>
  <body>
    
  <?php
    include("navbar.php");
    ?>
  
    <div class="main-content">
    <!-- Heading -->
    <div class="text-center py-4 text-white home-heading">
      <h1 class="fw-bold">OLMS – Your Digital Library Portal</h1>
      <h5>Search, borrow, and manage your books online with ease.</h5>
    </div>

    <!-- Carousel and Content in Row -->
    <div class="container my-5">
      <div class="row align-items-center">
        <!-- Carousel -->
        <div class="col-md-6 mb-4 mb-md-0">
          <div id="demo" class="carousel slide" data-bs-ride="carousel">
            <!-- Indicators -->
            <div class="carousel-indicators">
              <button
                type="button"
                data-bs-target="#demo"
                data-bs-slide-to="0"
                class="active"
              ></button>
              <button
                type="button"
                data-bs-target="#demo"
                data-bs-slide-to="1"
              ></button>
              <button
                type="button"
                data-bs-target="#demo"
                data-bs-slide-to="2"
              ></button>
            </div>

            <!-- Slides -->
            <div class="carousel-inner rounded">
              <div class="carousel-item active">
                <img
                  src="asset/images/1.jpeg"
                  class="d-block w-100"
                  alt="Browse Books"
                />
              </div>
              <div class="carousel-item">
                <img
                  src="asset/images/library1.png"
                  class="d-block w-100"
                  alt="Library 1"
                />
              </div>
              <div class="carousel-item">
                <img
                  src="asset/images/2.jpeg"
                  class="d-block w-100"
                  alt="Library 2"
                />
              </div>
            </div>

            <!-- Controls -->
            <button
              class="carousel-control-prev"
              type="button"
              data-bs-target="#demo"
              data-bs-slide="prev"
              style="background: transparent; border: none"
            >
              <span
                class="carousel-control-prev-icon"
                aria-hidden="true"
              ></span>
              <span class="visually-hidden">Previous</span>
            </button>
            <button
              class="carousel-control-next"
              type="button"
              data-bs-target="#demo"
              data-bs-slide="next"
              style="background: transparent; border: none"
            >
              <span
                class="carousel-control-next-icon"
                aria-hidden="true"
              ></span>
              <span class="visually-hidden">Next</span>
            </button>
          </div>
        </div>

        <!-- Text Content on home page -->
        <div class="col-md-6">
          <div class="home-content rounded">
            <h2 class="fw-bold">
              Welcome to OLMS (Online Library Management System)
            </h2>
            <br />
            <h5>OLMS helps you to:</h5>

            <ul>
              <li>Search and browse books easily</li>
              <li>Login as User or Admin</li>
              <li>Manage issue/return system online</li>
              <li>Track requests and view reading history</li>
            </ul>

            <a href="#" class="btn btn-primary me-2 mt-3">Explore Books</a>
            <a
              href="/auth/UserLogin.html"
              class="btn btn-outline-secondary mt-3"
              >Login Now</a
            >
          </div>
        </div>
      </div>
    </div>
    </div>
    <!-- footer -->
     <?php
    include("footer.php");
    ?>
  </body>
</html>
