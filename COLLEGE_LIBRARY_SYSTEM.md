# College Library Management System (OLMS)

## 📚 Overview

This is a **real college library workflow** system where:
- **Users (Students/Staff)** can only view books, search, and see their borrowing history
- **Admins (Librarians)** have full control over issuing, returning, and managing books

## 🔐 User Restrictions

### What Users CANNOT do:
- ❌ Request books
- ❌ Issue or buy books themselves
- ❌ Return books themselves
- ❌ Pay fines themselves

### What Users CAN do:
- ✅ View all available books
- ✅ Search books by title or author
- ✅ See their currently issued books
- ✅ View their borrowing history
- ✅ See fine amounts (but cannot pay them)

## 🧑‍💼 Admin-Only Features

### What Admins CAN do:
- ✅ **Issue Books**: Manually assign books to users with due dates
- ✅ **Return Books**: Process book returns and handle fines
- ✅ **Manage Books**: Add, edit, delete book details
- ✅ **Manage Users**: View, edit, delete user accounts
- ✅ **Handle Fines**: Mark fines as paid when users pay

## 📁 File Structure

```
olms/
├── auth/                          # Authentication files
│   ├── adminLogin.php             # Admin login
│   └── UserLogin.php              # User login
├── dashboard/                     # User interface
│   ├── user_dashboard.php         # User home page
│   ├── browse_books.php           # View all books
│   ├── search_books.php           # Search functionality
│   ├── my_books.php              # User's book history
│   └── user_header.php            # User navigation
├── Admin_dashboard/               # Admin interface
│   ├── admindashboard.php         # Admin home page
│   ├── issue_book.php            # Issue books to users
│   ├── return_book.php           # Return books and handle fines
│   ├── manage_books.php          # Manage book inventory
│   ├── manage_users.php          # Manage user accounts
│   ├── add_book.php              # Add new books
│   ├── add_user.php              # Add new users
│   └── navbar_admin.php          # Admin navigation
├── database/                      # Database files
│   ├── config.php                # Database connection
│   └── college_library_system.sql # Database structure
├── includes/                      # Helper functions
│   └── fine_calculator.php       # Fine calculation logic
└── asset/                        # CSS and images
    └── style.css                 # Styling
```

## 🗄️ Database Structure

### Tables:
1. **`users`** - Student and staff accounts
2. **`admin`** - Librarian accounts  
3. **`books`** - Book inventory
4. **`book_issues`** - Book borrowing records

### Key Features:
- **Foreign Key Constraints** for data integrity
- **Fine System** with ₹2/day after 14 days
- **Status Tracking** (issued, returned, overdue)
- **Transaction Safety** for book operations

## 🚀 Setup Instructions

### 1. Database Setup
```sql
-- Import the database structure
mysql -u root -p olms < database/college_library_system.sql
```

### 2. Default Credentials
- **Admin**: admin@library.com / password
- **Users**: john@example.com / password (and others)

### 3. Start XAMPP
- Start Apache and MySQL services
- Access: `http://localhost/olms`

## 📖 How It Works

### For Users:
1. **Login** with email/password
2. **Browse Books** - View all available books
3. **Search Books** - Find specific books by title/author
4. **My Books** - See current issues and history
5. **Contact Librarian** - For any book operations

### For Admins:
1. **Login** with admin credentials
2. **Issue Books** - Select user + book + due date
3. **Return Books** - Process returns and handle fines
4. **Manage Inventory** - Add/edit/delete books
5. **Manage Users** - Add/edit/delete user accounts

## 💰 Fine System

- **Due Period**: 14 days from issue date
- **Fine Rate**: ₹2 per day after due date
- **Calculation**: Automatic based on current date
- **Payment**: Admin marks fines as paid

## 🔧 Key Features

### Search Functionality
- Search by book title or author
- Partial matching (LIKE queries)
- Real-time results display

### Book Management
- Add books with cover images
- Edit book details (title, author, quantity)
- Delete books (with safety checks)
- Track available quantities

### User Management
- Add new users with roles
- Edit user information
- Delete users (with safety checks)
- View user borrowing history

### Issue/Return System
- **Issue**: Admin selects user + book + due date
- **Return**: Admin processes return + handles fines
- **Safety**: Database transactions ensure consistency
- **Tracking**: Complete history of all operations

## 🛡️ Security Features

- **Session Management** - Secure login/logout
- **Input Validation** - All user inputs sanitized
- **SQL Injection Protection** - Prepared statements
- **Role-Based Access** - Users vs Admin permissions
- **Data Integrity** - Foreign key constraints

## 📱 User Interface

### User Dashboard:
- Clean, simple interface
- No action buttons (view-only)
- Clear navigation
- Responsive design

### Admin Dashboard:
- Full control interface
- Action buttons for all operations
- Detailed tables and forms
- Professional layout

## 🎯 Learning Benefits

### For Beginners:
- **Simple PHP Logic** - No frameworks, just basic PHP
- **Clear File Structure** - Each operation in separate file
- **Commented Code** - Inline explanations
- **Real-World Workflow** - Actual library operations
- **Database Basics** - MySQL with proper relationships

### Key Concepts Demonstrated:
- Session management
- Database operations
- Form handling
- Input validation
- Error handling
- User authentication
- Role-based access control

## 🔄 Workflow Example

1. **Student** logs in and searches for a book
2. **Student** finds the book but cannot borrow it themselves
3. **Student** goes to library desk
4. **Librarian** logs into admin panel
5. **Librarian** issues book to student with due date
6. **Student** can see the book in "My Books"
7. **Student** returns book to library desk
8. **Librarian** processes return and handles any fines
9. **Book** is back in inventory

This system mimics a **real college library** where only librarians can issue/return books, and students can only view and search the catalog.

## 🚨 Important Notes

- **No User Actions**: Users cannot perform any book operations
- **Admin Only**: All book management is admin-controlled
- **Real Library Workflow**: Follows actual library procedures
- **Beginner Friendly**: Simple PHP, clear structure, good comments
- **Production Ready**: Includes security and error handling 