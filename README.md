# Mini CRM - Laravel 12 + Vue 3 + Inertia.js

A comprehensive Company, Employee, and User management system built with modern web technologies, following SOLID principles and clean architecture best practices.


## ✨ Features

### Core Functionality

#### 🏢 Company Management
- ✅ Full CRUD operations (Create, Read, Update, Delete)
- ✅ Company listing with search and sorting
- ✅ Pagination (15 items per page)
- ✅ Employee count per company
- ✅ Detailed company view
- ✅ Role-based access control

#### 👥 Employee Management
- ✅ Full CRUD operations
- ✅ Employee listing with search and sorting
- ✅ Pagination (15 items per page)
- ✅ Associate employees with companies
- ✅ Detailed employee view
- ✅ **Automatic email notification** when employee is created
- ✅ Role-based access control

#### 👤 User Management
- ✅ Full CRUD operations
- ✅ User listing with search and sorting
- ✅ Pagination (15 items per page)
- ✅ Role management (Admin / Company User)
- ✅ Associate users with companies
- ✅ Password management
- ✅ Profile editing

### Authentication & Authorization
- ✅ Laravel Breeze (Inertia + Vue) authentication
- ✅ Role-based access control (Admin & Company User)
- ✅ Protected routes with middleware
- ✅ Laravel Policies for fine-grained authorization


### Email Notifications
- ✅ **Automatic email when employee is created**
- ✅ Sent to company email address
- ✅ Professional HTML email template
- ✅ Mailhog integration for testing
- ✅ Responsive email design

### Advanced Features
- ✅ **Search functionality** across all modules
- ✅ **Sorting** by multiple columns
- ✅ **Pagination** with query string preservation
- ✅ **Flash messages** for user feedback
- ✅ **Form validation** with detailed error messages
- ✅ **Responsive design** (mobile-friendly)
- ✅ **Clean URL structure** with proper routing

### Architecture & Code Quality
- ✅ **Repository Pattern** for data access abstraction
- ✅ **Form Request Validation** for input validation
- ✅ **API Resources** for consistent data serialization
- ✅ **Policies** for authorization logic
- ✅ **SOLID Principles** throughout the codebase
- ✅ **Type hints** and return types
- ✅ **Clean code** practices
- ✅ **No linter errors**

---

## 🛠️ Tech Stack

### Backend
- **Laravel 12** - PHP Framework
- **PHP 8.2+** - Programming Language
- **SQLite/MySQL** - Database (configurable)
- **Laravel Breeze** - Authentication scaffolding
- **Mailhog** - Email testing

### Frontend
- **Vue 3** - Progressive JavaScript Framework
- **Inertia.js** - Modern monolithic SPA framework
- **TailwindCSS 3** - Utility-first CSS framework
- **Vite** - Next-generation frontend tooling

### Development Tools
- **Docker** - Containerization
- **Composer** - PHP dependency manager
- **NPM** - JavaScript package manager
- **Laravel Sail** - Docker development environment

---

## 🚀 Quick Start

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js & NPM
- Docker



### 🔑 Default Test Credentials

**Admin User:**
- Email: `admin@minicrm.com`
- Password: `password`

**Company User (Acme Corporation):**
- Email: `manager@acmecorporation.com`
- Password: `password`

---

## 🐳 Docker Setup (Recommended)

Docker setup includes Laravel Sail, MySQL, phpMyAdmin, and Mailhog for complete local development.

### Start Docker Environment


```bash
# 1. Clone the repository
git clone https://github.com/TadasBaltru/mini-crm
cd mini-crm

# 2. Copy environment configuration

# Linux/Mac
cp .env.example .env

# 3. Install PHP dependencies via Docker
docker run --rm -v $PWD:/var/www/html -w /var/www/html laravelsail/php84-composer:latest composer install --ignore-platform-reqs

# 4. Start Docker containers
./vendor/bin/sail up -d

# 5. Generate application key
./vendor/bin/sail artisan key:generate

# 6. Run database migrations
./vendor/bin/sail artisan migrate

# 7. Seed sample data
./vendor/bin/sail artisan db:seed

# 8. Install frontend dependencies
./vendor/bin/sail npm install

# 9. Build frontend assets
./vendor/bin/sail npm run dev
```

### Docker Services

Once running, access:
- **Application:** http://localhost
- **Mailhog UI:** http://localhost:8025
- **phpMyAdmin:** http://localhost:8081
- **MySQL:** localhost:3306

### Docker Commands

```bash
# Start containers
docker-compose up -d

# Stop containers
docker-compose down

# View logs
docker-compose logs -f

# Access Laravel container
./vendor/bin/sail shell

# Run artisan commands
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan tinker
```


## 📧 Email Functionality

### Automatic Email Notifications

When a new employee is created, an email is automatically sent to the **company's email address**.

### Email Content Includes:
- Employee's full name
- Employee's email
- Employee's phone number
- Company name
- Date and time of creation
- Link to view employee details

### Testing Emails with Mailhog

1. Mailhog is automatically available at http://localhost:8025
2. Create a new employee in the application
3. Check Mailhog UI to see the email

---

## 📁 Project Structure

```
mini-crm/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── CompanyController.php
│   │   │   ├── EmployeeController.php
│   │   │   └── UserController.php
│   │   ├── Requests/              # Form validation
│   │   │   ├── CompanyStoreRequest.php
│   │   │   ├── EmployeeStoreRequest.php
│   │   │   └── UserStoreRequest.php
│   │   └── Resources/             # API resources
│   │       ├── CompanyResource.php
│   │       ├── EmployeeResource.php
│   │       └── UserResource.php
│   ├── Mail/
│   │   └── EmployeeCreated.php    # Email notification
│   ├── Models/
│   │   ├── Company.php
│   │   ├── Employee.php
│   │   └── User.php
│   ├── Policies/                  # Authorization
│   │   ├── CompanyPolicy.php
│   │   ├── EmployeePolicy.php
│   │   └── UserPolicy.php
│   └── Repositories/              # Data access layer
│       ├── CompanyRepository.php
│       ├── EmployeeRepository.php
│       ├── UserRepository.php
│       └── Contracts/             # Interfaces
├── database/
│   ├── factories/                 # Model factories
│   ├── migrations/                # Database schema
│   └── seeders/                   # Test data
├── resources/
│   ├── js/
│   │   ├── Components/            # Reusable Vue components
│   │   ├── Layouts/
│   │   │   ├── AuthenticatedLayout.vue
│   │   │   └── GuestLayout.vue
│   │   └── Pages/
│   │       ├── Auth/              # Login, Register, etc.
│   │       ├── Companies/         # Company CRUD
│   │       ├── Employees/         # Employee CRUD
│   │       ├── Profile/           # User profile
│   │       └── Users/             # User management
│   └── views/
│       ├── app.blade.php          # Main layout
│       └── emails/
│           └── employee-created.blade.php
├── routes/
│   ├── web.php                    # Web routes
│   └── auth.php                   # Auth routes
├── compose.yaml                   # Docker configuration
└── package.json                   # NPM dependencies
```

---

## 🔒 User Roles & Permissions

### Admin Role (`role = 'admin'`)

**Companies:**
- ✅ View all companies
- ✅ Create new companies
- ✅ Edit any company
- ✅ Delete any company
- ✅ Search and sort companies

**Employees:**
- ✅ View all employees across all companies
- ✅ Create employees for any company
- ✅ Edit any employee
- ✅ Delete any employee
- ✅ Search and sort employees

**Users:**
- ✅ View all users
- ✅ Create new users (admin or company users)
- ✅ Edit any user
- ✅ Delete any user
- ✅ Assign users to companies

### Company User Role (`role = 'company_user'`)

**Companies:**
- ✅ View their own company only
- ✅ Edit their own company information
- ❌ Cannot create new companies
- ❌ Cannot delete companies
- ❌ Cannot view other companies

**Employees:**
- ✅ View employees in their company only
- ✅ Create employees (auto-assigned to their company)
- ✅ Edit employees in their company
- ✅ Delete employees in their company
- ❌ Cannot access employees from other companies

**Users:**
- ❌ Cannot access user management

---

## 🧪 Testing

### Test the Application

```bash
# Reset database with fresh test data
php artisan migrate:fresh --seed

# Login with test credentials
Email: admin@minicrm.com
Password: password
```


### Code Quality
- ✅ Type hints and return types everywhere
- ✅ Comprehensive PHPDoc comments
- ✅ Consistent naming conventions (PSR-12)
- ✅ Dependency injection throughout
- ✅ Clean, readable code
- ✅ No linter errors

---

### Development

```bash
# Start servers
php artisan serve              # Laravel (http://localhost:8000)
npm run dev                    # Vite with HMR

# Database
php artisan migrate            # Run migrations
php artisan migrate:fresh      # Fresh migration
php artisan db:seed            # Seed database
php artisan migrate:fresh --seed

## 🌟 Credits

Built with:
- [Laravel](https://laravel.com) - PHP Framework
- [Vue.js](https://vuejs.org) - JavaScript Framework
- [Inertia.js](https://inertiajs.com) - Modern Monolith
- [TailwindCSS](https://tailwindcss.com) - CSS Framework
- [Laravel Breeze](https://laravel.com/docs/starter-kits) - Authentication

---
