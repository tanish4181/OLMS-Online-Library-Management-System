   
   <nav
      class="navbar navbar-expand-lg bg-primary">
      <div class="container-fluid">
        <a class="navbar-brand fw-bold text-white" href="./user_dashboard.php">OLMS</a>

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
              <a class="nav-link text-white" href="./user_dashboard.php">Home</a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-white" href="./browse_books.php">Browse Books</a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-white" href="./search_books.php">Search Books</a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-white" href="./my_books.php">My Books</a>
            </li>
          </ul>
          <div class="logout-button">
            <a href="../logout.php"><button class="btn btn-secondary"> Logout </button></a>

          </div>
        </div>
      </div>
    </nav>