# Algorithms & Workflows
## Vaccination Management System (VMS)

---

## 1. Core Workflows

### 1.1 Parent Complete Journey

```
Parent Register → Login → Add Child → View Schedule → Find Hospital
→ Book Appointment → Track Status → Hospital Processes → Vaccination
→ View History
```

**Step-by-step:**

1. **Registration**: Parent fills 2-step form (personal details → password/terms)
2. **Login**: Email + password → redirected to /parent/dashboard
3. **Add Child**: Fill form (name, DOB, gender, blood group, allergies) → child created with status=active
4. **View Schedule**: System generates vaccination_schedules based on child DOB and vaccine age_range
5. **Find Hospital**: Browse active hospitals, filter by city, view available vaccines
6. **Book Appointment**: Select child, vaccine, hospital, date, time → appointment.status = pending
7. **Track Status**: View appointments list with status badges (pending/approved/completed/etc.)
8. **Vaccination**: Hospital processes → status chain: pending → approved → confirmed → completed
9. **View History**: View completed vaccination_records for own children

---

### 1.2 Admin Complete Journey

```
Login → Dashboard → Manage Vaccines → Manage Hospitals → Review Appointments
→ Approve/Reject → Monitor Vaccinations → Generate Reports
```

**Step-by-step:**

1. **Login**: Admin credentials → redirected to /admin/dashboard
2. **Dashboard**: View KPIs (total children, vaccinations, appointments, requests)
3. **Manage Vaccines**: CRUD operations on vaccine catalogue
4. **Manage Hospitals**: Approve pending registrations, activate/deactivate
5. **Review Appointments**: View all, approve/reject pending requests
6. **Monitor**: View vaccination records, reports
7. **Reports**: View trends, distributions, hospital performance

---

### 1.3 Hospital Complete Journey

```
Login → Dashboard → View Appointments → Verify Child/Vaccine
→ Confirm Appointment → Perform Vaccination → Update Status → Record
```

**Step-by-step:**

1. **Login**: Hospital credentials → redirected to /hospital/dashboard
2. **Dashboard**: View today's queue, pending count, completed count
3. **View Appointments**: Filter by Today/Upcoming/Completed
4. **4-Step Workflow**:
   - Step 1: Verify patient identity (view child/parent info)
   - Step 2: Verify vaccine (enter batch number, confirm expiry)
   - Step 3: Administer (checklist: consent, allergies, site, dose)
   - Step 4: Record (administered_by, batch, next_due, side_effects, notes)

---

## 2. Appointment Status Machine

### 2.1 Valid Transitions

```
                    ┌──────────────┐
                    │    pending    │ ← (Parent books)
                    └──────┬───────┘
                           │
              ┌────────────┼────────────┐
              ▼            ▼            ▼
        ┌──────────┐ ┌──────────┐ ┌──────────┐
        │ approved │ │ rejected │ │cancelled │
        └────┬─────┘ └──────────┘ └──────────┘
             │
    ┌────────┼─────────┐
    ▼        ▼         ▼
┌────────┐ ┌──────┐ ┌──────────┐
│confirm.│ │cancel│ │ no_show  │
└───┬────┘ └──────┘ └──────────┘
    │
    ▼
┌──────────┐
│completed │ → VaccinationRecord created
└──────────┘
```

### 2.2 Transition Rules

| From | To | Actor | Conditions |
|------|----|-------|------------|
| pending | approved | Admin | Review passed |
| pending | rejected | Admin | Review failed |
| pending | cancelled | Parent/Admin | Before hospital processes |
| approved | confirmed | Hospital | Hospital confirms attendance |
| approved | cancelled | Parent/Admin | Before hospital confirms |
| confirmed | completed | Hospital | Vaccination administered |
| confirmed | no_show | Hospital | Patient did not arrive |

### 2.3 Invalid Transitions (Blocked)

| From | To | Reason |
|------|----|--------|
| pending | confirmed | Must be approved first |
| pending | completed | Must go through approval + confirmation |
| completed | cancelled | Already completed, cannot undo |
| completed | no_show | Already completed |
| rejected | approved | Already rejected, must re-book |
| no_show | completed | Must re-book |

---

## 3. Vaccination Record Creation Algorithm

When hospital completes vaccination (Step 4):

```
1. Validate all required fields
   - administered_by (required)
   - batch_number (required)
   - date_administered = today

2. Create VaccinationRecord
   - child_id = appointment.child_id
   - hospital_id = appointment.hospital_id
   - vaccine_id = appointment.vaccine_id
   - appointment_id = appointment.id
   - dose_number = appointment.dose_number
   - batch_number = input
   - administered_by = input
   - date_administered = today
   - next_due_date = input (nullable)
   - side_effects = input (nullable)
   - clinical_notes = input (nullable)
   - status = 'done'

3. Update Appointment
   - status = 'completed'

4. Update VaccinationSchedule
   - Find schedule where child_id AND vaccine_id AND dose_number match
   - Set status = 'completed'

5. Update VaccineInventory
   - Find inventory where hospital_id AND vaccine_id match
   - Decrement available by 1
   - If available < 30% of capacity → flag for low stock alert

6. Create Notifications
   - Notify parent: "Vaccination completed for [child_name]"
   - Notify admin: "Vaccination recorded at [hospital_name]"
```

---

## 4. Hospital Registration Algorithm

```
1. Hospital fills registration form
2. Create User record (role=hospital, hospital_id=null)
3. Create Hospital record (status=pending)
4. Link User to Hospital (update user.hospital_id)
5. Send notification to Admin: "New hospital registration pending"
6. Show success: "Application submitted, review in 2-3 business days"

Admin Approval:
1. Admin views pending hospital
2. Approve → Hospital.status = active
3. Reject → Hospital.status = inactive, notify hospital
```

---

## 5. Vaccination Schedule Generation

When a child is registered:

```
1. For each active vaccine:
   a. Parse age_range to determine first dose timing
   b. Calculate due_date based on child.date_of_birth + age_offset
   c. Create VaccinationSchedule entry for each dose:
      - dose_number = i (1 to vaccines.doses)
      - target_age = descriptive text
      - due_date = calculated date
      - status = 'due'

2. Compare with existing vaccination_records:
   - If record exists for this child + vaccine + dose → status = 'completed'
   - If due_date < today AND no record → status = 'overdue'
```

---

## 6. Dashboard KPI Algorithms

### 6.1 Admin Dashboard

```
Total Children = COUNT(children)
Total Vaccinations = COUNT(vaccination_records WHERE status='done')
Upcoming Appointments = COUNT(appointments WHERE status IN ('pending','approved') AND appointment_date >= today)
Pending Requests = COUNT(appointments WHERE status='pending')
Monthly Vaccinations = GROUP BY month FROM vaccination_records (last 12 months)
Appointment Status % = GROUP BY status FROM appointments
```

### 6.2 Hospital Dashboard

```
Today's Appointments = COUNT(appointments WHERE hospital_id=own AND appointment_date=today)
Pending Vaccinations = COUNT(appointments WHERE hospital_id=own AND status='confirmed' AND appointment_date=today)
Completed Today = COUNT(appointments WHERE hospital_id=own AND status='completed' AND date=today)
Vaccine Types Available = COUNT(vaccine_inventory WHERE hospital_id=own AND available > 0)
```

---

## 7. Search & Filter Algorithm

All index pages follow this pattern:

```
1. Start with base query (scope by ownership for parent/hospital)
2. Apply search filter (LIKE %query% on name/email fields)
3. Apply dropdown filter (WHERE status = selected_status)
4. Apply date range filter (WHERE date BETWEEN start AND end)
5. Apply sort (ORDER BY created_at DESC by default)
6. Paginate (->paginate(10))
7. Return to view with filtered results
```

---

*Document: Algorithms & Workflows v1.0 — Vaccination Management System*
