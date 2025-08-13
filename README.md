<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
  <h1 align="center">Classified Ads Platform</h1>
  <p align="center">A modern classified ads platform built with Laravel, Vue.js, and Inertia.js</p>
</p>

<p align="center">
  <a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
  <a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
  <a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
  <a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## 📋 Project Overview

The Classified Ads Platform is a comprehensive web application that allows users to browse, post, and manage classified advertisements. The platform features a robust admin panel, user authentication, and advanced filtering capabilities.

## ✨ Features

### 🏷️ Categories & Brands
- Dynamic category management with custom fields
- Brand management with logo support
- Hierarchical category structure
- Featured categories and brands

### 🛍️ Products/Listings
- Create and manage product listings
- Multiple product images with Spatie Media Library
- Advanced filtering and search functionality
- Product details with dynamic attributes
- Wishlist functionality

### 👤 User Features
- User registration and authentication
- Profile management
- Wishlist management
- Product management for sellers

### 🎛️ Admin Panel
- Comprehensive dashboard with statistics
- User management
- Category and brand management
- Product moderation
- Dynamic form field management for categories

### 🔍 Advanced Search & Filtering
- Filter by category, brand, price range, and condition
- Full-text search
- Sorting options
- Pagination

## 🛠️ Tech Stack

### Backend
- **PHP 8.1+**
- **Laravel 10.x**
- **MySQL/PostgreSQL**
- **Redis** (Caching & Queue)
- **Spatie Media Library** (File management)
- **Laravel Sanctum** (API Authentication)

### Frontend
- **Vue.js 3**
- **Inertia.js**
- **Tailwind CSS**
- **Alpine.js**
- **Vuex/Pinia** (State management)

### Development Tools
- **PHPUnit** (Testing)
- **Pest** (Testing)
- **Laravel Sail** (Docker development)
- **Laravel Horizon** (Queue monitoring)
- **Laravel Telescope** (Debugging)

## 🚀 Getting Started

### Prerequisites

- PHP 8.1 or higher
- Composer
- Node.js & NPM
- MySQL/PostgreSQL
- Redis (optional)

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/classified-ads-platform.git
   cd classified-ads-platform
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install JavaScript dependencies**
   ```bash
   npm install
   ```

4. **Copy environment file**
   ```bash
   cp .env.example .env
   ```

5. **Generate application key**
   ```bash
   php artisan key:generate
   ```

6. **Configure database**
   Update your `.env` file with your database credentials:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=classified_ads
   DB_USERNAME=root
   DB_PASSWORD=
   ```

7. **Run migrations and seeders**
   ```bash
   php artisan migrate --seed
   ```

8. **Link storage**
   ```bash
   php artisan storage:link
   ```

9. **Start the development server**
   ```bash
   php artisan serve
   npm run dev
   ```

10. **Access the application**
    Visit `http://localhost:8000` in your browser.

## 🧪 Testing

Run the tests with:

```bash
php artisan test
```

Or for continuous testing:

```bash
php artisan test --parallel
```

## 🔧 Environment Variables

Key environment variables:

- `APP_ENV`: Application environment (local, staging, production)
- `APP_DEBUG`: Debug mode
- `APP_URL`: Application URL
- `DB_*`: Database configuration
- `REDIS_*`: Redis configuration
- `MAIL_*`: Email configuration
- `AWS_*`: AWS S3 configuration (for file storage)
- `PUSHER_*`: Pusher configuration (for real-time features)

## 🤝 Contributing

1. Fork the project
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 👥 Contributors

<a href="https://github.com/yourusername/classified-ads-platform/graphs/contributors">
  <img src="https://contrib.rocks/image?repo=yourusername/classified-ads-platform" />
</a>

## 📄 License

This project is open-source and available under the [MIT License](LICENSE).

## 🙏 Acknowledgments

- [Laravel](https://laravel.com)
- [Vue.js](https://vuejs.org/)
- [Inertia.js](https://inertiajs.com/)
- [Tailwind CSS](https://tailwindcss.com/)
- [Spatie](https://spatie.be/)
- And all other wonderful open-source projects used in this project.
