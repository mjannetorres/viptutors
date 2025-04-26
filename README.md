# Project Name (Laravel API)

Brief description of the Laravel project and its purpose.

---

## 🚀 Getting Started

Instructions to set up the project locally.

### Prerequisites

-   PHP >= 8.1
-   Composer
-   MySQL or PostgreSQL
-   Laravel CLI (optional)
-   Postman (for API testing)

---

## 📦 Installation

```bash
# Clone the repository
git clone https://github.com/your-username/your-repo.git
cd your-repo

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate app key
php artisan key:generate

# Update .env with your database credentials:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Run database migrations:
php artisan migrate

# Run the server:
php artisan serve

# API Testing with Postman

Open Postman.

Import the provided VIPTutors.postman_collection.json file.

(Optional) Set up an environment with:

base_url = http://127.0.0.1:8000/api

Authenticate using the login endpoint and use the Bearer Token for protected routes.

```
