# Request & Calculation Workflow System

A full-stack web application with Laravel API backend and Vue 3 frontend.

## Features

- ✅ Laravel 11 REST API Backend
- ✅ Vue 3 + TypeScript Frontend
- ✅ Authentication with Laravel Sanctum
- ✅ Role-based access (Admin & User)
- ✅ Clean dashboard with sidebar and navbar
- ✅ Axios for API communication
- ✅ Pinia for state management

## Tech Stack

**Backend:**
- Laravel 11
- MySQL Database
- Laravel Sanctum (API Authentication)

**Frontend:**
- Vue 3 with JavaScript
- Vue Router
- Pinia (State Management)
- Axios

## Prerequisites

- PHP 8.2+
- Composer
- Node.js 18+
- MySQL
- XAMPP or similar (for MySQL)

## Installation

### 1. Database Setup

Create a MySQL database named `request_workflow`:

```sql
CREATE DATABASE request_workflow;
```

### 2. Backend Setup

```bash
cd backend

# Install dependencies (if not already done)
composer install

# Run migrations
php artisan migrate

# Seed database with test users
php artisan db:seed

# Start Laravel server
php artisan serve
```

The backend will run on `http://localhost:8000`

### 3. Frontend Setup

```bash
cd frontend

# Install dependencies (if not already done)
npm install

# Start development server
npm run dev
```

The frontend will run on `http://localhost:5173`

## Demo Credentials

**Admin Account:**
- Email: admin@example.com
- Password: password

**User Account:**
- Email: user@example.com
- Password: password

## Project Structure

```
.
├── backend/                 # Laravel API
│   ├── app/
│   │   ├── Http/
│   │   │   └── Controllers/
│   │   │       └── AuthController.php
│   │   └── Models/
│   │       └── User.php
│   ├── database/
│   │   ├── migrations/
│   │   └── seeders/
│   ├── routes/
│   │   └── api.php
│   └── .env
│
└── frontend/               # Vue 3 Application
    ├── src/
    │   ├── views/
    │   │   ├── LoginView.vue
    │   │   └── DashboardView.vue
    │   ├── stores/
    │   │   └── auth.ts
    │   ├── services/
    │   │   └── api.ts
    │   ├── router/
    │   │   └── index.ts
    │   └── App.vue
    └── package.json
```

## API Endpoints

### Authentication
- `POST /api/login` - Login user
- `POST /api/logout` - Logout user (requires auth)
- `GET /api/me` - Get authenticated user (requires auth)

## Features Overview

### Authentication System
- Secure login with Laravel Sanctum
- Token-based authentication
- Auto-redirect based on auth state

### Dashboard
- Clean, modern UI
- Sidebar navigation
- Top navbar with user info
- Role-based display (admin/user)
- Statistics cards
- Recent activity section

### Roles
- **Admin**: Full access to all features
- **User**: Standard user access

## Development

### Backend Commands

```bash
# Run migrations
php artisan migrate

# Seed database
php artisan db:seed

# Clear cache
php artisan cache:clear
php artisan config:clear
```

### Frontend Commands

```bash
# Development server
npm run dev

# Build for production
npm run build

# Preview production build
npm run preview
```

## Notes

- Make sure MySQL is running before starting the backend
- Update `.env` file in backend if your MySQL credentials differ
- CORS is configured to allow requests from `http://localhost:5173`
- The system is ready for further feature development

## Next Steps

You can now extend this system with:
- Request management features
- Calculation workflows
- User management (for admins)
- Reports and analytics
- File uploads
- Notifications

## License

MIT
