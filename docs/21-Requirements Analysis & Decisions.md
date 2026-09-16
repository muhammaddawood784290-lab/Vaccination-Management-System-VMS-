# Requirements Analysis & Decisions
## Vaccination Management System (VMS)

---

## 1. Contradictions Identified

### 1.1 Hospital Registration vs Hospital Management

**Conflict:** The Figma design shows hospitals self-registering (with admin approval), but also shows admins directly creating hospitals via a form.

**Analysis:** Both workflows are valid and complementary. Hospital self-registration is the primary flow; admin creation is an override for pre-approved facilities.

**Resolution:** Implement both. Hospital self-registration creates a `pending` record. Admin can also create hospitals directly with `active` status.

---

### 1.2 Appointment Status Values

**Conflict:** The Figma design uses "Scheduled" as a status, but the database design uses "pending" as the initial status after booking.

**Analysis:** These represent different stages. "Scheduled" could mean the hospital has confirmed, while "pending" means awaiting admin approval.

**Resolution:** Use the database schema status values: `pending → approved → confirmed → completed`. Map "Scheduled" in the Figma UI to the `confirmed` status in the database. The Figma design simplifies the workflow for visual purposes.

---

### 1.3 Parent Request Types

**Conflict:** The Figma design shows a "Parent Requests" section in the Admin portal with types like "Hospital Registration" and "Record Update." The SRS does not explicitly define this as a separate entity.

**Analysis:** The Figma design treats these as admin-side requests that come from hospitals or parents. The SRS focuses on appointment-based workflows.

**Resolution:** Treat "Parent Requests" as a view into the `appointments` table (status=pending) plus hospital registrations (status=pending). Implement as a filtered view of existing data rather than a new entity.

---

## 2. Ambiguities Resolved

### 2.1 Vaccination Schedule Generation

**Ambiguity:** When are vaccination schedules created? On child registration? On demand?

**Resolution:** Schedules are generated when a child is registered. For each active vaccine, calculate due dates based on the child's date of birth and the vaccine's age_range. This can be implemented as a post-creation job.

---

### 2.2 Vaccine Inventory Decrement

**Ambiguity:** When is vaccine inventory decremented? When appointment is booked? When vaccination is completed?

**Resolution:** Decrement when vaccination is completed (Hospital confirms). This ensures accurate stock levels — no premature deductions for no-shows or cancellations.

---

### 2.3 Hospital Staff Login

**Ambiguity:** Can a hospital have multiple staff users? How are they managed?

**Resolution:** For this version, assume one user per hospital. The hospital user IS the hospital's representative. Multi-staff support is a recommended enhancement.

---

### 2.4 Charts in Blade

**Ambiguity:** The Figma design uses Recharts (React). How to implement in Blade?

**Resolution:** Use Chart.js or a similar lightweight JavaScript charting library that works with server-rendered HTML. Embed chart data as JSON in `<script>` tags and initialize charts client-side.

---

## 3. Missing Requirements Identified

### 3.1 Export PDF (Admin Reports)

**Missing:** The Figma design shows an "Export PDF" button on the Reports page. No PDF generation library is in the requirements.

**Resolution:** Recommended Enhancement. Document as future feature. Implementing with dompdf or similar library would be straightforward but adds a dependency.

---

### 3.2 Settings Page

**Missing:** The Figma design shows a "Settings" page for all portals (notification toggles, security options). The SRS doesn't detail this.

**Resolution:** Implement a basic settings page with notification preferences and security options (change password). Keep it simple — no API integration needed.

---

### 3.3 Parent Relationship to Child

**Missing:** The Figma design shows "Parent: Vikram Sharma" under child details. The SRS doesn't specify the relationship type field on children.

**Resolution:** Add a `relationship` field (mother, father, guardian) to the children table. Required for parent registration flow.

---

## 4. Assumptions Made

| # | Assumption | Rationale |
|---|-----------|-----------|
| 1 | One user per hospital | Simplifies multi-tenancy; recommended enhancement for multi-staff |
| 2 | No email sending in development | Mail driver set to `log` in .env.example |
| 3 | Charts rendered client-side | Blade cannot render interactive charts server-side |
| 4 | No file upload for child photos | Not in requirements; avatar generated from initials |
| 5 | No real-time notifications | WebSocket/polling not required; page refresh suffices |
| 6 | No REST API | Blade-only; no mobile app or SPA |
| 7 | No payment processing | Vaccination management is the scope, not billing |
| 8 | English language only | No i18n/l10n required |

---

## 5. Design Decisions

| # | Decision | Rationale |
|---|---------|-----------|
| 1 | Single database with role column on users | Avoids separate databases; simpler queries |
| 2 | Role prefix on routes (/admin/, /parent/, /hospital/) | Clear separation; works with Laravel middleware |
| 3 | Blade components for reusable UI | Consistent design; follows Figma component structure |
| 4 | Tailwind CSS over Bootstrap | Matches Figma design tokens exactly |
| 5 | Policies + Gates over Spatie Permission | Lighter weight; sufficient for 3 roles |
| 6 | Database sessions over file sessions | Better for shared hosting (Hostinger) |
| 7 | Bcrypt over Argon2 | Broader PHP compatibility; 12 rounds sufficient |

---

## 6. Recommended Enhancements (Non-Conflicting)

| # | Enhancement | Priority | Notes |
|---|------------|----------|-------|
| 1 | Multi-staff hospital accounts | Medium | Add staff_user pivot table |
| 2 | Email notifications (actual sending) | Medium | Configure SMTP, use Mailable |
| 3 | Child photo upload | Low | Store in storage disk |
| 4 | Export PDF reports | Low | Add dompdf dependency |
| 5 | Real-time notifications | Low | Add Laravel Echo + Pusher |
| 6 | REST API for mobile | Low | Separate API routes with Sanctum |
| 7 | Multi-language support | Low | Laravel localization |
| 8 | Two-factor authentication | High | Add TOTP/SMS verification |

---

*Document: Requirements Analysis & Decisions v1.0 — Vaccination Management System*
