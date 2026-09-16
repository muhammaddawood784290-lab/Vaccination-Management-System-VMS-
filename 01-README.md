# Vaccination Management System (VMS)

> A centralized Laravel application connecting parents, hospitals, and administrators to ensure every child receives timely, complete vaccination care.

---

## Project Overview

The Vaccination Management System (VMS) is a role-based web application built with Laravel 12, PHP 8.3+, MySQL, Blade templating, and Tailwind CSS. It serves three user roles — **Admin**, **Parent**, and **Hospital** — through a single shared backend with role-specific dashboards, navigation, and workflows.

### Key Statistics

| Metric | Value |
|--------|-------|
| Children Registered | 12,400+ |
| Partner Hospitals | 48 |
| Vaccines in Catalogue | 10+ |
| Schedule Compliance | 98% |

---

## Main Features

### Authentication & Registration
- Role-based login (Admin, Parent, Hospital)
- Parent self-registration (2-step form)
- Hospital registration with admin approval workflow
- Password reset via email
- Session management and CSRF protection

### Admin Portal
- System-wide dashboard with KPIs, charts, and activity feed
- Child management (view, search, filter)
- Vaccine catalogue CRUD
- Hospital management with approval workflow
- Appointment oversight and status management
- Parent request review (approve/reject)
- Vaccination reports and analytics
- Notification center

### Parent Portal
- Family dashboard with child cards and progress
- Child registration and management
- Vaccination schedule with due dates
- Hospital discovery (search, filter by city)
- Appointment booking (child, vaccine, hospital, date/time)
- Vaccination history with records
- Notification center

### Hospital Portal
- Operational dashboard with today's queue
- Appointment management with processing workflow
- 4-step vaccination workflow (Verify Patient, Verify Vaccine, Administer, Record)
- Vaccine inventory management with stock alerts
- Vaccination records and history
- Hospital profile management

---

## Roles

| Role | Access Level | Portal |
|------|-------------|--------|
| Admin | System-wide management | `/admin/*` |
| Parent | Own children and appointments only | `/parent/*` |
| Hospital | Own hospital operations only | `/hospital/*` |

---

## Architecture

- **Single Laravel 12 application** with shared backend, business logic, and MySQL database
- **Role-specific routing** via middleware (`/admin/*`, `/parent/*`, `/hospital/*`)
- **Laravel MVC** with Controllers, Models (Eloquent), Blade Views, Form Requests, Policies
- **Authorization** via Role Middleware + Policies + Gates
- **No duplicate backends** — all roles share models and database

---

## Technology Stack

| Layer | Technology |
|-------|-----------|
| Backend | Laravel 12, PHP 8.3+ |
| Frontend | Blade, HTML5, CSS3, JavaScript |
| Styling | Tailwind CSS 4 |
| UI Components | Blade Components |
| Database | MySQL 8.0+ |
| ORM | Laravel Eloquent |
| Authentication | Laravel Auth (Bcrypt) |
| Authorization | Role Middleware + Policies/Gates |
| Build Tool | Vite 7 |
| Version Control | Git/GitHub |
| Hosting | Hostinger |
| Production Domain | vms.permetheon.com |

---

## Requirements

### Minimum Requirements
- PHP 8.2+
- MySQL 8.0+ or MariaDB 10.4+
- Composer 2.x
- Node.js 18+ and npm
- Web server with PHP support (Apache/Nginx)

### Extensions
- pdo_mysql, mbstring, openssl, curl, gd or imagick, xml

---

## Installation

```bash
git clone <repository-url>
cd vms
composer install
npm install
cp .env.example .env
php artisan key:generate
# Configure DB in .env, then:
php artisan migrate --force
php artisan db:seed
php artisan storage:link
npm run build
php artisan serve
```

Access at `http://localhost:8000`

---

## Environment Configuration

| Variable | Development | Production |
|----------|-------------|------------|
| `APP_ENV` | `local` | `production` |
| `APP_DEBUG` | `true` | `false` |
| `APP_URL` | `http://localhost:8000` | `https://vms.permetheon.com` |
| `DB_CONNECTION` | `mysql` | `mysql` |
| `SESSION_DRIVER` | `database` | `database` |
| `SESSION_SECURE_COOKIE` | `false` | `true` |

---

## Testing

```bash
php artisan test
```

---

## Documentation Index

| # | Document | Description |
|---|----------|-------------|
| 01 | README | Project overview |
| 02 | SRS | Software Requirements Specification |
| 03 | System Architecture | Application architecture |
| 04 | Database Design | Complete database schema |
| 05 | ER Diagram Specification | Entity-relationship diagram |
| 06 | Module Documentation | Module specifications |
| 07 | Role & Permission Matrix | Access control matrix |
| 08 | Route Architecture | Route definitions |
| 09 | Algorithms & Workflows | Business logic |
| 10 | GUI Standards | Design system |
| 11 | Interface Design Document | Screen specifications |
| 12 | Project Plan | Timeline and milestones |
| 13 | Task Sheet | Task breakdown |
| 14 | Testing Strategy | Test approach |
| 15 | Unit Testing Checklist | Unit test cases |
| 16 | QA Checklist | QA checklist |
| 17 | Security Requirements | Security specs |
| 18 | Deployment Documentation | Deployment guide |
| 19 | Project Review & Monitoring Report | Review templates |
| 20 | Final Project Report Structure | Report guide |
| 21 | Requirements Analysis & Decisions | Conflict resolution |
| 22 | CREDENTIALS | Development credentials |

---

*Generated for the Vaccination Management System — Aptech eProject*
