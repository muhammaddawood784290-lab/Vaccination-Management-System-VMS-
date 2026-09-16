# Route Architecture
## Vaccination Management System (VMS)

---

## 1. Route Organization

All routes are defined in `routes/web.php` and organized by role prefix.

### 1.1 Public Routes (No Auth Required)

| Method | URI | Controller | Action | Description |
|--------|-----|-----------|--------|-------------|
| GET | `/` | — | view welcome | Landing page |
| GET | `/login` | AuthController | show | Login form |
| POST | `/login` | AuthController | store | Process login |
| GET | `/register/parent` | AuthController | showRegisterParent | Parent registration |
| POST | `/register/parent` | AuthController | storeParent | Create parent account |
| GET | `/register/hospital` | AuthController | showRegisterHospital | Hospital registration |
| POST | `/register/hospital` | AuthController | storeHospital | Submit hospital registration |
| GET | `/forgot-password` | AuthController | showForgotPassword | Password reset request |
| POST | `/forgot-password` | AuthController | sendResetLink | Send reset email |
| GET | `/reset-password/{token}` | AuthController | showResetPassword | Reset form |
| POST | `/reset-password` | AuthController | resetPassword | Update password |

---

## 2. Admin Routes

All admin routes require `auth` + `role:admin` middleware.

### 2.1 Dashboard

| Method | URI | Controller | Action |
|--------|-----|-----------|--------|
| GET | `/admin/dashboard` | Admin\DashboardController | index |

### 2.2 Children

| Method | URI | Controller | Action |
|--------|-----|-----------|--------|
| GET | `/admin/children` | Admin\ChildController | index |
| GET | `/admin/children/{child}` | Admin\ChildController | show |

### 2.3 Vaccines

| Method | URI | Controller | Action |
|--------|-----|-----------|--------|
| GET | `/admin/vaccines` | Admin\VaccineController | index |
| GET | `/admin/vaccines/create` | Admin\VaccineController | create |
| POST | `/admin/vaccines` | Admin\VaccineController | store |
| GET | `/admin/vaccines/{vaccine}/edit` | Admin\VaccineController | edit |
| PUT | `/admin/vaccines/{vaccine}` | Admin\VaccineController | update |
| DELETE | `/admin/vaccines/{vaccine}` | Admin\VaccineController | destroy |

### 2.4 Hospitals

| Method | URI | Controller | Action |
|--------|-----|-----------|--------|
| GET | `/admin/hospitals` | Admin\HospitalController | index |
| GET | `/admin/hospitals/create` | Admin\HospitalController | create |
| POST | `/admin/hospitals` | Admin\HospitalController | store |
| GET | `/admin/hospitals/{hospital}` | Admin\HospitalController | show |
| GET | `/admin/hospitals/{hospital}/edit` | Admin\HospitalController | edit |
| PUT | `/admin/hospitals/{hospital}` | Admin\HospitalController | update |
| POST | `/admin/hospitals/{hospital}/toggle-status` | Admin\HospitalController | toggleStatus |
| DELETE | `/admin/hospitals/{hospital}` | Admin\HospitalController | destroy |

### 2.5 Appointments

| Method | URI | Controller | Action |
|--------|-----|-----------|--------|
| GET | `/admin/appointments` | Admin\AppointmentController | index |
| GET | `/admin/appointments/{appointment}` | Admin\AppointmentController | show |
| POST | `/admin/appointments/{appointment}/approve` | Admin\AppointmentController | approve |
| POST | `/admin/appointments/{appointment}/reject` | Admin\AppointmentController | reject |
| POST | `/admin/appointments/{appointment}/cancel` | Admin\AppointmentController | cancel |

### 2.6 Reports

| Method | URI | Controller | Action |
|--------|-----|-----------|--------|
| GET | `/admin/reports` | Admin\ReportController | index |


### 2.7 Admin Requests

| Method | URI | Controller | Action |
|--------|-----|-----------|--------|
| GET | /admin/requests | AdminRequestController | index |
| GET | /admin/requests/{request} | AdminRequestController | show |
| PUT | /admin/requests/{request} | AdminRequestController | update |
| POST | /admin/requests/{request}/review | AdminRequestController | review |
| POST | /admin/requests/{request}/resolve | AdminRequestController | resolve |
| POST | /admin/requests/{request}/close | AdminRequestController | close |
### 2.8 Profile

| Method | URI | Controller | Action |
|--------|-----|-----------|--------|
| GET | `/admin/profile` | Admin\ProfileController | show |
| PUT | `/admin/profile` | Admin\ProfileController | update |
| PUT | `/admin/profile/password` | Admin\ProfileController | updatePassword |

### 2.9 Notifications

| Method | URI | Controller | Action |
|--------|-----|-----------|--------|
| GET | `/admin/notifications` | Admin\NotificationController | index |
| POST | `/admin/notifications/{notification}/read` | Admin\NotificationController | markRead |
| POST | `/admin/notifications/read-all` | Admin\NotificationController | markAllRead |


### 2.10 Admin Settings

| Method | URI | Controller | Action |
|--------|-----|-----------|--------|
| GET | /admin/settings | AdminSettingsController | show |
| PUT | /admin/settings/notifications | AdminSettingsController | updateNotifications |
| PUT | /admin/settings/security | AdminSettingsController | updateSecurity |
| PUT | /admin/settings/password | AdminSettingsController | updatePassword |

**Admin Route Count: 36**

---

## 3. Parent Routes

All parent routes require `auth` + `role:parent` middleware.

### 3.1 Dashboard

| Method | URI | Controller | Action |
|--------|-----|-----------|--------|
| GET | `/parent/dashboard` | Parent\DashboardController | index |

### 3.2 Children

| Method | URI | Controller | Action |
|--------|-----|-----------|--------|
| GET | `/parent/children` | Parent\ChildController | index |
| GET | `/parent/children/create` | Parent\ChildController | create |
| POST | `/parent/children` | Parent\ChildController | store |
| GET | `/parent/children/{child}` | Parent\ChildController | show |
| GET | `/parent/children/{child}/edit` | Parent\ChildController | edit |
| PUT | `/parent/children/{child}` | Parent\ChildController | update |
| DELETE | `/parent/children/{child}` | Parent\ChildController | destroy |

### 3.3 Hospitals

| Method | URI | Controller | Action |
|--------|-----|-----------|--------|
| GET | `/parent/hospitals` | Parent\HospitalController | index |
| GET | `/parent/hospitals/{hospital}` | Parent\HospitalController | show |

### 3.4 Appointments

| Method | URI | Controller | Action |
|--------|-----|-----------|--------|
| GET | `/parent/appointments` | Parent\AppointmentController | index |
| GET | `/parent/appointments/create` | Parent\AppointmentController | create |
| POST | `/parent/appointments` | Parent\AppointmentController | store |
| GET | `/parent/appointments/{appointment}` | Parent\AppointmentController | show |
| POST | `/parent/appointments/{appointment}/cancel` | Parent\AppointmentController | cancel |

### 3.5 Vaccinations

| Method | URI | Controller | Action |
|--------|-----|-----------|--------|
| GET | `/parent/vaccinations/schedule` | Parent\VaccinationController | schedule |
| GET | `/parent/vaccinations/history` | Parent\VaccinationController | history |
| GET | `/parent/vaccinations/{record}` | Parent\VaccinationController | show |

### 3.6 Profile

| Method | URI | Controller | Action |
|--------|-----|-----------|--------|
| GET | `/parent/profile` | Parent\ProfileController | show |
| PUT | `/parent/profile` | Parent\ProfileController | update |
| PUT | `/parent/profile/password` | Parent\ProfileController | updatePassword |

### 3.7 Notifications

| Method | URI | Controller | Action |
|--------|-----|-----------|--------|
| GET | `/parent/notifications` | Parent\NotificationController | index |
| POST | `/parent/notifications/{notification}/read` | Parent\NotificationController | markRead |
| POST | `/parent/notifications/read-all` | Parent\NotificationController | markAllRead |


### 3.8 Parent Settings

| Method | URI | Controller | Action |
|--------|-----|-----------|--------|
| GET | /parent/settings | ParentSettingsController | show |
| PUT | /parent/settings/notifications | ParentSettingsController | updateNotifications |
| PUT | /parent/settings/password | ParentSettingsController | updatePassword |

**Parent Route Count: 27**

---

## 4. Hospital Routes

All hospital routes require `auth` + `role:hospital` middleware.

### 4.1 Dashboard

| Method | URI | Controller | Action |
|--------|-----|-----------|--------|
| GET | `/hospital/dashboard` | Hospital\DashboardController | index |

### 4.2 Appointments

| Method | URI | Controller | Action |
|--------|-----|-----------|--------|
| GET | `/hospital/appointments` | Hospital\AppointmentController | index |
| GET | `/hospital/appointments/{appointment}` | Hospital\AppointmentController | show |
| POST | `/hospital/appointments/{appointment}/confirm` | Hospital\AppointmentController | confirm |
| POST | `/hospital/appointments/{appointment}/complete` | Hospital\AppointmentController | complete |
| POST | /hospital/appointments/{appointment}/no-show | Hospital\AppointmentController | noShow |

### 4.3 Vaccine Inventory

| Method | URI | Controller | Action |
|--------|-----|-----------|--------|
| GET | `/hospital/vaccines` | Hospital\VaccineInventoryController | index |
| PUT | `/hospital/vaccines/{inventory}` | Hospital\VaccineInventoryController | update |

### 4.4 Vaccination Records

| Method | URI | Controller | Action |
|--------|-----|-----------|--------|
| GET | `/hospital/vaccinations` | Hospital\VaccinationController | index |
| GET | `/hospital/vaccinations/{record}` | Hospital\VaccinationController | show |

### 4.5 Profile

| Method | URI | Controller | Action |
|--------|-----|-----------|--------|
| GET | /hospital/profile | Hospital\ProfileController | show |
| PUT | /hospital/profile | Hospital\ProfileController | update |
| PUT | /hospital/profile/password | Hospital\ProfileController | updatePassword |

### 4.6 Notifications

| Method | URI | Controller | Action |
|--------|-----|-----------|--------|
| GET | /hospital/notifications | Hospital\NotificationController | index |
| POST | /hospital/notifications/{id}/read | Hospital\NotificationController | markRead |
| POST | /hospital/notifications/read-all | Hospital\NotificationController | markAllRead |

### 4.7 Hospital Settings

| Method | URI | Controller | Action |
|--------|-----|-----------|--------|
| GET | /hospital/settings | HospitalSettingsController | show |
| PUT | /hospital/settings/notifications | HospitalSettingsController | updateNotifications |
| PUT | /hospital/settings/password | HospitalSettingsController | updatePassword |

**Hospital Route Count: 18**