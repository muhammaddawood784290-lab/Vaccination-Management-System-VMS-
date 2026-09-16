# Module Documentation
## Vaccination Management System (VMS)

---

## 1. Module Overview

| Module | Description | Controllers | Key Models |
|--------|-------------|-------------|------------|
| Authentication | Login, registration, password reset | AuthController | User |
| Admin Dashboard | System overview with KPIs | Admin\DashboardController | User, Child, Hospital, Vaccine, Appointment |
| Admin Children | View all children | Admin\ChildController | Child, VaccinationRecord |
| Admin Vaccines | Vaccine catalogue CRUD | Admin\VaccineController | Vaccine, VaccineInventory |
| Admin Hospitals | Hospital management + approval | Admin\HospitalController | Hospital, User |
| Admin Appointments | Appointment oversight | Admin\AppointmentController | Appointment |
| Admin Reports | Vaccination analytics | Admin\ReportController | VaccinationRecord, Appointment |
| Parent Dashboard | Family overview | Parent\DashboardController | Child, Appointment, VaccinationRecord |
| Parent Children | Child CRUD | Parent\ChildController | Child, VaccinationSchedule |
| Parent Hospitals | Hospital discovery | Parent\HospitalController | Hospital, VaccineInventory |
| Parent Appointments | Book/manage appointments | Parent\AppointmentController | Appointment |
| Parent Vaccinations | Schedule + history | Parent\VaccinationController | VaccinationSchedule, VaccinationRecord |
| Hospital Dashboard | Operational overview | Hospital\DashboardController | Appointment, VaccineInventory |
| Hospital Appointments | Process appointments | Hospital\AppointmentController | Appointment, VaccinationRecord |
| Hospital Inventory | Vaccine stock management | Hospital\VaccineInventoryController | VaccineInventory |
| Hospital Vaccinations | Vaccination records | Hospital\VaccinationController | VaccinationRecord |
| Notifications | All roles | *NotificationController | Notification |
| Profile | All roles | *ProfileController | User, Hospital |

---

## 2. Module Details

### 2.1 Authentication Module

**Controller:** `AuthController`
**Views:** `resources/views/auth/`
**Middleware:** `guest` (for login/register pages)

**Screens:**
| Screen | Route | Method | Description |
|--------|-------|--------|-------------|
| Login | GET /login | show | Login form with role selector |
| Login | POST /login | store | Authenticate and redirect by role |
| Register Parent | GET /register/parent | show | 2-step parent registration form |
| Register Parent | POST /register/parent | store | Create parent account |
| Register Hospital | GET /register/hospital | show | Hospital registration form |
| Register Hospital | POST /register/hospital | store | Submit hospital for approval |
| Forgot Password | GET /forgot-password | show | Email input form |
| Forgot Password | POST /forgot-password | store | Send reset link |
| Reset Password | GET /reset-password/{token} | show | New password form |
| Reset Password | POST /reset-password | store | Update password |
| Logout | POST /logout | destroy | End session |

**Validation:**
- Login: email (required, email), password (required)
- Register Parent: name, email, phone, city, password (min 8, confirmed), terms agreement
- Register Hospital: hospital_name, code, email, phone, city, state, address, contact_person, designation, password

---

### 2.2 Admin Dashboard Module

**Controller:** `Admin\DashboardController`
**View:** `admin/dashboard/index.blade.php`
**Middleware:** `auth`, `role:admin`

**Data Queried:**
- Total Children count
- Total Vaccinations count (completed records)
- Upcoming Appointments (status=confirmed, appointment_date >= today)
- Pending Requests count (if request system implemented)
- Monthly vaccination data (for chart)
- Appointment status distribution (for pie chart)
- Hospital overview (top 4)
- Recent activity feed

**KPI Cards:** Total Children, Total Vaccinations, Upcoming Appointments, Pending Requests

**Charts:** Monthly Vaccinations (BarChart), Appointment Status (PieChart)

**Widgets:** Hospital Overview, Pending Requests, Vaccine Usage, Recent Activity

---

### 2.3 Admin Children Module

**Controller:** `Admin\ChildController`
**Views:** `admin/children/index.blade.php`, `admin/children/show.blade.php`
**Middleware:** `auth`, `role:admin`

**Actions:**
| Action | Route | Method | Description |
|--------|-------|--------|-------------|
| Index | /admin/children | GET | List all children with search/filter |
| Show | /admin/children/{child} | GET | Child detail with vaccination history |

**Features:**
- Search by child name or parent name
- Gender filter (All, Male, Female)
- Pagination (10 per page)
- Vaccination progress bar per child
- Status badges (Active/Inactive)

---

### 2.4 Admin Vaccines Module

**Controller:** `Admin\VaccineController`
**Views:** `admin/vaccines/index.blade.php`, `admin/vaccines/create.blade.php`, `admin/vaccines/edit.blade.php`
**Middleware:** `auth`, `role:admin`

**Actions:**
| Action | Route | Method | Description |
|--------|-------|--------|-------------|
| Index | /admin/vaccines | GET | List all vaccines with search |
| Create | /admin/vaccines/create | GET | Vaccine creation form |
| Store | /admin/vaccines | POST | Create vaccine |
| Edit | /admin/vaccines/{vaccine}/edit | GET | Edit form |
| Update | /admin/vaccines/{vaccine} | PUT | Update vaccine |
| Destroy | /admin/vaccines/{vaccine} | DELETE | Delete vaccine |

**Fields:** name, code, doses (number), age_range, type (dropdown), manufacturer, description, status

---

### 2.5 Admin Hospitals Module

**Controller:** `Admin\HospitalController`
**Views:** `admin/hospitals/index.blade.php`, `admin/hospitals/create.blade.php`, `admin/hospitals/show.blade.php`, `admin/hospitals/edit.blade.php`
**Middleware:** `auth`, `role:admin`

**Actions:**
| Action | Route | Method | Description |
|--------|-------|--------|-------------|
| Index | /admin/hospitals | GET | List all hospitals with search/filter |
| Create | /admin/hospitals/create | GET | Hospital creation form |
| Store | /admin/hospitals | POST | Create hospital |
| Show | /admin/hospitals/{hospital} | GET | Hospital detail |
| Edit | /admin/hospitals/{hospital}/edit | GET | Edit form |
| Update | /admin/hospitals/{hospital} | PUT | Update hospital |
| Toggle Status | /admin/hospitals/{hospital}/toggle-status | POST | Activate/deactivate |
| Destroy | /admin/hospitals/{hospital} | DELETE | Delete hospital |

**Fields:** name, code, email, phone, city, state, total_beds, address, contact_person, designation, description, status

---

### 2.6 Admin Appointments Module

**Controller:** `Admin\AppointmentController`
**Views:** `admin/appointments/index.blade.php`, `admin/appointments/show.blade.php`
**Middleware:** `auth`, `role:admin`

**Actions:**
| Action | Route | Method | Description |
|--------|-------|--------|-------------|
| Index | /admin/appointments | GET | List all with status tabs |
| Show | /admin/appointments/{appointment} | GET | Appointment detail |
| Approve | /admin/appointments/{appointment}/approve | POST | Approve pending appointment |
| Reject | /admin/appointments/{appointment}/reject | POST | Reject with notes |
| Cancel | /admin/appointments/{appointment}/cancel | POST | Cancel appointment |

**Status Tabs:** All, Scheduled, Completed, Pending, Cancelled/No-show

---

### 2.7 Admin Reports Module

**Controller:** `Admin\ReportController`
**View:** `admin/reports/index.blade.php`
**Middleware:** `auth`, `role:admin`

**Data Displayed:**
- 4 Summary KPIs: Total Vaccinations, Total Appointments, Completed, Pending
- Completion Rate percentage
- Active Hospitals count
- Vaccines in Catalogue count
- Vaccination Trends line chart
- Vaccine Distribution horizontal bar chart
- Hospital Performance summary table
- Filterable vaccination records table (by date, vaccine, hospital)

---

### 2.8 Parent Dashboard Module

**Controller:** `Parent\DashboardController`
**View:** `parent/dashboard/index.blade.php`
**Middleware:** `auth`, `role:parent`

**Data Queried (own children only):**
- Children cards with vaccination progress
- Next upcoming appointment
- Recent vaccinations
- Quick action links

---

### 1.9 Admin Requests Module

**Purpose:** Admin reviews and manages parent-submitted requests (vaccination requests, inquiries, complaints, feedback).

**Middleware:** auth, role:admin

**Controller:** AdminRequestController

**Actions:**

| Action | HTTP Method | URI | Description |
|--------|-------------|-----|-------------|
| index | GET | /admin/requests | List all requests with status filter |
| show | GET | /admin/requests/{request} | View request details and respond |
| update | PUT | /admin/requests/{request} | Update status and add admin response |
| review | POST | /admin/requests/{request}/review | Mark as in_review |
| resolve | POST | /admin/requests/{request}/resolve | Mark as resolved with response |
| close | POST | /admin/requests/{request}/close | Mark as closed |

**Data Queried:**
- All parent_requests with user and hospital relationships
- Filter by status (pending, in_review, resolved, closed)
- Filter by type (vaccination_request, general_inquiry, complaint, feedback)
- Pagination (15 per page)

**Views:**
- admin/requests/index.blade.php - Request list with status filters
- admin/requests/show.blade.php - Request detail with response form

---

### 1.10 Admin Settings Module

**Purpose:** Admin manages notification preferences and security settings.

**Middleware:** auth, role:admin

**Controller:** AdminSettingsController

**Actions:**

| Action | HTTP Method | URI | Description |
|--------|-------------|-----|-------------|
| show | GET | /admin/settings | View settings page |
| updateNotifications | PUT | /admin/settings/notifications | Update notification preferences |
| updateSecurity | PUT | /admin/settings/security | Update security settings |
| updatePassword | PUT | /admin/settings/password | Change password |

**Views:**
- admin/settings/index.blade.php - Settings page with tabs (Notifications, Security, Password)

---

### 1.11 Parent Settings Module

**Purpose:** Parent manages notification preferences and password.

**Middleware:** auth, role:parent

**Controller:** ParentSettingsController

**Actions:**

| Action | HTTP Method | URI | Description |
|--------|-------------|-----|-------------|
| show | GET | /parent/settings | View settings page |
| updateNotifications | PUT | /parent/settings/notifications | Update notification preferences |
| updatePassword | PUT | /parent/settings/password | Change password |

**Views:**
- parent/settings/index.blade.php - Settings page with tabs (Notifications, Password)

---

### 1.12 Hospital Settings Module

**Purpose:** Hospital manages notification preferences and password.

**Middleware:** auth, role:hospital

**Controller:** HospitalSettingsController

**Actions:**

| Action | HTTP Method | URI | Description |
|--------|-------------|-----|-------------|
| show | GET | /hospital/settings | View settings page |
| updateNotifications | PUT | /hospital/settings/notifications | Update notification preferences |
| updatePassword | PUT | /hospital/settings/password | Change password |

**Views:**
- hospital/settings/index.blade.php - Settings page with tabs (Notifications, Password)

##