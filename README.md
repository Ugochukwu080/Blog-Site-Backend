# Scholarly Blog Site - Backend API

A high-fidelity RESTful API built with PHP, facilitating a curated library and archival blog management system.

## Prerequisites

To run this project locally or on a production server, ensure you have the following installed:

- **PHP 8.1+**
- **Composer** (PHP dependency manager)
- **MySQL / MariaDB** (Recommended via XAMPP for local development)
- **Apache/Nginx** with URL rewriting enabled

## Installation & Setup

Follow these steps to initialize the backend:

### 1. Clone the Repository
Clone this codebase into your web server's root directory (e.g., `C:/xampp/htdocs/Blog-Site-Backend`).

### 2. Install Dependencies
Navigate to the project root in your terminal and run:
```bash
composer install
```

### 3. Environment Configuration
The system uses a `.env` file for sensitive configurations.
- Copy the template: `cp .env.example .env` (or manually copy and rename).
- Generate a secure JWT secret:
  ```bash
  php -r "echo bin2hex(random_bytes(32));"
  ```
- Update the `JWT_SECRET` and database credentials in your new `.env` file:
  ```env
  DB_HOST=localhost
  DB_NAME=blog_site
  DB_USER=your_username
  DB_PASS=your_password
  JWT_SECRET=your_generated_secret
  ```

### 4. Database Setup
A full database export is provided in the codebase.
- Create a new database named `blog_site` in your MySQL management tool (e.g., phpMyAdmin).
- Import the schema and initial data from [config/blog_site.sql](config/blog_site.sql).

### 5. Running the Seeder (Optional)
To repopulate the database with curated scholarly entries and library items, run:
```bash
php seed.php
```

## API Endpoints

The API is structured around the following core modules:

### Library Assets (Books)
- `GET /api/books` - Retrieve the full library inventory.
- `GET /api/books/slug/{slug}` - Fetch specific publication details via slug.
- `POST /api/books` - Add new publication (Requires Auth).
- `DELETE /api/books/{id}` - Retire publication (Requires Auth).

### Blog Archive
- `GET /api/blogs` - Fetch all published scholarly essays.
- `GET /api/blogs/slug/{slug}` - Fetch a specific essay by its unique slug.
- `POST /api/blogs` - Create a new archive entry (Requires Auth).
- `PUT /api/blogs/{id}` - Update entry metadata or content (Requires Auth).

### Authentication & Management
- `POST /api/auth/login` - Authenticate as the curator.
- `GET /api/settings` - Retrieve site identity and curator profile metadata.
- `PUT /api/settings` - Update site identity (Requires Auth).

## Architecture Notes

- **Front Controller**: All requests are routed through `index.php`.
- **Security**: Authentication is handled via JWT (JSON Web Tokens).
- **Aesthetics**: File uploads and image paths are centralized to ensure high-fidelity rendering on the frontend.

---
*© 2026 Blog-Site. Built for scholarly explorations.*
