# OLMS Book Request and Issue System - Setup Instructions

## Overview
This system implements a complete book request and issue management system with fine calculation, user management, and admin controls.

## Features Implemented

### ✅ Book Request & Approval System
- Users can request books from available inventory
- Admin can approve/reject requests with notes
- Automatic book quantity management
- 14-day borrowing period with fine calculation

### ✅ Fine System
- ₹2 per day fine after 14 days
- Automatic fine calculation based on due dates
- Fine payment tracking
- Overdue status management

### ✅ Database Structure
- `book_requests` - Track user requests
- `book_issues` - Track issued books and fines
- Foreign key constraints for data integrity
- Sample books included

### ✅ Admin Features
- Manage book requests (approve/reject)
- Add, edit, delete books
- Manage users (view, edit, delete)
- View all system statistics

### ✅ User Features
- Request books from available inventory
- View current book issues and history
- Return books and pay fines
- Track borrowing history

## Database Setup

### Step 1: Import Database Structure
1. Open phpMyAdmin
2. Select your `olms` database
3. Go to "Import" tab
4. Upload and import `database/book_system.sql`
5. This will create:
   - `book_requests` table
   - `book_issues` table
   - Sample books data
   - Foreign key constraints

### Step 2: Verify Tables
After import, you should have these tables:
- `users` (existing)
- `books` (existing, with sample data)
- `book_requests` (new)
- `book_issues` (new)

## File Structure

```
olms/
├── database/
│   ├── config.php (database connection)
│   ├── book_system.sql (database structure)
│   └── olms.sql (existing database)
├── includes/
│   └── fine_calculator.php (fine calculation logic)
├── Admin_dashboard/
│   ├── admindashboard.php (main admin dashboard)
│   ├── manage_books.php (book management)
│   ├── manage_users.php (user management)
│   ├── manage_requests.php (request approval)
│   └── navbar_admin.php (admin navigation)
├── dashboard/
│   ├── user_dashboard.php (user dashboard)
│   ├── request_book.php (book requests)
│   ├── my_books.php (user book history)
│   └── user_header.php (user navigation)
└── asset/
    └── style.css (styling)
```

## How to Use

### For Users:
1. **Login** to your user account
2. **Request Books** - Browse available books and submit requests
3. **My Books** - View current issues, return books, pay fines
4. **Track History** - See your borrowing history

### For Admins:
1. **Login** to admin account
2. **Manage Requests** - Approve/reject book requests
3. **Manage Books** - Add, edit, delete books
4. **Manage Users** - View, edit, delete users
5. **Dashboard** - View system statistics

## Key Features Explained

### Book Request Flow:
1. User requests a book → Request stored in `book_requests`
2. Admin reviews request → Can approve/reject with notes
3. If approved → Book issued, quantity reduced, entry in `book_issues`
4. User can return book → Quantity increased, fine calculated

### Fine Calculation:
- Books can be kept for 14 days
- After 14 days: ₹2 per day fine
- Fines are calculated automatically
- Users can pay fines through the system

### Security Features:
- Prepared statements prevent SQL injection
- Session-based authentication
- Input validation and sanitization
- Foreign key constraints maintain data integrity

## Troubleshooting

### Common Issues:

1. **Database Connection Error**
   - Ensure MySQL server is running
   - Check database credentials in `database/config.php`
   - Verify database name is correct

2. **Tables Not Found**
   - Import `database/book_system.sql` completely
   - Check for any import errors in phpMyAdmin

3. **Session Issues**
   - Ensure `session_start()` is called in all pages
   - Check login status before accessing protected pages

4. **Fine Calculation Issues**
   - Verify server timezone is set correctly
   - Check `includes/fine_calculator.php` functions

## Testing the System

### Test User Flow:
1. Login as a regular user
2. Go to "Request Books"
3. Request a book
4. Check "My Books" to see request status

### Test Admin Flow:
1. Login as admin
2. Go to "Manage Requests"
3. Approve a book request
4. Check "Manage Books" to see quantity reduced

### Test Fine System:
1. Issue a book to a user
2. Wait or manually update due date to past date
3. Check fine calculation in "My Books"

## Customization

### Adding New Categories:
Edit `Admin_dashboard/manage_books.php` and add new options to the category dropdown.

### Changing Fine Rate:
Edit `includes/fine_calculator.php` and modify the fine calculation:
```php
$fine_amount = $days_overdue * 2.00; // Change 2.00 to your rate
```

### Changing Borrowing Period:
Edit `Admin_dashboard/manage_requests.php` and modify:
```php
$due_date = date('Y-m-d H:i:s', strtotime('+14 days')); // Change 14 to your period
```

## Support

If you encounter any issues:
1. Check the error messages displayed on the page
2. Verify database connection and table structure
3. Ensure all files are in the correct directories
4. Check file permissions for uploads (if using file uploads)

## Security Notes

- All user inputs are sanitized using `htmlspecialchars()`
- Database queries use prepared statements
- Session management prevents unauthorized access
- Passwords should be hashed (implemented in existing auth system)

This system provides a complete, beginner-friendly book management solution with proper error handling and security measures. 