# Interface Design Document
## Vaccination Management System (VMS)

---

## 1. Screen Inventory

### 1.1 Authentication Screens

| Screen | Route | Components | Description |
|--------|-------|-----------|-------------|
| Login | GET /login | Split layout (dark left + form right), role selector, email/password inputs, demo credentials box, register buttons | Entry point for all roles |
| Register Parent | GET /register/parent | Step indicator (2 steps), personal details form, password form, terms checkbox | Parent self-registration |
| Register Hospital | GET /register/hospital | Back link, form (hospital details + password), info alert, success state | Hospital registration |
| Forgot Password | GET /forgot-password | Lock icon, email input, submit button | Password reset request |
| Check Email | GET /forgot-password (after submit) | Email icon, confirmation message, email address display, back button | Confirmation screen |

### 1.2 Admin Screens (15)

| Screen | Route | Key Components |
|--------|-------|---------------|
| Dashboard | /admin/dashboard | 4 KPI cards, bar chart, pie chart, hospital overview, pending requests, vaccine usage, activity feed |
| Children Index | /admin/children | Search, gender filter, data table (avatar, name, DOB, gender, blood group, parent, progress bar, status) |
| Child Show | /admin/children/{id} | Avatar, personal info grid, vaccination progress bar |
| Vaccines Index | /admin/vaccines | 3 KPI cards, search, data table (name, code, doses, age range, type, manufacturer, status) |
| Vaccine Create | /admin/vaccines/create | Form (name, code, doses, age range, type dropdown, manufacturer, description, status) |
| Vaccine Edit | /admin/vaccines/{id}/edit | Pre-filled form matching create |
| Hospitals Index | /admin/hospitals | 3 KPI cards, search, status filter, data table (icon, name, code, city, contact, vaccinations count, status) |
| Hospital Create | /admin/hospitals/create | Full form (name, code, email, phone, city, state, beds, address, contact, designation, status) |
| Hospital Show | /admin/hospitals/{id} | Hospital info grid, status badge, action buttons (activate/deactivate) |
| Hospital Edit | /admin/hospitals/{id}/edit | Pre-filled form |
| Appointments Index | /admin/appointments | 4 KPI cards, status tabs, search, data table (child, vaccine, hospital, date/time, status) |
| Appointment Show | /admin/appointments/{id} | Avatar, detail grid, status badge, notes |
| Reports | /admin/reports | 4 summary cards, line chart, bar chart, hospital performance table |
| Profile | /admin/profile | Profile info form + password change form |
| Notifications | /admin/notifications | Notification list with type badges, unread highlighting, mark-as-read |

### 1.3 Parent Screens (15)

| Screen | Route | Key Components |
|--------|-------|---------------|
| Dashboard | /parent/dashboard | KPI cards, child cards with progress, next appointment, recent vaccinations, quick actions |
| Children Index | /parent/children | Card grid (avatar, name, age, progress, completed/upcoming/overdue counts) |
| Add Child | /parent/children/create | Form (name, DOB, gender, blood group, relationship, allergies, notes) |
| Child Show | /parent/children/{id} | Personal info, vaccination history table, edit/delete buttons |
| Child Edit | /parent/children/{id}/edit | Pre-filled form |
| Vaccination Schedule | /parent/vaccinations/schedule | Child filter, schedule table (vaccine, dose, target age, due date, status badge) |
| Find Hospitals | /parent/hospitals | Search, city filter, hospital cards (name, city, vaccine count, rating) |
| Hospital Show | /parent/hospitals/{id} | Hospital info, available vaccines with status, book appointment button |
| Book Appointment | /parent/appointments/create | Form (child dropdown, vaccine dropdown, hospital dropdown, date, time, notes) |
| Appointments Index | /parent/appointments | Status filter, appointment table with cancel action |
| Appointment Show | /parent/appointments/{id} | Full details, cancel button for pending/approved |
| Vaccination History | /parent/vaccinations/history | Child + vaccine filters, records table |
| Vaccination Record | /parent/vaccinations/{id} | Full record details (batch, administered by, side effects, next due) |
| Profile | /parent/profile | Personal info form + password change |
| Notifications | /parent/notifications | Notification list with mark-as-read |

### 1.4 Hospital Screens (8)

| Screen | Route | Key Components |
|--------|-------|---------------|
| Dashboard | /hospital/dashboard | 3 KPI cards, appointment queue (clickable), completion pie chart, hourly bar chart, stock alerts |
| Appointments Index | /hospital/appointments | Status tabs (Today/Upcoming/Completed/All), search, table with Process/View buttons |
| Appointment Detail / Workflow | /hospital/appointments/{id} | 4-step workflow (patient verify, vaccine verify, administer, record), no-show modal |
| Vaccine Inventory | /hospital/vaccines | Stock cards with progress bars, low stock alerts, update modal |
| Vaccination Records | /hospital/vaccinations | Search, records table with status badges |
| Vaccination Record Detail | /hospital/vaccinations/{id} | Full record details |
| Profile | /hospital/profile | Hospital info form + password change |
| Notifications | /hospital/notifications | Notification list with mark-as-read |

---

## 2. Navigation Structure

### 2.1 Admin Sidebar

| Icon | Label | Route | Badge |
|------|-------|-------|-------|
| Grid | Dashboard | /admin/dashboard | — |
| Users | Children | /admin/children | — |
| Syringe | Vaccines | /admin/vaccines | — |
| Building | Hospitals | /admin/hospitals | — |
| Calendar | Appointments | /admin/appointments | — |
| Clipboard | Parent Requests | /admin/requests | pending count |
| Chart | Reports | /admin/reports | — |
| Bell | Notifications | /admin/notifications | unread count |
| Person | Profile | /admin/profile | — |
| Gear | Settings | /admin/settings | — |

### 2.2 Parent Sidebar

| Icon | Label | Route | Badge |
|------|-------|-------|-------|
| Home | Dashboard | /parent/dashboard | — |
| Users | My Children | /parent/children | — |
| Calendar | Vaccination Schedule | /parent/vaccinations/schedule | — |
| Building | Find Hospitals | /parent/hospitals | — |
| Clock | Appointments | /parent/appointments | upcoming count |
| History | Vaccination History | /parent/vaccinations/history | — |
| Bell | Notifications | /parent/notifications | unread count |
| Person | Profile | /parent/profile | — |
| Gear | Settings | /parent/settings | — |

### 2.3 Hospital Sidebar

| Icon | Label | Route | Badge |
|------|-------|-------|-------|
| Grid | Dashboard | /hospital/dashboard | — |
| Calendar | Appointments | /hospital/appointments | today's upcoming count |
| Syringe | Vaccinations | /hospital/vaccinations | — |
| Box | Vaccine Inventory | /hospital/vaccines | — |
| Document | Vaccination Records | /hospital/vaccinations | — |
| Bell | Notifications | /hospital/notifications | unread count |
| Building | Hospital Profile | /hospital/profile | — |
| Gear | Settings | /hospital/settings | — |

---

## 3. Form Specifications

### 3.1 Parent Registration (2-Step)

**Step 1 — Personal Details:**
- First Name (text, required)
- Last Name (text, required)
- Email (email, required, icon)
- Phone (tel, required)
- City (text, required)

**Step 2 — Account Setup:**
- Password (password, required, min 8, hint: "Must contain uppercase, number and special character")
- Confirm Password (password, required)
- Terms checkbox (required to proceed)

### 3.2 Book Appointment

- Child (dropdown, required — populated with own children)
- Vaccine (dropdown, required — populated with active vaccines)
- Hospital (dropdown, required — populated with active hospitals)
- Date (date picker, required — future dates only)
- Time (time picker, required)
- Notes (textarea, optional)

### 3.3 Vaccination Record (Hospital Step 4)

- Administered By (text, required — default: logged-in user name)
- Batch Number (text, required)
- Next Due Date (date, optional)
- Side Effects (text, optio

---

### 3.7 Hospital Settings

| Screen | URI | Description |
|--------|-----|-------------|
| Settings | /hospital/settings | Tabbed settings: Notifications (email, low stock alerts), Password (current, new, confirm) |
