# Role & Permission Matrix
## Vaccination Management System (VMS)

---

## 1. Role Definitions

| Role | Description | Access Scope |
|------|-------------|-------------|
| Admin | System administrator | Full system-wide access |
| Parent | Parent/guardian | Own children and related data only |
| Hospital | Healthcare facility user | Own hospital operations only |

---

## 2. Permission Matrix

### 2.1 Authentication

| Action | Admin | Parent | Hospital |
|--------|:-----:|:------:|:--------:|
| Login | ✅ | ✅ | ✅ |
| Register (self) | ❌ | ✅ | ✅ (requires approval) |
| Forgot Password | ✅ | ✅ | ✅ |
| Logout | ✅ | ✅ | ✅ |

### 2.2 Dashboard

| Action | Admin | Parent | Hospital |
|--------|:-----:|:------:|:--------:|
| View Dashboard | ✅ System-wide | ✅ Own data only | ✅ Own hospital only |
| View KPIs | ✅ All children, vaccinations, appointments | ✅ Own children, appointments | ✅ Today's queue, stock |
| View Charts | ✅ All data | ❌ | ✅ Today's completion, hourly |

### 2.3 Children Management

| Action | Admin | Parent | Hospital |
|--------|:-----:|:------:|:--------:|
| View all children | ✅ | ❌ | ❌ |
| View own children | N/A | ✅ | ❌ |
| View child detail | ✅ Any child | ✅ Own children only | ❌ |
| Create child | ❌ (parent creates) | ✅ | ❌ |
| Edit child | ✅ Any child | ✅ Own children only | ❌ |
| Delete child | ✅ Any child | ✅ Own children only | ❌ |

### 2.4 Vaccine Management

| Action | Admin | Parent | Hospital |
|--------|:-----:|:------:|:--------:|
| View vaccine catalogue | ✅ | ✅ (browse only) | ✅ (browse only) |
| Create vaccine | ✅ | ❌ | ❌ |
| Edit vaccine | ✅ | ❌ | ❌ |
| Delete vaccine | ✅ | ❌ | ❌ |
| View vaccine inventory | ✅ All hospitals | ✅ Available at hospitals | ✅ Own hospital only |
| Update vaccine inventory | ❌ | ❌ | ✅ Own hospital only |

### 2.5 Hospital Management

| Action | Admin | Parent | Hospital |
|--------|:-----:|:------:|:--------:|
| View all hospitals | ✅ | ✅ (active only) | ❌ |
| View hospital detail | ✅ | ✅ | ✅ Own hospital |
| Create hospital | ✅ | ❌ | ❌ |
| Edit hospital | ✅ Any | ❌ | ✅ Own profile only |
| Approve hospital | ✅ | ❌ | ❌ |
| Deactivate hospital | ✅ | ❌ | ❌ |
| Delete hospital | ✅ | ❌ | ❌ |

### 2.6 Appointment Management

| Action | Admin | Parent | Hospital |
|--------|:-----:|:------:|:--------:|
| View all appointments | ✅ | ❌ | ❌ |
| View own appointments | N/A | ✅ | ✅ Own hospital only |
| Book appointment | ❌ | ✅ | ❌ |
| Approve appointment | ✅ | ❌ | ❌ |
| Reject appointment | ✅ | ❌ | ❌ |
| Cancel appointment | ✅ Any | ✅ Own (pending/approved) | ❌ |
| Confirm appointment | ❌ | ❌ | ✅ Own hospital |
| Complete vaccination | ❌ | ❌ | ✅ Own hospital |
| Mark no-show | ❌ | ❌ | ✅ Own hospital |

### 2.7 Vaccination Records

| Action | Admin | Parent | Hospital |
|--------|:-----:|:------:|:--------:|
| View all records | ✅ | ❌ | ❌ |
| View own records | N/A | ✅ (children's records) | ✅ (own hospital records) |
| Create record | ❌ | ❌ | ✅ (via workflow) |
| View record detail | ✅ Any | ✅ Own children's | ✅ Own hospital |

### 2.8 Vaccination Schedule

| Action | Admin | Parent | Hospital |
|--------|:-----:|:------:|:--------:|
| View schedule | ✅ | ✅ (own children) | ❌ |
| Manage schedule | ✅ | ❌ | ❌ |

### 2.9 Reports

| Action | Admin | Parent | Hospital |
|--------|:-----:|:------:|:--------:|
| View reports | ✅ | ❌ | ❌ |
| Export PDF | ✅ | ❌ | ❌ |

### 2.10 Notifications

| Action | Admin | Parent | Hospital |
|--------|:-----:|:------:|:--------:|
| View notifications | ✅ Own | ✅ Own | ✅ Own |
| Mark as read | ✅ | ✅ | ✅ |
| Mark all read | ✅ | ✅ | ✅ |

### 2.11 Profile

| Action | Admin | Parent | Hospital |
|--------|:-----:|:------:|:--------:|
| View profile | ✅ | ✅ | ✅ |
| Edit profile | ✅ | ✅ | ✅ Own hospital |
| Change password | ✅ | ✅ | ✅ |

---

## 3. Authorization Implementation

### 3.1 Middleware Stack

```
Route Group: /admin/*
  → middleware: ['web', 'auth', 'role:admin']

Route Group: /parent/*
  → middleware: ['web', 'auth', 'role:parent']

Route Group: /hospital/*
  → middleware: ['web', 'auth', 'role:hospital']
```

### 3.2 Policy Classes

| Policy | Resource | Key Rule |
|--------|----------|----------|
| ChildPolicy | Child | `$user->id === $child->user_id` (for parent role) |
| HospitalPolicy | Hospital | `$user->hospital_id === $hospital->id` (for hospital role) |
| AppointmentPolicy | Appointment | `$user->id === $appointment->parent_id` (parent) or `$user->hospital_id === $appointment->hospital_id` (hospital) |
| VaccinationRecordPolicy | VaccinationRecord | Same ownership as appointment |
| VaccinePolicy | Vaccine | Admin only for create/update/delete |
| VaccineInventoryPolicy | VaccineInventory | `$user->hospital_id === $inventory->hospital_id` |
| NotificationPolicy | Notification | `$user->id === $notification->user_id` |

### 3.3 Gates

| Gate | Purpose | Definition |
|------|---------|------------|
| view-admin-portal | Access admin routes | `$user->role === 'admin'` |
| view-parent-portal | Access parent routes | `$user->role === 'parent'` |
| view-hospital-portal | Access hospital routes | `$user->role === 'hospital'` |

### 3.4 Controller-Level Authorization

Every controller action that accesses a resource enforces ownership:

```php
// Parent accessing child
abort_unless($child->user_id === auth()->id(), 403);

// Hospital accessing appointment
abort_unless(
    $appointment->hospital_id === auth()->user()->hospital_id, 403
);
```

---

## 4. Post-Login Redirects

| Role | Redirect URL |
|------|-------------|
| Admin | `/admin/dashboard` |
| Parent | `/parent/dashboard` |
| Hospital | `/hospital/dashboard` |

---

*Document: Role & Permission Matrix v1.0 — Vaccination Management System*
