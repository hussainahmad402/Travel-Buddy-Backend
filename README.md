# 🌍 Travel Buddy Backend API

A RESTful backend API built with **Laravel 12** for the **Travel Buddy App**.  
This API handles **user authentication, trip management, and document storage**, designed to be consumed by a **mobile app (Flutter)** or **web frontend**.

---

## 🚀 Features
- 🔑 **Authentication with OTP & JWT**
- 📧 **Email OTP Verification & Password Reset**
- 👤 **User Profile Management**
- 🗺️ **Trip Management (CRUD)**
- 📂 **Document Upload & Management**
- 📡 **RESTful APIs with clean JSON responses**
- 🗄️ **MySQL Database Support**
- ⏱️ **OTP with expiry (5 minutes)**
- 🧪 **API Testing with Postman Collection**

---

## 📦 Tech Stack
- **Framework**: Laravel 12 (Latest Stable)
- **Database**: MySQL
- **Authentication**: JWT (JSON Web Token)
- **Email**: Laravel Mail (SMTP configuration in `.env`)
- **Architecture**: MVC + API Resources/Transformers
- **Other**: Migrations & Seeders, Postman Collection

---

## ⚙️ Installation & Setup

### 1️⃣ Clone the repository
```bash
git clone https://github.com/your-username/travel-buddy-backend.git
cd travel-buddy-backend
```
##  Install dependencies
```
composer install
```
## Create .env file
```
cp .env.example .env
```

### Update .env with your own settings:
```
APP_NAME=TravelBuddy
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=travel_buddy
DB_USERNAME=root
DB_PASSWORD=

# Mail (for OTP)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_password_or_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your_email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"

# JWT
JWT_SECRET=

# File Storage
FILESYSTEM_DISK=public
```

## Generate app key , Generate JWT secret & Run migrations
```
php artisan key:generate
php artisan jwt:secret
php artisan migrate
php artisan serve
```
