# Online Library Management System (OLMS)

## Project Overview
OLMS (Online Library Management System) is a web-based application designed to manage library resources, user accounts, book borrowing, and administrative tasks. It provides a digital platform for users and administrators to interact with the library efficiently.

## Features
- **User Authentication**: Secure login for users and administrators.
- **Book Management**: Add, edit, delete, and browse books with cover images and descriptions.
- **Borrowing System**: Issue, return, and track borrowed books, including overdue management and fines.
- **User Management**: Admins can add, edit, and manage user accounts and roles.
- **Search and Browse**: Users can search and browse books by title, author, or category.
- **Statistics Dashboard**: Real-time statistics for admins and users, including total books, users, borrowed books, and categories.
- **Responsive Design**: Built with Bootstrap for mobile and desktop compatibility.

## Technologies Used
- **Frontend**: HTML, CSS (custom and Bootstrap), JavaScript
- **Backend**: PHP
- **Database**: MySQL (MariaDB)

## Folder Structure
- `Admin_dashboard/` - Admin panel for managing books, users, and requests.
- `asset/` - Static assets (CSS, images).
- `auth/` - Authentication scripts for user and admin login.
- `dashboard/` - User dashboard and book browsing/searching pages.
- `database/` - Database configuration and SQL dump for setup.
- `includes/` - Utility scripts (e.g., fine calculator).
- `uploads/` - Uploaded book cover images.

## Database Structure
- **users**: Stores user and admin details (name, email, password, role).
- **books**: Stores book details (title, author, category, description, cover, quantity).
- **book_issues**: Tracks book borrowing, due dates, return status, and fines.
- **admin**: Stores admin credentials.

Refer to `database/complete_olms_database.sql` for full schema and sample data.

## How It Works
### User Flow
1. **Registration/Login**: Users and admins log in securely.
2. **Browse/Search Books**: Users can view all books or search by criteria.
3. **Borrow Books**: Users can request to borrow available books.
4. **Return Books**: Users return books; overdue fines are calculated if applicable.
5. **View Dashboard**: Users see their borrowing status and latest books.

### Admin Flow
1. **Login**: Admins access the dashboard via secure login.
2. **Manage Books**: Add, edit, or delete books. Cannot delete books with active issues.
3. **Manage Users**: Add or edit user accounts and assign roles.
4. **Track Borrowings**: View recent borrowings and overdue books.
5. **System Statistics**: Monitor library usage and inventory.

## Security
- Passwords are securely hashed in the database.
- Session management ensures only authorized access to admin/user areas.
- Sensitive configuration details (such as database credentials) are kept out of public documentation and code comments.

## Setup Instructions
1. **Clone the Repository**
2. **Configure Database**
   - Import `database/complete_olms_database.sql` into your MySQL/MariaDB server.
   - Update `database/config.php` with your local or hosted database credentials.
3. **Set Up Web Server**
   - Place the project folder in your web server's root directory (e.g., `htdocs` for XAMPP).
   - Ensure PHP and MySQL are running.
4. **Access the Application**
   - Open the site in your browser (e.g., `http://localhost/olms`).
   - Use provided admin/user credentials to log in.

## Customization
- **Add Books**: Use the admin dashboard to add new books with cover images and descriptions.
- **Add Users**: Admins can add users or promote users to admin roles.
- **Change Styles**: Modify `asset/style.css` for custom branding.

## Contributors
- Tanish Sharma
- Mehul Suthar
- Sajal Singhal

## License
This project is for educational purposes. Do not share or publish confidential information such as database passwords or user credentials.

## Disclaimer
All sensitive information (such as actual passwords, private keys, or confidential data) is hidden and not included in this documentation. Please ensure your deployment follows best security practices.
