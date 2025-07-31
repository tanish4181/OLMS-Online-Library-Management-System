<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document</title>
    <link rel="stylesheet" href="../asset/style.css" />
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
  </head>
  <body class="user-dashboard my_books">
    <!-- Navbar -->
<?php include("./user_header.php"); ?>

    <!-- my books box -->
    <div class="mBook-main-box">
      <div class="mbook-heading"> <h1>My Books</h1> </div>
      <div class="mbook-borrowed"> <h3>Currently Borrowed (4)</h3> </div>
      <div class="mbook-table">
        <table class="user-table">
          <thead>
            <tr class="user-thead-row">
              <th>Book</th>
              <th>Author</th>
              <th>Borrow date</th>
              <th>Duedate</th>
              <th>status</th>
              <th>fine</th>
              <th>Reissue request</th>
            </tr>
          </thead>
          <tbody>
            <tr class="user-tbody-row">
              <td>book name plus image</td>
              <td>Author name</td>
              <td>date</td>
              <td>date</td>
              <td>active</td>
              <td>0rs</td>
              <td><button>request</button></td>
            </tr>
            <tr class="user-tbody-row">
              <td>book name plus image</td>
              <td>Author name</td>
              <td>date</td>
              <td>date</td>
              <td>active</td>
              <td>0rs</td>
              <td><button>request</button></td>
            </tr>
            <tr class="user-tbody-row">
              <td>book name plus image</td>
              <td>Author name</td>
              <td>date</td>
              <td>date</td>
              <td>active</td>
              <td>0rs</td>
              <td><button>request</button></td>
            </tr>
            <tr class="user-tbody-row">
              <td>book name plus image</td>
              <td>Author name</td>
              <td>date</td>
              <td>date</td>
              <td>active</td>
              <td>0rs</td>
              <td><button>request</button></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="mBook-main-box">
      <div class="mbook-borrowed"> <h3>Borrowing history</h3> </div>
      <div class="mbook-table">
        <table class="user-table">
          <thead>

          </thead>
          <tbody>
               <td><h1>no borrowing history</h1></td> 
          </tbody>
        </table>
      </div>
    </div>    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
