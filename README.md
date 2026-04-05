# Aquahub.pro - Aquarium Blog for Beginners

Modern, SEO-optimized niche blog built with Laravel 12 and Vanilla CSS. Designed for rapid traffic growth and monetization via AdSense and Affiliate marketing.

## ✨ Key Features
- **Premium Design**: Modern "Liquid" design system using Vanilla CSS.
- **Advanced SEO**: Auto-generated ToC, Schema.org (Article, FAQ, Breadcrumb), Meta tags, and dynamic Sitemap.xml.
- **Monetization Ready**: Custom components for affiliate boxes, product highlights, and Ad slots.
- **Custom CMS**: Lightweight Admin dashboard for CRUD operations on Categories, Tags, and Posts.
- **Performance**: High Lighthouse scores via optimized assets and lightweight styling.

## 🛠 Tech Stack
- **Backend**: Laravel 12.0+ (PHP 8.2+)
- **Frontend**: Blade Components + Vanilla CSS (No Tailwind) + Vite
- **Database**: MySQL
- **SEO**: Structured data (JSON-LD), Meta optimization, clean slugs.

## 🚀 Getting Started

### 1. Installation
```bash
composer install
npm install
php artisan migrate:fresh --seed
npm run dev
```

### 2. Admin Access
- **URL**: `http://aquahub.pro/admin` (or your local domain)
- **Email**: `admin@aquahub.pro`
- **Password**: `password`

### 3. Key Components
- **ToC**: Table of Contents is auto-generated based on `<h2>` and `<h3>` tags in the post content.
- **FAQ**: Add FAQs in the admin to automatically generate FAQPage schema.
- **Social**: Integrated share buttons on every post page.
- **Affiliate**: Use the side-callout and product box components for mentions.

## 📁 Project Structure
- `app/Http/Controllers/Admin`: Admin CRUD logic.
- `app/Http/Controllers`: Public blog and search logic.
- `resources/css/app.css`: Custom Vanilla CSS design system.
- `resources/views/layouts`: Main and Admin layouts.
- `resources/views/blog`: SEO-optimized post and category templates.

## 📈 SEO Compliance
- Every page includes `canonical` tags and `open-graph` tags.
- Sitemap is generated at `/sitemap.xml`.
- Professional Disclaimer and Privacy pages are included for AdSense approval.
