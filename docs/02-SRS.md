# Software Requirements Specification (SRS)
## Vaccination Management System (VMS)

**Version:** 1.0
**Date:** September 2026
**Project Type:** Aptech eProject

---

## 1. Introduction

### 1.1 Purpose
This document specifies the complete functional and non-functional requirements for the Vaccination Management System (VMS). It serves as the primary reference for development, testing, and validation.

### 1.2 Scope
VMS is a single Laravel application serving three roles (Admin, Parent, Hospital) through a shared backend and MySQL database. Each role has dedicated views, navigation, dashboards, and workflows while sharing business logic and data.

### 1.3 Definitions

| Term | Definition |
|------|-----------|
| Admin | System administrator with full access |
| Parent | Registered parent/guardian managing children's vaccinations |
| Hospital | Approved healthcare facility administering vaccinations |
| Appointment | A scheduled vaccination session |
| Vaccination Record | A completed vaccination with clinical details |
| Schedule | The recommended vaccination timeline for a child |
| Request | A parent-submitted request requiring admin review |

---

## 2. Overall Description

### 2.1 Product Perspective
- Single Laravel 12 MVC application
- Shared MySQL database
- Role-specific Blade views and routes
- One backend, one codebase, three portal experiences

### 2.2 User Classes

| Role | Description | Primary Goal |
|------|-------------|-------------|
| Admin | System manager | Manage system, approve hospitals, oversee vaccination |
| Parent | Parent/guardian | Register children, book appointments, track vaccination history |
| Hospital | Healthcare facility | Process appointments, administer vaccinations, manage inventory |

### 2.3 Operating Environment
- Backend: Laravel 12 + PHP 8.3+
- Frontend: Blade + Tailwind CSS 4
- Database: MySQL 8.0+
- Server: Hostinger (Apache/Nginx)
- Browser: Modern browsers (Chrome, Firefox, Safari, Edge)

---

## 3. Functional Requirements

### 3.1 Authentication Module [FR-AUTH]

| ID | Requirement | Priority |
|----|-------------|----------|
| FR-AUTH-001 | Users can log in with email and password | Required |
| FR-AUTH-002 | Post-login redirect by role: Admin→/admin/dashboard, Parent→/parent/dashboard, Hospital→/hospital/dashboard | Required |
| FR-AUTH-003 | Parents can self-register with personal details and password (2-step form) | Required |
| FR-AUTH-004 | Hospitals can submit registration requiring admin approval | Required |
| FR-AUTH-005 | Password reset via email link | Required |
| FR-AUTH-006 | Logout destroys session | Required |
| FR-AUTH-007 | Unauthenticated users redirected to login | Required |
| FR-AUTH-008 | Users cannot access another role's portal | Required |
| FR-AUTH-009 | Show generic "credentials do not match" error (no user enumeration) | Required |

### 3.2 Admin Module [FR-ADMIN]

| ID | Requirement | Priority |
|----|-------------|----------|
| FR-ADMIN-001 | System dashboard with KPIs: Total Children, Total Vaccinations, Upcoming Appointments, Pending Requests | Required |
| FR-ADMIN-002 | Dashboard charts: Monthly Vaccination Trends (bar), Appointment Status Distribution (pie), Vaccine Usage (progress bars) | Required |
| FR-ADMIN-003 | Dashboard widgets: Hospital Overview, Pending Requests, Vaccine Usage, Recent Activity feed | Required |
| FR-ADMIN-004 | Child Management: View all children with search, gender filter, vaccination progress, status | Required |
| FR-ADMIN-005 | Child Details: Personal info, parent info, vaccination progress bar | Required |
| FR-ADMIN-006 | Vaccine Management: Full CRUD with name, code, doses, age range, type, manufacturer, description, status | Required |
| FR-ADMIN-007 | Vaccine summary cards: Total, Active, Inactive counts | Required |
| FR-ADMIN-008 | Hospital Management: Full CRUD with name, code, email, phone, city, state, beds, address, contact, status | Required |
| FR-ADMIN-009 | Hospital approval workflow: view pending, approve, reject | Required |
| FR-ADMIN-010 | Hospital summary cards: Total, Active, Pending Approval | Required |
| FR-ADMIN-011 | Appointment Management: View all appointments with status tabs (All, Scheduled, Completed, Pending, Cancelled/No-show) | Required |
| FR-ADMIN-012 | Appointment summary cards: Total, Scheduled, Completed, No-shows | Required |
| FR-ADMIN-013 | Parent Requests: View requests with tabs (Pending, Approved, Rejected), approve/reject with notes | Required |
| FR-ADMIN-014 | Reports: Vaccination Trends chart, Vaccine Distribution chart, Hospital Performance summary table, Export PDF button | Required |
| FR-ADMIN-015 | Notification center with mark-as-read, unread indicators, type badges | Required |
| FR-ADMIN-016 | Admin profile management (name, email, phone, password) | Required |
| FR-ADMIN-017 | Settings page with notification toggles and security options | Required |

### 3.3 Parent Module [FR-PARENT]

| ID | Requirement | Priority |
|----|-------------|----------|
| FR-PARENT-001 | Dashboard with child cards showing vaccination progress, next appointment, recent vaccinations, quick actions | Required |
| FR-PARENT-002 | My Children: Card grid with completed/scheduled/overdue dose counts | Required |
| FR-PARENT-003 | Add Child: Form with name, DOB, gender, blood group, relationship, allergies, notes | Required |
| FR-PARENT-004 | Child Detail: Personal info, vaccination history table, edit/delete buttons | Required |
| FR-PARENT-005 | Vaccination Schedule: Per-child schedule showing vaccine, dose, target age, due date, status badges | Required |
| FR-PARENT-006 | Find Hospitals: Search by name, filter by city, hospital cards with vaccine count and rating | Required |
| FR-PARENT-007 | Hospital Detail: Info, available vaccines with status badges, book appointment link | Required |
| FR-PARENT-008 | Book Appointment: Select child, vaccine, hospital, date/time, notes — submit creates pending appointment | Required |
| FR-PARENT-009 | Appointments: View own appointments with status filter, cancel pending/approved appointments | Required |
| FR-PARENT-010 | Vaccination History: View completed vaccination records with child and vaccine filters | Required |
| FR-PARENT-011 | Vaccination Record Detail: Full record with batch number, administered by, side effects, next due | Required |
| FR-PARENT-012 | Notification center with mark-as-read | Required |
| FR-PARENT-013 | Profile management (name, email, phone, password) | Required |
| FR-PARENT-014 | **CRITICAL**: Parent can ONLY access own children, appointments, and vaccination records | Required |

### 3.4 Hospital Module [FR-HOSPITAL]

| ID | Requirement | Priority |
|----|-------------|----------|
| FR-HOSPITAL-001 | Dashboard: Today's Appointments, Pending Vaccinations, Completed Today, Vaccine Types Available | Required |
| FR-HOSPITAL-002 | Dashboard: Today's Appointment Queue (clickable), Today's Completion pie chart, Appointments by Hour bar chart, Vaccine Stock Alerts | Required |
| FR-HOSPITAL-003 | Appointments: View own hospital appointments with tabs (Today, Upcoming, Completed, All) | Required |
| FR-HOSPITAL-004 | 4-step Vaccination Workflow: Step 1 Verify Patient Identity, Step 2 Verify Vaccine (batch number), Step 3 Administer (checklist), Step 4 Record (administered by, batch, next due, side effects, notes) | Required |
| FR-HOSPITAL-005 | Workflow allows marking appointment as No-show with reason | Required |
| FR-HOSPITAL-006 | Vaccination completion creates VaccinationRecord, updates vaccine inventory | Required |
| FR-HOSPITAL-007 | Vaccine Inventory: View stock levels with progress bars, low-stock alerts, update stock modal | Required |
| FR-HOSPITAL-008 | Vaccination Records: View own hospital's completed vaccination records with search | Required |
| FR-HOSPITAL-009 | Record Detail: Full vaccination record with all clinical details | Required |
| FR-HOSPITAL-010 | Hospital Profile: Edit name, email, phone, address, contact person, beds | Required |
| FR-HOSPITAL-011 | Settings: Email notification toggle, low stock alert toggle, change password | Required |
| FR-HOSPITAL-0
