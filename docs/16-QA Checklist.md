# QA Checklist
## Vaccination Management System (VMS)

---

## 1. Functional QA

| ID | Module | Check | Expected | Status |
|----|--------|-------|----------|--------|
| QA-001 | Auth | Login with valid credentials | Redirects to role dashboard | ☐ |
| QA-002 | Auth | Login with invalid credentials | Shows "credentials do not match" | ☐ |
| QA-003 | Auth | Parent registration (2-step) | Account created, auto-login | ☐ |
| QA-004 | Auth | Hospital registration | Account created with pending status | ☐ |
| QA-005 | Auth | Password reset flow | Email sent, reset works | ☐ |
| QA-006 | Auth | Logout | Session destroyed, redirect to login | ☐ |
| QA-007 | Admin | Dashboard KPIs display correctly | All 4 cards with real data | ☐ |
| QA-008 | Admin | Children list with search/filter | Results filter correctly | ☐ |
| QA-009 | Admin | Vaccine CRUD | Create, edit, delete all work | ☐ |
| QA-010 | Admin | Hospital CRUD + status toggle | All operations work | ☐ |
| QA-011 | Admin | Appointment approve/reject/cancel | Status transitions correctly | ☐ |
| QA-012 | Admin | Reports page renders with data | Charts and tables display | ☐ |
| QA-013 | Admin | Profile update | Name/email/password changes | ☐ |
| QA-014 | Admin | Notifications mark-as-read | Status updates | ☐ |
| QA-015 | Parent | Dashboard shows own children | Only own children displayed | ☐ |
| QA-016 | Parent | Add child | Child created with correct parent_id | ☐ |
| QA-017 | Parent | Edit child | Changes saved correctly | ☐ |
| QA-018 | Parent | Delete child | Child removed | ☐ |
| QA-019 | Parent | Vaccination schedule shows correct data | Per-child schedule correct | ☐ |
| QA-020 | Parent | Find hospitals | Active hospitals listed, search works | ☐ |
| QA-021 | Parent | Book appointment | Appointment created with status=pending | ☐ |
| QA-022 | Parent | Cancel appointment | Only pending/approved can cancel | ☐ |
| QA-023 | Parent | Vaccination history | Records for own children only | ☐ |
| QA-024 | Hospital | Dashboard today's queue | Correct count, clickable | ☐ |
| QA-025 | Hospital | 4-step vaccination workflow | All steps function correctly | ☐ |
| QA-026 | Hospital | Vaccination completion creates record | Record saved with all fields | ☐ |
| QA-027 | Hospital | Vaccine inventory update | Stock levels update correctly | ☐ |
| QA-028 | Hospital | Low stock alerts display | Red alert for <30% stock | ☐ |
| QA-029 | Hospital | Vaccination records view | Own hospital records only | ☐ |
| QA-030 | Hospital | Profile update | Hospital info changes saved | ☐ |

---

## 2. Authorization QA

| ID | Check | Expected | Status |
|----|-------|----------|--------|
| QA-031 | Parent accesses /admin/dashboard | 403 Forbidden | ☐ |
| QA-032 | Hospital accesses /admin/dashboard | 403 Forbidden | ☐ |
| QA-033 | Admin accesses /parent/dashboard | 403 Forbidden | ☐ |
| QA-034 | Parent views other parent's child | 403 Forbidden | ☐ |
| QA-035 | Hospital views other hospital's appointments | 403 Forbidden | ☐ |
| QA-036 | Unauthenticated access to /admin/dashboard | Redirect to /login | ☐ |
| QA-037 | Unauthenticated access to /parent/dashboard | Redirect to /login | ☐ |
| QA-038 | Parent approves appointment | 403 Forbidden | ☐ |

---

## 3. UI/UX QA

| ID | Check | Expected | Status |
|----|-------|----------|--------|
| QA-039 | Sidebar navigation renders on all portals | Consistent sidebar with correct items | ☐ |
| QA-040 | Active nav item highlighted | Correct item has active style | ☐ |
| QA-041 | Tables display data correctly | Columns aligned, data readable | ☐ |
| QA-042 | Forms have proper labels | All required fields marked | ☐ |
| QA-043 | Form validation errors display | Inline errors below fields | ☐ |
| QA-044 | Empty states show when no data | "No records found" messages | ☐ |
| QA-045 | Flash messages display on success/error | Green/red alerts | ☐ |
| QA-046 | Modals open and close correctly | Backdrop, close button, escape key | ☐ |
| QA-047 | Search/filter returns correct results | Filtering works as expected | ☐ |
| QA-048 | Pagination works on list views | 10 items per page, page navigation | ☐ |
| QA-049 | Responsive: sidebar collapses on tablet | Icons-only sidebar | ☐ |
| QA-050 | Responsive: mobile layout works | Single column, stacked cards | ☐ |
| QA-051 | No broken links/routes | All nav links work | ☐ |
| QA-052 | No console errors | Clean browser console | ☐ |
| QA-053 | Assets load correctly | CSS, JS, images all load | ☐ |
| QA-054 | Focus states visible | Tab navigation shows focus ring | ☐ |
| QA-055 | Status badges display correct colors | Green/Blue/Yellow/Red matching status | ☐ |

---

## 4. Database QA

| ID | Check | Expected | Status |
|----|-------|----------|--------|
| QA-056 | All migrations run cleanly | No errors | ☐ |
| QA-057 | Seeders populate valid data | All records valid | ☐ |
| QA-058 | Foreign keys enforced | Cannot create orphan records | ☐ |
| QA-059 | Unique constraints enforced | Cannot duplicate emails/codes | ☐ |
| QA-060 | Cascade deletes work | Deleting parent removes children | ☐ |

---

## 5. Security QA

| ID | Check | Expected | Status |
|----|-------|----------|--------|
| QA-061 | CSRF token on all forms | Token present in form HTML | ☐ |
| QA-062 | No unescaped Blade output | All {{ }} not {!! !!} | ☐ |
| QA-063 | Passwords hashed (bcrypt) | Cannot read plaintext password | ☐ |
| QA-064 | Mass assignment protected | Cannot set role via URL parameter | ☐ |
| QA-065 | Session cookie is HTTP-only | Cookie has httponly flag | ☐ |
| QA-066 | APP_DEBUG=false in production | No debug info exposed | ☐ |
| QA-067 | .env not publicly accessible | Cannot access via URL | ☐ |

---

*Document: QA Checklist v1.0 — Vaccination Management System*
