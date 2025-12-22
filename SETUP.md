# E-Commerce Shop Setup Guide

## Prerequisites
- PHP 8.3+
- MySQL 8.0+
- Node.js 18+ and npm
- Composer

## Installation Steps

### 1. Install PHP Dependencies
```bash
composer install
```

### 2. Install Node Dependencies
```bash
npm install
```

### 3. Environment Configuration
Copy `.env.example` to `.env` and configure:
```bash
cp .env.example .env
php artisan key:generate
```

Update your `.env` file with database credentials:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=shop
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Run Migrations
```bash
php artisan migrate
```

### 5. Create Admin User
Run tinker to create an admin user:
```bash
php artisan tinker
```

Then run:
```php
$user = \App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => bcrypt('password'),
    'role' => 'admin',
]);
```

### 6. Build Assets
```bash
npm run build
```

Or for development:
```bash
npm run dev
```

### 7. Start Development Server
```bash
php artisan serve
```

Visit `http://localhost:8000` in your browser.

## Features

### Frontend
- Home page with hero slider and product grid
- Product listing with category filters
- Single product page with order form
- Bangladesh-specific address fields (District, Upazila, City/Village, Post Code)
- Custom product sections (rendered via React from JSON layout)

### Admin Dashboard
- Product CRUD with theme builder (drag-and-drop sections)
- Order management
- Sponsor/Affiliate management
- Settings (FB Pixel, Google Analytics, site customization)
- Sales statistics

### Affiliate System
- Multi-level referral tracking
- Unique affiliate links per user
- Sponsor dashboard with stats and referrals
- Automatic commission tracking

### Theme Builder
- Drag-and-drop page builder for products
- Section types: Rich Text, Image Gallery, FAQs, Testimonials, Video Embed
- JSON-based layout storage

## Default Admin Credentials
- Email: admin@example.com
- Password: password (change immediately!)

## Creating Sponsors
1. Register a new user or use tinker
2. Update user role to 'sponsor':
```php
$user = \App\Models\User::find(USER_ID);
$user->update(['role' => 'sponsor']);
```
3. The user will automatically get an affiliate code

## Technology Stack
- **Backend**: Laravel 12, PHP 8.3
- **Frontend**: Blade Templates, Tailwind CSS 4, Alpine.js
- **React**: Only for theme builder and custom product sections
- **Database**: MySQL
- **Styling**: Tailwind CSS with custom colors (Indigo/Blue primary, Emerald accent)

## Color Scheme
- Primary: #4F46E5 (Indigo)
- Primary Light: #6366F1
- Accent: #10B981 (Emerald)
- Fonts: Inter (English), Noto Sans Bengali (Bangla)

## Notes
- No shopping cart - direct order from product page
- Orders are tracked with referral codes automatically
- SMS integration placeholder ready for implementation
- All dashboards are mobile-responsive


