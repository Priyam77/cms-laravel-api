# 📘 CMS Laravel API

A simple Laravel-based CMS API with Admin and Author roles, built for managing articles and categories. Includes authentication and Postman collection for quick testing.

---

## 🔗 Postman Collection

👉 [Click to open the Postman Collection](https://web.postman.co/workspace/Personal-Workspace~eeb6648b-f37a-4d77-9db8-d303b1fb2b05/collection/42422813-50df7de5-9a43-4f48-8e25-fc9c1f533cb3?action=share&source=copy-link&creator=42422813)  
_Import this into Postman to test all available APIs._

---

## 🚀 Getting Started

### 1. Clone the Repo

```bash
git clone https://github.com/Priyam77/cms-laravel-api.git
cd cms-laravel-api
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` and update your DB details:

```
DB_DATABASE=cms_db
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 4. Migrate & Seed the Database

```bash
php artisan migrate --seed
```

🧪 Login Credentials after seeding:

- **Admin**  
  `admin@example.com / password`

- **Author**  
  `author@example.com / password`

### 5. Start the Server

```bash
php artisan serve
```

---

## 🧠 Slug & Summary Generator

- Articles have slug and summary generated **asynchronously**.
- To process jobs, run:

```bash
php artisan queue:work
```

---

## 📂 Key APIs

| Function           | Method | Endpoint             |
|--------------------|--------|----------------------|
| Login              | POST   | `/api/login`         |
| Logout             | POST   | `/api/logout`        |
| Create Article     | POST   | `/api/articles`      |
| List Articles      | GET    | `/api/articles`      |
| Category CRUD      | Various| `/api/categories`    |

> Full list available in Postman Collection.

---

## 🧑‍💻 Maintainer

**GitHub:** [@Priyam77](https://github.com/Priyam77)

---

## 📃 License

MIT License
