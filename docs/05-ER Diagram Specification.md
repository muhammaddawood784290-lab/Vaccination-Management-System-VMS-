# ER Diagram Specification
## Vaccination Management System (VMS)

---

## 1. Entity-Relationship Overview

### 1.1 Entities

| Entity | Table | Key Attributes |
|--------|-------|----------------|
| User | users | id, name, email, password, role |
| Hospital | hospitals | id, name, code, email, city, status |
| Child | children | id, name, date_of_birth, gender, blood_group |
| Vaccine | vaccines | id, name, code, doses, type |
| VaccineInventory | vaccine_inventory | id, available, capacity, batch_number |
| VaccinationSchedule | vaccination_schedules | id, dose_number, due_date, status |
| Appointment | appointments | id, dose, appointment_date, appointment_time, status |
| VaccinationRecord | vaccination_records | id, dose_number, batch_number, administered_by |
| Notification | notifications | id, title, message, type, read_at |
| ParentRequest | parent_requests | id, type, subject, details, status |

---

## 2. Relationships

### 2.1 Relationship Matrix

| From Entity | Relationship | To Entity | Cardinality | FK Column |
|-------------|-------------|-----------|-------------|-----------|
| User (parent) | has many | Child | 1:N | children.user_id |
| User (parent) | has many | Appointment | 1:N | appointments.parent_id |
| User | has many | Notification | 1:N | notifications.user_id |
| User (hospital) | belongs to | Hospital | N:1 | users.hospital_id |
| Hospital | has many | User | 1:N | users.hospital_id |
| Hospital | has many | VaccineInventory | 1:N | vaccine_inventory.hospital_id |
| Hospital | has many | Appointment | 1:N | appointments.hospital_id |
| Hospital | has many | VaccinationRecord | 1:N | vaccination_records.hospital_id |
| Child | belongs to | User (parent) | N:1 | children.user_id |
| Child | has many | Appointment | 1:N | appointments.child_id |
| Child | has many | VaccinationRecord | 1:N | vaccination_records.child_id |
| Child | has many | VaccinationSchedule | 1:N | vaccination_schedules.child_id |
| Vaccine | has many | VaccineInventory | 1:N | vaccine_inventory.vaccine_id |
| Vaccine | has many | VaccinationSchedule | 1:N | vaccination_schedules.vaccine_id |
| Vaccine | has many | Appointment | 1:N | appointments.vaccine_id |
| Vaccine | has many | VaccinationRecord | 1:N | vaccination_records.vaccine_id |
| Appointment | belongs to | Child | N:1 | appointments.child_id |
| Appointment | belongs to | Hospital | N:1 | appointments.hospital_id |
| Appointment | belongs to | Vaccine | N:1 | appointments.vaccine_id |
| Appointment | belongs to | User (parent) | N:1 | appointments.parent_id |
| VaccinationRecord | belongs to | Child | N:1 | vaccination_records.child_id |
| VaccinationRecord | belongs to | Hospital | N:1 | vaccination_records.hospital_id |
| VaccinationRecord | belongs to | Vaccine | N:1 | vaccination_records.vaccine_id |
| VaccinationRecord | belongs to | Appointment | N:1 | vaccination_records.appointment_id |
| User (parent) | has many | ParentRequest | 1:N | parent_requests.user_id |
| Hospital | has many | ParentRequest | 1:N | parent_requests.hospital_id |
| ParentRequest | belongs to | User (parent) | N:1 | parent_requests.user_id |
| ParentRequest | belongs to | Hospital | N:1 | parent_requests.hospital_id |
| VaccinationSchedule | belongs to | Child | N:1 | vaccination_schedules.child_id |
| VaccinationSchedule | belongs to | Vaccine | N:1 | vaccination_schedules.vaccine_id |

---

## 3. ER Diagram (Text Representation)

```
                    ┌──────────────────┐
                    │      users       │
                    │──────────────────│
                    │ id (PK)          │
                    │ name             │
                    │ email (UQ)       │
                    │ password         │
                    │ role             │
                    │ phone            │
                    │ city             │
                    └────────┬─────────┘
                             │
              ┌──────────────┼──────────────────┐
              │              │                   │
              │ 1:N          │ 1:N               │ 1:N
              ▼              ▼                   ▼
    ┌─────────────┐  ┌──────────────┐  ┌────────────────┐
    │  children   │  │ appointments │  │ notifications  │
    │─────────────│  │ (parent_id)  │  │────────────────│
    │ id (PK)     │  │──────────────│  │ id (PK)        │
    │ user_id(FK) │  │ id (PK)      │  │ user_id (FK)   │
    │ name        │  │ child_id(FK) │  │ title          │
    │ date_of_birth│ │ parent_id(FK)│  │ message        │
    │ gender      │  │ hospital(FK) │  │ type           │
    │ blood_group │  │ vaccine_id(FK│  │ read_at        │
    │ status      │  │ date/time    │  └────────────────┘
    └──────┬──────┘  │ status       │
           │         └──────┬───────┘
           │                │
    ┌──────┼──────┐         │
    │      │      │         │
    │1:N   │1:N   │1:N      │N:1
    ▼      ▼      ▼         │
┌───────┐┌───────┐┌───────┐ │
│schedul││vacc   ││appoint│ │
│es     ││records││ments  │ │
│───────││───────││───────│ │
│id(PK) ││id(PK) ││id(PK) │ │
│child_ ││child_ ││       │ │
│id(FK) ││id(FK) ││       │ │
│vaccine││hospit.││       │ │
│_id(FK)││_id(FK)││       │ │
│dose_no││vaccine││       │ │
│due_date││_id(FK)││       │ │
│status ││batch# ││       │ │
└───────┘│admin_ ││       │ │
         │by     ││       │ │
         │date   ││       │ │
         └───────┘│       │ │
                  │       │ │
    ┌─────────────┘       │ │
    │                     │ │
    ▼                     ▼ │
┌──────────────┐  ┌────────────┐
│  hospitals   │  │  vaccines  │
│──────────────│  │────────────│
│ id (PK)      │  │ id (PK)    │
│ name         │  │ name       │
│ code (UQ)    │  │ code (UQ)  │
│ email (UQ)   │  │ doses      │
│ city         │  │ age_range  │
│ status       │  │ type       │
└──────┬───────┘  │ manufacturer│
       │          │ status      │
       │          └──────┬─────┘
       │                 │
       │ 1:N             │ 1:N
       ▼                 ▼
┌────────────────┐
│vaccine_inventory│
│────────────────│
│ id (PK)        │
│ hospital_id(FK)│
│ vaccine_id (FK)│
│ available      │
│ capacity       │
│ batch_number   │
│ expiry_date    │
└────────────────┘
```

---

## 4. Cardinality Summary

| Relationship | Type | Description |
|-------------|------|-------------|
| User → Child | One-to-Many | A parent has many children |
| User → Appointment | One-to-Many | A parent has many appointments |
| User → Notification | One-to-Many | A user has many notifications |
| Hospital → User | One-to-Many | A hospital has many staff users |
| Hospital → VaccineInventory | One-to-Many | A hospital has many vaccine stocks |
| Hospital → Appointment | One-to-Many | A hospital has many appointments |
| Hospital → VaccinationRecord | One-to-Many | A hospital has many records |
| Child → Appointment | One-to-Many | A child has many appointments |
| Child → VaccinationRecord | One-to-Many | A child has many records |
| Child → VaccinationSchedule | One-to-Many | A child has a vaccination schedule |
| Vaccine → VaccineInventory | One-to-Many | A vaccine exists in many hospital inventories |
| Vaccine → VaccinationSchedule | One-to-Many | A vaccine appears in many schedules |
| Vaccine → Appointment | One-to-Many | A vaccine is in many appointments |
| Vaccine → VaccinationRecord | One-to-Many | A vaccine is in many records |
| Appointment → VaccinationRecord | One-to-One | An appointment may produce one record |

---

## 5. Data Ownership Rules

| Role | Owns | Can Access |
|------|------|------------|
| Admin | System-wide | All data |
| Parent | Own children | Own children, own appointments, own vaccination records, own notifications |
| Hospital | Own hospital data | Own hospital appointments, own inventory, own vaccination records |

---

*Document: ER Diagram Specification v1.0 — Vaccination Management System*
