# Asset Management Backend

## Overview

This repository contains a **Laravel 11** backend for an asset‑management system. It provides APIs for handling assets, expense requests, maintenance requests, and user management. The project integrates **Firebase Cloud Messaging (FCM)** to send real‑time push notifications (e.g., when an expense is approved or cancelled).

---

## Table of Contents
- [Prerequisites](#prerequisites)
- [Installation (Local)](#installation-local)
- [Docker Setup](#docker-setup)
- [Configuration](#configuration)
  - [Environment variables (`.env`)](#environment-variables)
  - [Firebase credentials](#firebase-credentials)
- [Running the Application](#running-the-application)
- [Firebase Notification Service](#firebase-notification-service)
- [Testing](#testing)
- [Deployment](#deployment)
- [Troubleshooting](#troubleshooting)
- [License](#license)

---

## Prerequisites

- **PHP 8.2+** (only needed for local development; Docker image includes it)
- **Composer** (local only)
- **Node.js & npm** (for the Vite front‑end assets)
- **Docker & Docker‑Compose** (recommended for production‑like environment)
- **Firebase service‑account JSON** file (for FCM)

---

## Installation (Local)

1. **Clone the repository**
   ```bash
   git clone <repo-url>
   cd "Asset-Management-Backend - Copy"
   ```
2. **Install PHP dependencies**
   ```bash
   composer install
   ```
3. **Install front‑end dependencies**
   ```bash
   npm install
   ```
4. **Generate the application key**
   ```bash
   php artisan key:generate
   ```
5. **Run migrations**
   ```bash
   php artisan migrate --force
   ```

---

## Docker Setup

A `docker-compose.yml` file is provided to run the entire stack (app, database, queue worker, and Nginx). The Laravel application runs inside the `php` container, which already contains the required PHP extensions.

### 1️⃣ Copy the example environment file
```bash
cp .env.example .env
```
Make sure the following values are set (you can edit them later):
```dotenv
APP_NAME="Asset Management"
APP_URL=http://localhost

# Database (Docker service name)
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=asset_backend
DB_USERNAME=root
DB_PASSWORD=secret

# Firebase (path is inside the container)
FIREBASE_CREDENTIALS=/var/www/html/storage/app/firebase/firebase_credentials.json
```

### 2️⃣ Add your Firebase service‑account file
Place the downloaded JSON at `storage/app/firebase/firebase_credentials.json` **on your host**. It will be mounted into the container automatically.

### 3️⃣ Build and start the containers
```bash
docker compose up -d --build
```
This will start:
- `php` (Laravel application)
- `mysql` (database)
- `nginx` (web server)
- `redis` (used by the queue driver)
- `worker` (queue listener for background jobs)

### 4️⃣ Run migrations & seed the database inside the container
```bash
docker compose exec php php artisan migrate --force
# (optional) seed data
# docker compose exec php php artisan db:seed
```

### 5️⃣ Access the application
Open your browser at `http://localhost` (or the custom port you configured). The API endpoints are available under the same domain.

### 6️⃣ Stopping the stack
```bash
docker compose down
```
Add `-v` if you also want to remove volumes (including the database data).

---

## Configuration

### Environment variables (`.env`)
Key variables you need to set (already shown in the Docker section):
```dotenv
APP_NAME="Asset Management"
APP_URL="http://localhost"

# Database (for Docker the host is the service name `mysql`)
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=asset_backend
DB_USERNAME=root
DB_PASSWORD=secret

# Firebase
FIREBASE_CREDENTIALS=/var/www/html/storage/app/firebase/firebase_credentials.json   # path inside the container
```

### Firebase credentials
1. In the Firebase console, create a **service‑account** and download the JSON file.
2. Place the file at `storage/app/firebase/firebase_credentials.json` on the host (it will be mounted into the container).
3. Ensure the file is readable by the container user (normally `www-data`).

---

## Running the Application (Docker)

The containers already run the application in the background. If you need to run Artisan commands, use `docker compose exec`:
```bash
# Example: watch Vite assets
docker compose exec php npm run dev

# Run the queue worker manually (usually the `worker` service does this automatically)
# docker compose exec php php artisan queue:listen --tries=1 --timeout=0
```

---

## Firebase Notification Service

The service lives at **`app/Services/FirebaseNotificationService.php`** and provides a single method:
```php
public function send(string $token, string $title, string $body): ?SendResult
```
- **Usage example** (inside a controller):
```php
if ($user && $user->fcm_token) {
    $firebaseService->send(
        $user->fcm_token,
        'Expense approved',
        'Your expense request has been approved.'
    );
}
```
- Errors are caught and logged via Laravel’s `logger()` helper; the method returns `null` on failure so the calling code can continue gracefully.

---

## Testing

Run the Laravel test suite (inside the container or locally):
```bash
docker compose exec php php artisan test
# or locally: php artisan test
```
If you wish to unit‑test the Firebase service, mock the `Factory` and `Messaging` classes.

---

## Deployment

1. **Build front‑end assets**
   ```bash
   npm run build
   ```
2. **Configure a production web server** (the provided `nginx` service is ready for production). Ensure `APP_ENV=production`.
3. **Cache configuration**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
4. **Run the queue worker** as a daemon (Supervisor, systemd, or the already‑included `worker` container).

---

## Troubleshooting

- **“FCM send failed”** – check `storage/logs/laravel.log` for details. Common causes are an invalid token, missing credentials, or network connectivity.
- **Missing `fcm_token` column** – ensure the `users` table has a nullable `fcm_token` field and that you store it when the client registers for push notifications.
- **Permission denied on credentials file** – verify the file path and that the container user can read the JSON file.
- **Docker container cannot connect to MySQL** – double‑check `DB_HOST=mysql` and that the `mysql` service is healthy (`docker compose logs mysql`).

---

## License

This project is licensed under the **MIT License**.

---

*Created by Shwe Yee Myint Myat(ShinNova-coding)*
*GoodLuck*
