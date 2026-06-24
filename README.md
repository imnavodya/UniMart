# UniMart (NSBM Exclusive)

UniMart is a sleek, modern, and premium e-commerce marketplace built specifically for university students. It features a beautiful dark glassmorphism design, real-time order tracking, and a robust admin panel.

## Features
- **Premium UI/UX**: Dark mode glassmorphism interface with smooth animations and dynamic styling.
- **User Dashboard**: Customers can track their orders live and update their profiles (including avatars).
- **Admin Panel**: Full control over products, categories, and live order status management.
- **AJAX Interactions**: Seamless add-to-cart, dynamic profile updates, and live-syncing order statuses without page reloads.
- **Responsive**: Fully optimized for both desktop and mobile devices.

## Installation & Setup

1. **Clone the Repository**
   Clone this repository into your local server environment (like XAMPP, WAMP, or MAMP) inside the `htdocs` or `www` directory.

2. **Database Setup**
   - Create a new MySQL database named `unimart` (or a name of your choice).
   - Import the included `database.sql` file into this database. This single file contains all the necessary tables (`users`, `categories`, `products`, `orders`, `order_items`) and some demo data to get you started immediately.

3. **Configuration Changes**
   Before running the app, make sure to update the database credentials to match your local setup.
   - Open `config/database.php`
   - Update the `$host`, `$dbname`, `$username`, and `$password` variables:
     ```php
     $host = '127.0.0.1';
     $dbname = 'unimart';
     $username = 'root';
     $password = ''; 
     ```

4. **Base URL / Path Adjustments**
   The project uses absolute paths based on the folder name `UniMart`.
   - If you rename the repository folder (e.g., from `UniMart` to something else like `my-shop`), you will need to do a global search and replace in the codebase to change `/UniMart/` to `/<your-new-folder-name>/`.
   - *Tip: If you run this project on a live production server at the root domain (e.g., `https://example.com`), change `/UniMart/` to simply `/` everywhere.*

## Default Accounts

To test the application, you can use the following default accounts included in the database:

**Admin Account:**
- **Email:** admin@unimart.com
- **Password:** password123

**Customer Account:**
- **Email:** john@unimart.com
- **Password:** password123

## Tech Stack
- **Frontend**: HTML5, Vanilla CSS, JavaScript, Bootstrap 5.
- **Backend**: PHP
- **Database**: MySQL / MariaDB

---
