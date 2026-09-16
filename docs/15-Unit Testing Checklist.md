# Unit Testing Checklist
## Vaccination Management System (VMS)

---

## Model Tests

| ID | Model | Test | Expected |
|----|-------|------|----------|
| UT-001 | User | Create user with role=admin | role = 'admin' |
| UT-002 | User | Create user with role=parent | role = 'parent' |
| UT-003 | User | Create user with role=hospital | role = 'hospital' |
| UT-004 | User | Password is hashed (not plaintext) | Hash::check returns true |
| UT-005 | User | hasChildren relationship (parent) | Returns child collection |
| UT-006 | User | hasMany appointments relationship | Returns appointment collection |
| UT-007 | User | belongsTo hospital relationship | Returns hospital model |
| UT-008 | User | $fillable includes name, email, password, role | Mass assignment works |
| UT-009 | User | $hidden includes password, remember_token | Hidden from serialization |
| UT-010 | Hospital | Create hospital with status=pending | status = 'pending' |
| UT-011 | Hospital | Toggle status active→inactive | status = 'inactive' |
| UT-012 | Hospital | hasMany users relationship | Returns user collection |
| UT-013 | Hospital | hasMany vaccineInventory | Returns inventory collection |
| UT-014 | Child | Create child linked to parent user | user_id matches parent |
| UT-015 | Child | belongsTo user (parent) | Returns parent user |
| UT-016 | Child | hasMany appointments | Returns appointment collection |
| UT-017 | Child | hasMany vaccinationRecords | Returns record collection |
| UT-018 | Child | age calculation from date_of_birth | Returns correct age |
| UT-019 | Vaccine | Create vaccine with unique code | Code is unique |
| UT-020 | Vaccine | hasMany vaccineInventory | Returns inventory collection |
| UT-021 | Vaccine | hasMany vaccinationSchedules | Returns schedule collection |
| UT-022 | VaccineInventory | belongsTo hospital | Returns hospital model |
| UT-023 | VaccineInventory | belongsTo vaccine | Returns vaccine model |
| UT-024 | VaccineInventory | Unique constraint (hospital_id, vaccine_id) | Rejects duplicate |
| UT-025 | VaccinationSchedule | belongsTo child | Returns child model |
| UT-026 | VaccinationSchedule | belongsTo vaccine | Returns vaccine model |
| UT-027 | VaccinationSchedule | Status transitions (due→completed) | Status updates |
| UT-028 | Appointment | belongsTo child, hospital, vaccine, parent | All relationships valid |
| UT-029 | Appointment | Status transitions: pending→approved→confirmed→completed | Each transition works |
| UT-030 | Appointment | Invalid transition blocked: pending→completed | Throws exception |
| UT-031 | VaccinationRecord | belongsTo child, hospital, vaccine | All relationships valid |
| UT-032 | VaccinationRecord | Optional appointment_id (nullable FK) | Can be null |
| UT-033 | Notification | forUser scope returns only user's notifications | Filtered correctly |
| UT-034 | Notification | unread scope returns unread only | read_at IS NULL |

---

## Business Logic Tests

| ID | Test | Expected |
|----|------|----------|
| UT-035 | Dashboard KPI: Total Children count | Correct count |
| UT-036 | Dashboard KPI: Total Vaccinations count | Correct count (status=done) |
| UT-037 | Dashboard KPI: Upcoming Appointments | Future dates, pending/approved |
| UT-038 | Dashboard KPI: Pending Requests | status=pending |
| UT-039 | Hospital Dashboard: Today's Appointments | Matches today's date |
| UT-040 | Vaccination completion: Record created | All fields populated |
| UT-041 | Vaccination completion: Appointment status updated | status = 'completed' |
| UT-042 | Vaccination completion: Schedule updated | Schedule status = 'completed' |
| UT-043 | Vaccination completion: Inventory decremented | available -= 1 |
| UT-044 | Low stock alert: available < 30% of capacity | Alert triggered |
| UT-045 | Parent registration: Creates User + role=parent | User created |
| UT-046 | Hospital registration: Creates User + Hospital | Both created, status=pending |
| UT-047 | Search: Filter children by name | Returns matching |
| UT-048 | Search: Filter hospitals by city | Returns matching |
| UT-049 | Pagination: 10 items per page | Correct page size |

---

## Policy Tests

| ID | Policy | Test | Expected |
|----|--------|------|----------|
| UT-050 | ChildPolicy | Parent can view own child | Allowed |
| UT-051 | ChildPolicy | Parent cannot view other's child | Denied |
| UT-052 | ChildPolicy | Admin can view any child | Allowed |
| UT-053 | HospitalPolicy | Hospital can view own profile | Allowed |
| UT-054 | HospitalPolicy | Hospital cannot view other's profile | Denied |
| UT-055 | AppointmentPolicy | Parent can view own appointment | Allowed |
| UT-056 | AppointmentPolicy | Hospital can view hospital's appointment | Allowed |
| UT-057 | AppointmentPolicy | Parent cannot view other's appointment | Denied |
| UT-058 | NotificationPolicy | User can view own notification | Allowed |
| UT-059 | NotificationPolicy | User cannot view other's notification | Denied |

---

*Document: Unit Testing Checklist v1.0 — Vaccination Management System*

| TC-REQ-01 | AdminRequestController | Admin can list all parent requests | Admin authenticated | GET /admin/requests | List of requests | PASS | High |
| TC-REQ-02 | AdminRequestController | Admin can view request detail | Admin authenticated, request exists | GET /admin/requests/{id} | Request detail page | PASS | High |
| TC-REQ-03 | AdminRequestController | Admin can review request (pending->in_review) | Admin authenticated, request pending | POST /admin/requests/{id}/review | Status updated to in_review | PASS | High |
| TC-REQ-04 | AdminRequestController | Admin can resolve request with response | Admin authenticated, request in_review | POST /admin/requests/{id}/resolve | Status updated to resolved, admin_response saved | PASS | High |
| TC-REQ-05 | AdminRequestController | Admin can close request | Admin authenticated, request resolved | POST /admin/requests/{id}/close | Status updated to closed | PASS | Medium |
| TC-SET-01 | AdminSettingsController | Admin can view settings page | Admin authenticated | GET /admin/settings | Settings page rendered | PASS | Medium |
| TC-SET-02 | AdminSettingsController | Admin can update notification prefs | Admin authenticated | PUT /admin/settings/notifications | Prefs saved | PASS | Medium |
| TC-SET-03 | AdminSettingsController | Admin can change password | Admin authenticated, valid current password | PUT /admin/settings/password | Password updated | PASS | Medium |
| TC-SET-04 | ParentSettingsController | Parent can view settings page | Parent authenticated | GET /parent/settings | Settings page rendered | PASS | Medium |
| TC-SET-05 | ParentSettingsController | Parent can change password | Parent authenticated, valid current password | PUT /parent/settings/password | Password updated | PASS | Medium |
| TC-SET-06 | HospitalSettingsController | Hospital can view settings page | Hospital authenticated | GET /hospital/settings | Settings page rendered | PASS | Medium |
| TC-SET-07 | HospitalSettingsController | Hospital can change password | Hospital authenticated, valid current password | PUT /hospital/settings/password | Password updated | PASS | Medium |
| TC-NS-01 | HospitalAppointmentController | Hospital can mark appointment no-show | Hospital authenticated, appointment confirmed | POST /hospital/appointments/{id}/no-show | Status updated to no_show | PASS | High |
| TC-NS-02 | HospitalAppointmentController | No-show on pending appointment blocked | Hospital authenticated, appointment pending | POST /hospital/appointments/{id}/no-show | 403 Forbidden | PASS | High |
| TC-PR-01 | ParentRequest model | Parent can create request | Parent authenticated | POST /parent/requests | Request created with pending status | PASS | High |
| TC-PR-02 | ParentRequest model | Parent can view own requests only | Parent authenticated | GET /parent/requests | Only own requests shown | PASS | High |
| TC-PR-03 | ParentRequest model | Parent cannot view other parents requests | Parent authenticated | GET /parent/requests with other parent ID | 403 Forbidden | PASS | High |
