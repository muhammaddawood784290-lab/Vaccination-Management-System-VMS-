# Testing Strategy
## Vaccination Management System (VMS)

---

## 1. Testing Approach

VMS employs a multi-layered testing strategy aligned with Laravel's testing tools (PHPUnit/Pest).

### 1.1 Test Levels

| Level | Tool | Coverage |
|-------|------|---------|
| Unit Tests | PHPUnit | Models, helpers, business logic |
| Feature Tests | PHPUnit | HTTP requests, controllers, views |
| Integration Tests | PHPUnit + Tinker | Cross-module workflows |
| Manual Tests | Browser | UI/UX, visual regression |

---

## 2. Test Categories

### 2.1 Authentication Testing

| Test | Description |
|------|-------------|
| Auth-001 | Valid login redirects to correct dashboard by role |
| Auth-002 | Invalid login shows generic error |
| Auth-003 | Logout destroys session |
| Auth-004 | Unauthenticated access redirects to login |
| Auth-005 | Parent registration creates account with role=parent |
| Auth-006 | Hospital registration creates account with role=hospital, hospital.status=pending |
| Auth-007 | Password reset sends email and allows reset |
| Auth-008 | Post-login redirect: admin→/admin/dashboard, parent→/parent/dashboard, hospital→/hospital/dashboard |

### 2.2 Authorization Testing

| Test | Description |
|------|-------------|
| Authz-001 | Parent cannot access /admin/* routes |
| Authz-002 | Hospital cannot access /admin/* routes |
| Authz-003 | Admin cannot access /parent/* routes |
| Authz-004 | Parent cannot view another parent's children |
| Authz-005 | Hospital cannot view another hospital's appointments |
| Authz-006 | Parent cannot approve/reject appointments |
| Authz-007 | Hospital cannot manage vaccine catalogue |
| Authz-008 | Cross-role API calls return 403 |

### 2.3 CRUD Testing

| Module | Tests |
|--------|-------|
| Children | Create, Read, Update, Delete (parent only for own) |
| Vaccines | Create, Read, Update, Delete (admin only) |
| Hospitals | Create, Read, Update, Delete, Toggle Status (admin only) |
| Appointments | Create, Read, Cancel, Approve, Reject, Confirm, Complete |
| Vaccination Records | Create (hospital workflow), Read (admin/parent/hospital) |
| Profile | Update name/email, Change password |

### 2.4 Workflow Testing

| Workflow | Steps to Test |
|----------|--------------|
| Parent Journey | Register → Login → Add Child → Book Appointment → Track → View History |
| Admin Journey | Login → Dashboard → Manage → Review → Reports |
| Hospital Journey | Login → Dashboard → View Appointments → Process Workflow → Complete → Record |

### 2.5 State Transition Testing

| Transition | Expected Result |
|------------|----------------|
| pending → approved | Status updates, parent notified |
| pending → rejected | Status updates, parent notified |
| approved → confirmed | Hospital confirms |
| confirmed → completed | VaccinationRecord created, inventory decremented |
| confirmed → no_show | Status updates, parent notified |
| pending → completed | BLOCKED (invalid) |
| completed → cancelled | BLOCKED (invalid) |

### 2.6 Validation Testing

Every form must test:
- Required field validation
- Email format validation
- Password strength validation
- Date format validation
- Numeric field validation
- Max length constraints
- Unique field constraints

### 2.7 Security Testing

| Test | Description |
|------|-------------|
| Sec-001 | CSRF token required on all POST/PUT/DELETE |
| Sec-002 | XSS prevention (no unescaped output) |
| Sec-003 | SQL injection prevention (Eloquent parameterized queries) |
| Sec-004 | Mass assignment protection on all models |
| Sec-005 | Passwords stored as bcrypt hashes |
| Sec-006 | Session security (HTTP-only, Secure cookies) |
| Sec-007 | Input sanitization on all user inputs |

### 2.8 Database Testing

| Test | Description |
|------|-------------|
| DB-001 | All migrations run without errors |
| DB-002 | Rollback works cleanly |
| DB-003 | Foreign key constraints enforced |
| DB-004 | Unique constraints enforced |
| DB-005 | Cascade deletes work correctly |
| DB-006 | Seeders produce valid data |

---

## 3. Test Execution

### 3.1 Commands

```bash
php artisan test                          # All tests
php artisan test --filter=Auth            # Auth tests
php artisan test --filter=Admin           # Admin tests
php artisan test --filter=Parent          # Parent tests
php artisan test --filter=Hospital        # Hospital tests
php artisan test --filter=Appointment     # Appointment tests
php artisan test --filter=Security        # Security tests
```

### 3.2 CI/CD Integration

Tests should run automatically on:
- Every git push
- Before deployment
- On pull request creation

---

## 4. Test Data

Use model factories for all test data:
- UserFactory: role parameter for admin/parent/hospital
- ChildFactory: linked to parent user
- HospitalFactory: with status parameter
- VaccineFactory: with status parameter
- AppointmentFactory: with status parameter
- VaccinationRecordFactory: with clinical details

---

*Document: Testing Strategy v1.0 — Vaccination Management System*
