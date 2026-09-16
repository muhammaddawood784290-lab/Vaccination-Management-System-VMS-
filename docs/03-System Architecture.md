# System Architecture
## Vaccination Management System (VMS)

---

## 1. Architecture Overview

VMS follows the **Laravel MVC (Model-View-Controller)** pattern with role-based separation. A single application instance serves all three roles through shared backend logic, a single MySQL database, and role-specific routing/views.

```
┌─────────────────────────────────────────────────────┐
│                    Browser (Client)                   │
└──────────────────┬──────────────────────────────────┘
                   │ HTTP/HTTPS
┌──────────────────▼──────────────────────────────────┐
│              Laravel Application                     │
│  ┌────────────┐  ┌────────────┐  ┌────────────────┐ │
│  │ Admin Routes│  │Parent Routes│  │Hospital Routes │ │
│  └──────┬─────┘  └──────┬─────┘  └───────┬────────┘ │
│         │               │                │           │
│  ┌──────▼───────────────▼────────────────▼────────┐  │
│  │            Middleware Layer                     │  │
│  │  ┌─────────┐ ┌──────┐ ┌───────────────────┐   │  │
│  │  │  Auth   │ │ Role │ │  CSRF Verification│   │  │
│  │  └─────────┘ └──────┘ └───────────────────┘   │  │
│  └──────────────────┬────────────────────────────┘  │
│                     │                               │
│  ┌──────────────────▼────────────────────────────┐  │
│  │              Controllers                      │  │
│  │  ┌──────────┐ ┌────────┐ ┌────────────────┐  │  │
│  │  │ Admin/   │ │Parent/ │ │   Hospital/    │  │  │
│  │  │ 8 Ctrl   │ │7 Ctrl  │ │   6 Ctrl       │  │  │
│  │  └──────────┘ └────────┘ └────────────────┘  │  │
│  └──────────────────┬────────────────────────────┘  │
│                     │                               │
│  ┌──────────────────▼────────────────────────────┐  │
│  │         Models (Eloquent ORM)                 │  │
│  │  User, Hospital, Child, Vaccine,              │  │
│  │  VaccineInventory, VaccinationSchedule,       │  │
│  │  Appointment, VaccinationRecord, Notification │  │
│  └──────────────────┬────────────────────────────┘  │
│                     │                               │
│  ┌──────────────────▼────────────────────────────┐  │
│  │         Blade Views + Components              │  │
│  │  ┌───────┐ ┌────────┐ ┌──────────┐           │  │
│  │  │ admin/│ │parent/ │ │hospital/ │           │  │
│  │  │ 15    │ │ 15     │ │ 8 views  │           │  │
│  │  └───────┘ └────────┘ └──────────┘           │  │
│  └──────────────────────────────────────────────┘  │
└──────────────────┬──────────────────────────────────┘
                   │
┌──────────────────▼──────────────────────────────────┐
│                   MySQL Database                     │
│  9 application tables + 8 infrastructure tables      │
└─────────────────────────────────────────────────────┘
```

---

## 2. Layer Architecture

### 2.1 Presentation Layer (Blade Views)
- Role-specific view directories: `resources/views/admin/`, `resources/views/parent/`, `resources/views/hospital/`
- Reusable Blade components: `x-forms.input`, `x-forms.button`, `x-data.table`, `x-ui.kpi-card`, `x-ui.status-badge`, `x-ui.flash-alert`, `x-ui.secondary-button`
- Role-specific layouts extending a common base layout
- Tailwind CSS 4 for styling
- Charts via JavaScript charting library

### 2.2 Routing Layer
- All routes defined in `routes/web.php`
- Role prefixing: `/admin/*`, `/parent/*`, `/hospital/*`
- Middleware stack: `web`, `auth`, `role:admin|parent|hospital`
- Route model binding where applicable

### 2.3 Middleware Layer
| Middleware | Purpose |
|-----------|---------|
| `web` | Session, CSRF, cookie encryption |
| `auth` | Ensures authenticated user |
| `role:admin` | Ensures user role is admin |
| `role:parent` | Ensures user role is parent |
| `role:hospital` | Ensures user role is hospital |

### 2.4 Controller Layer
- Namespaced by role: `App\Http\Controllers\Admin\`, `App\Http\Controllers\Parent\`, `App\Http\Controllers\Hospital\`
- Controllers handle request validation, business logic orchestration, and view rendering
- Form Request classes for complex validation

### 2.5 Model Layer (Eloquent)
- All models in `App\Models\`
- Mass-assignment protection via `$fillable`
- Relationships defined on models
- Scopes for common queries (e.g., `forUser()`, `forHospital()`)

### 2.6 Authorization Layer
| Component | Implementation |
|-----------|---------------|
| Role Middleware | Route-level role checking |
| Policies | Resource-level authorization (ChildPolicy, HospitalPolicy, AppointmentPolicy, VaccinationRecordPolicy) |
| Gates | System-level permission checks |

### 2.7 Data Layer (MySQL)
- Normalized relational database
- Foreign key constraints
- Indexes on frequently queried columns
- Status enums for workflow states

---

## 3. Application Flow

### 3.1 Request Lifecycle

```
HTTP Request
  → public/index.php
    → Laravel Kernel
      → Middleware (web, auth, role)
        → Route matching
          → Controller action
            → Eloquent queries (Model)
            → Form validation (Request)
            → Authorization check (Policy/Gate)
            → Response (Blade view or redirect)
```

### 3.2 Role-Based Routing Flow

```
User visits /admin/dashboard
  → Route matches: /admin/*
  → auth middleware: Is user logged in?
    → No: Redirect to /login
    → Yes: role:admin middleware
      → Is user role = admin?
        → No: Abort 403
        → Yes: Admin\DashboardController@index
          → Query database for KPIs
          → Render admin/dashboard/index.blade.php
```

---

## 4. File Structure

```
VMS/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── ChildController.php
│   │   │   │   ├── HospitalController.php
│   │   │   │   ├── VaccineController.php
│   │   │   │   ├── AppointmentController.php
│   │   │   │   ├── ReportController.php
│   │   │   │   ├── ProfileController.php
│   │   │   │   └── NotificationController.php
│   │   │   ├── Parent/
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── ChildController.php
│   │   │   │   ├── HospitalController.php
│   │   │   │   ├── AppointmentController.php
│   │   │   │   ├── VaccinationController.php
│   │   │   │   ├── ProfileController.php
│   │   │   │   └── NotificationController.php
│   │   │   └── Hospital/
│   │   │       ├── DashboardController.php
│   │   │       ├── AppointmentController.php
│   │   │       ├── VaccineInventoryController.php
│   │   │       ├── VaccinationController.php
│   │   │       ├── ProfileController.php
│   │   │       └── NotificationController.php
│   │   ├── Middleware/
│   │   │   └── RoleMiddleware.php
│   │   └── Requests/
│   │       ├── StoreChildRequest.php
│   │       ├── StoreAppointmentRequest.php
│   │       ├── StoreHospitalRequest.php
│   │       ├── StoreVaccineRequest.php
│   │       └── UpdateProfileRequest.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Hospital.php
│   │   ├── Child.php
│   │   ├── Vaccine.php
│   │   ├── VaccineInventory.php
│   │   ├── VaccinationSchedule.php
│   │   ├── Appointment.php
│   │   ├── VaccinationRecord.php
│   │   └── Notification.php
│   └── Policies/
│       ├── ChildPolicy.php
│       ├── HospitalPolicy.php
│       ├── AppointmentPolicy.php
│       ├── VaccinationRecordPolicy.php
│       ├── VaccinePolicy.php
│       ├── VaccineInventoryPolicy.php
│       └── NotificationPolicy.php
├── database/
│   ├── migrations/     (11 migration files)
│   ├── seeders/        (6 seeders)
│   └── factories/      (9 factories)
├── resources/
│   └── views/
│       ├── admin/      (15 views)
│       ├── parent/     (15 views)
│       ├── hospital/   (8 views)
│       ├── auth/       (5 views)
│       ├── components/ (7 components)
│       └── layouts/    (4 layouts)
├── routes/
│   └── web.php
├── config/
├── public/
└── docs/
```

---

## 5. Technology Decisions

| Decision | Choice | Rationale |
|----------|--------|-----------|
| Backend Framework | Laravel 12 | Approved by requirements, mature ecosystem |
| Frontend Templating | Blade | Server-side rendering, no SPA complexity |
| CSS Fra
