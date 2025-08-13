# Classified Ads Platform

A modern classified ads platform built with Laravel, Vue 3, and Inertia.js. Users can browse, search, and manage listings; admins can manage categories, brands, and products.

## Features

- Categories and brands management (featured flags, hierarchy for categories)
- Product listings with multiple images (Spatie Media Library)
- Dynamic product fields per category
- Powerful filters (category, brand, price range, condition, sort)
- Wishlist
- Auth: register, login, password reset, email verification
- Admin panel with dashboard and CRUD for categories, brands, products

## Tech Stack

- Backend: PHP 8.1+, Laravel 10
- Frontend: Vue 3, Inertia.js, Tailwind CSS
- Files: Spatie Laravel Media Library
- API Auth: Laravel Sanctum
- Testing: PHPUnit

## Getting Started

### Prerequisites
- PHP 8.1+
- Composer
- Node.js & npm
- A database (MySQL/PostgreSQL/SQLite)

### Installation

1. Clone and install dependencies
```bash
git clone https://github.com/yourusername/classified-ads-platform.git
cd classified-ads-platform
composer install
npm install
```

2. Copy env and generate key
```bash
cp .env.example .env
php artisan key:generate
```

3. Configure database in `.env`
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=classified_ads
DB_USERNAME=root
DB_PASSWORD=
```

4. Configure mail (example: Gmail SMTP)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=prosenjitbiswas983@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=prosenjitbiswas983@gmail.com
MAIL_FROM_NAME="Ads Platform"
```
- Tip: For Gmail, create an App Password and use that as `MAIL_PASSWORD`.
- For local development without SMTP, you can use `MAIL_MAILER=log` to log outgoing emails.

5. Run migrations and seeders
```bash
php artisan migrate --seed
php artisan storage:link
```

6. Start servers
```bash
php artisan serve
npm run dev
```
Visit http://localhost:8000

## Admin Login

- Email: `admin@example.com`
- Password: `admin123`

These are created by the database seeder. You can change them in `database/seeders/DatabaseSeeder.php`.

## Scripts

- Run tests: `php artisan test`
- Run tests in parallel: `php artisan test --parallel`

## API

Public API endpoints:
- `GET /api/products` – list with filters (category_id, brand_id, min_price, max_price, condition, sort_by, per_page)
- `GET /api/products/{slug}` – product details

Wishlist (requires Sanctum auth):
- `GET /api/wishlist`
- `POST /api/wishlist/{product}`
- `DELETE /api/wishlist/{wishlistItem}`
- `PUT /api/wishlist/{wishlistItem}`
- `POST /api/wishlist/{product}/toggle`
- `DELETE /api/wishlist`

## Development Notes

- New user registration triggers a welcome email (`App\\Mail\\WelcomeMail`).
- Controllers use Form Request validation (`app/Http/Requests/...`).
- Image uploads stored via Spatie Media Library; run `php artisan storage:link`.

## Troubleshooting

- If emails are not sending, set `MAIL_MAILER=log` to debug and run `php artisan config:clear` after `.env` edits.
- If `/` errors during tests due to missing tables, ensure migrations run; this route is guarded for empty schemas in tests.

## License

MIT
