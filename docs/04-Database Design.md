# Database Design
## Vaccination Management System (VMS)

---

## 1. Overview

The VMS uses a single MySQL database with **10 application tables** and 8 Laravel infrastructure tables. All three roles (Admin, Parent, Hospital) share the same database through the users.role field.

### 1.1 Table Summary

| Table | Purpose | Key Relationships |
|-------|---------|-------------------|
| users | All authenticated users | Has children (as parent), belongs to hospital (as staff) |
| hospitals | Registered healthcare facilities | Has users, appointments, inventory, records |
| children | Registered children | Belongs to user (parent), has appointments, records |
| vaccines | Vaccine catalogue | Has inventory, schedules, appointments, records |
| vaccine_inventory | Hospital vaccine stock | Belongs to hospital + vaccine |
| vaccination_schedules | Recommended vaccination timeline | Belongs to child + vaccine |
| appointments | Scheduled vaccination sessions | Belongs to child, hospital, vaccine, parent |
| parent_requests | Parent submission requests | Belongs to user (submitter), reviewed by admin |
| vaccination_records | Completed vaccination records | Belongs to child, hospital, vaccine |
| notifications | User notifications | Belongs to user |
---

## 2. Table Definitions

### 2.1 users

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | BIGINT UNSIGNED (PK) | No | auto | Primary key |
| name | VARCHAR(255) | No | -- | Full name |
| email | VARCHAR(255) | No | unique | Email (login credential) |
| email_verified_at | TIMESTAMP | Yes | null | Email verification timestamp |
| password | VARCHAR(255) | No | -- | Bcrypt hashed password |
| role | ENUM(admin,parent,hospital) | No | -- | User role |
| phone | VARCHAR(20) | Yes | null | Phone number |
| city | VARCHAR(100) | Yes | null | City (for parents) |
| hospital_id | BIGINT UNSIGNED (FK->hospitals.id) | Yes | null | Hospital association (hospital role users only) |
| remember_token | VARCHAR(100) | Yes | null | Remember me token |
| created_at | TIMESTAMP | No | current_timestamp | Creation timestamp |
| updated_at | TIMESTAMP | No | current_timestamp | Update timestamp |

**Indexes:** email (unique), role, hospital_id
**Foreign Key:** hospital_id -> hospitals.id ON DELETE SET NULL

**Ownership/Authorization Notes:**
- Admin users: hospital_id is NULL
- Parent users: hospital_id is NULL
- Hospital users: hospital_id links to the hospital they represent
- Hospital isolation is enforced via: ->hospital_id === ->id

---

### 2.2 hospitals

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | BIGINT UNSIGNED (PK) | No | auto | Primary key |
| name | VARCHAR(255) | No | -- | Hospital name |
| code | VARCHAR(50) | No | unique | Registration code |
| email | VARCHAR(255) | No | unique | Hospital email |
| phone | VARCHAR(20) | No | -- | Contact phone |
| address | VARCHAR(500) | No | -- | Full address |
| city | VARCHAR(100) | No | -- | City |
| state | VARCHAR(100) | No | -- | State |
| contact_person | VARCHAR(255) | No | -- | Contact person name |
| designation | VARCHAR(100) | Yes | null | Contact person designation |
| total_beds | INTEGER | No | 0 | Total bed capacity |
| description | TEXT | Yes | null | Hospital description |
| status | ENUM(active,inactive,pending) | No | pending | Approval status |
| rating | DECIMAL(2,1) | Yes | null | Average rating |
| created_at | TIMESTAMP | No | current_timestamp | Creation timestamp |
| updated_at | TIMESTAMP | No | current_timestamp | Update timestamp |

**Indexes:** code (unique), email (unique), city, status
---

### 2.3 children

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | BIGINT UNSIGNED (PK) | No | auto | Primary key |
| user_id | BIGINT UNSIGNED (FK->users.id) | No | -- | Parent user ID |
| name | VARCHAR(255) | No | -- | Child full name |
| date_of_birth | DATE | No | -- | Date of birth |
| gender | ENUM(male,female) | No | -- | Gender |
| blood_group | VARCHAR(10) | Yes | null | Blood group (A+, B-, etc.) |
| relationship | VARCHAR(50) | Yes | null | Parent relationship (mother, father, etc.) |
| allergies | VARCHAR(500) | Yes | null | Known allergies |
| notes | TEXT | Yes | null | Additional notes |
| status | ENUM(active,inactive) | No | active | Record status |
| created_at | TIMESTAMP | No | current_timestamp | Creation timestamp |
| updated_at | TIMESTAMP | No | current_timestamp | Update timestamp |

**Indexes:** user_id (foreign key), status
**Constraint:** Foreign key user_id -> users.id ON DELETE CASCADE

---

### 2.4 vaccines

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | BIGINT UNSIGNED (PK) | No | auto | Primary key |
| name | VARCHAR(255) | No | -- | Vaccine name |
| code | VARCHAR(20) | No | unique | Short code (BCG, MMR, etc.) |
| doses | INTEGER | No | -- | Number of required doses |
| age_range | VARCHAR(100) | No | -- | Target age range |
| type | VARCHAR(100) | No | -- | Vaccine type (Live attenuated, etc.) |
| manufacturer | VARCHAR(255) | No | -- | Manufacturer name |
| description | TEXT | Yes | null | Vaccine description |
| status | ENUM(active,inactive) | No | active | Catalogue status |
| created_at | TIMESTAMP | No | current_timestamp | Creation timestamp |
| updated_at | TIMESTAMP | No | current_timestamp | Update timestamp |

**Indexes:** code (unique), name, status
---

### 2.5 vaccine_inventory

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | BIGINT UNSIGNED (PK) | No | auto | Primary key |
| hospital_id | BIGINT UNSIGNED (FK->hospitals.id) | No | -- | Hospital reference |
| vaccine_id | BIGINT UNSIGNED (FK->vaccines.id) | No | -- | Vaccine reference |
| available | INTEGER | No | 0 | Current stock quantity |
| capacity | INTEGER | No | 0 | Maximum capacity |
| batch_number | VARCHAR(100) | Yes | null | Current batch number |
| expiry_date | DATE | Yes | null | Batch expiry date |
| last_updated | TIMESTAMP | No | current_timestamp | Last stock update |
| created_at | TIMESTAMP | No | current_timestamp | Creation timestamp |
| updated_at | TIMESTAMP | No | current_timestamp | Update timestamp |

**Indexes:** hospital_id, vaccine_id
**Constraint:** Unique on (hospital_id, vaccine_id) -- one inventory record per hospital per vaccine
**Foreign Keys:** hospital_id -> hospitals.id ON DELETE CASCADE, vaccine_id -> vaccines.id ON DELETE CASCADE

---

### 2.6 vaccination_schedules

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | BIGINT UNSIGNED (PK) | No | auto | Primary key |
| child_id | BIGINT UNSIGNED (FK->children.id) | No | -- | Child reference |
| vaccine_id | BIGINT UNSIGNED (FK->vaccines.id) | No | -- | Vaccine reference |
| dose_number | INTEGER | No | -- | Which dose (1, 2, 3...) |
| target_age | VARCHAR(100) | No | -- | Target age description |
| due_date | DATE | No | -- | Recommended due date |
| status | ENUM(due,completed,overdue,skipped) | No | due | Schedule status |
| created_at | TIMESTAMP | No | current_timestamp | Creation timestamp |
| updated_at | TIMESTAMP | No | current_timestamp | Update timestamp |

**Indexes:** child_id, vaccine_id, due_date, status
**Foreign Keys:** child_id -> children.id ON DELETE CASCADE, vaccine_id -> vaccines.id ON DELETE CASCADE
---

### 2.7 appointments

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | BIGINT UNSIGNED (PK) | No | auto | Primary key |
| child_id | BIGINT UNSIGNED (FK->children.id) | No | -- | Child reference |
| parent_id | BIGINT UNSIGNED (FK->users.id) | No | -- | Parent who booked |
| hospital_id | BIGINT UNSIGNED (FK->hospitals.id) | No | -- | Hospital reference |
| vaccine_id | BIGINT UNSIGNED (FK->vaccines.id) | No | -- | Vaccine reference |
| dose | VARCHAR(50) | No | -- | Dose descriptor (e.g., Dose 1, Dose 2, Annual, Booster) |
| appointment_date | DATE | No | -- | Scheduled date |
| appointment_time | TIME | No | -- | Scheduled time |
| status | ENUM(pending,approved,confirmed,completed,cancelled,rejected,no_show) | No | pending | Appointment status |
| notes | TEXT | Yes | null | Additional notes/remarks |
| created_at | TIMESTAMP | No | current_timestamp | Creation timestamp |
| updated_at | TIMESTAMP | No | current_timestamp | Update timestamp |

**Indexes:** child_id, parent_id, hospital_id, vaccine_id, status, appointment_date
**Foreign Keys:** child_id -> children.id ON DELETE CASCADE, parent_id -> users.id ON DELETE CASCADE, hospital_id -> hospitals.id ON DELETE CASCADE, vaccine_id -> vaccines.id ON DELETE CASCADE

**Dose Field Decision (Issue H5 resolved):**
The dose field is VARCHAR(50) because the VMS supports dose descriptors beyond simple integers. Examples: Dose 1, Dose 2, Dose 3, Annual, Booster. An INTEGER type cannot represent Annual or Booster. The dose display is formatted as the descriptor string; when numeric dose ordering is needed, the related vaccination_schedules.dose_number (INTEGER) provides the ordinal position.

**Status Transitions (Issue H7 resolved):**

| Current Status | Allowed Next Status | Triggered By | Description |
|---------------|--------------------|----|-------------|
| pending | approved | Admin | Admin reviews and approves |
| pending | rejected | Admin | Admin rejects the request |
| pending | cancelled | Parent | Parent cancels before approval |
| approved | confirmed | Hospital | Hospital confirms appointment |
| approved | cancelled | Parent/Admin | Cancel before hospital confirms |
| approved | no_show | Hospital | Patient did not arrive |
| confirmed | completed | Hospital | Vaccination performed |
| confirmed | no_show | Hospital | Patient did not arrive after confirmation |
| completed | -- | -- | Terminal state |
| rejected | -- | -- | Terminal state |
| cancelled | -- | -- | Terminal state |
| no_show | -- | -- | Terminal state (parent must re-book) |

**Note:** The term scheduled was previously used in Algorithms doc (Issue H4) to mean appointments whose date is today. This is a QUERY condition (status=confirmed AND appointment_date=today), not a status value. Updated all documentation to use confirmed with a date filter instead.
---

### 2.8 parent_requests

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | BIGINT UNSIGNED (PK) | No | auto | Primary key |
| user_id | BIGINT UNSIGNED (FK->users.id) | No | -- | Parent who submitted |
| type | ENUM(vaccination_request,general_inquiry,complaint,feedback) | No | -- | Request category |
| subject | VARCHAR(255) | No | -- | Request subject/title |
| details | TEXT | No | -- | Detailed description |
| hospital_id | BIGINT UNSIGNED (FK->hospitals.id) | Yes | null | Target hospital (if applicable) |
| status | ENUM(pending,in_review,resolved,closed) | No | pending | Processing status |
| admin_response | TEXT | Yes | null | Admin response/notes |
| responded_at | TIMESTAMP | Yes | null | When admin responded |
| created_at | TIMESTAMP | No | current_timestamp | Creation timestamp |
| updated_at | TIMESTAMP | No | current_timestamp | Update timestamp |

**Indexes:** user_id, type, status, hospital_id
**Foreign Keys:** user_id -> users.id ON DELETE CASCADE, hospital_id -> hospitals.id ON DELETE SET NULL

**Purpose:** This table supports the Parent Request workflow visible in the Figma design. Parents submit requests (vaccination requests, inquiries, complaints, feedback) that Admin reviews and processes. The Figma ParentRequest entity maps directly to this table.

**Status Transitions:**

| Current Status | Next Status | Triggered By | Description |
|---------------|-------------|----|-------------|
| pending | in_review | Admin | Admin begins reviewing |
| pending | resolved | Admin | Admin resolves directly |
| in_review | resolved | Admin | Admin provides resolution |
| in_review | closed | Admin | Admin closes without resolution |
| resolved | closed | Admin | Admin archives resolved request |
---

### 2.9 vaccination_records

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | BIGINT UNSIGNED (PK) | No | auto | Primary key |
| child_id | BIGINT UNSIGNED (FK->children.id) | No | -- | Child reference |
| hospital_id | BIGINT UNSIGNED (FK->hospitals.id) | No | -- | Hospital that administered |
| vaccine_id | BIGINT UNSIGNED (FK->vaccines.id) | No | -- | Vaccine administered |
| appointment_id | BIGINT UNSIGNED (FK->appointments.id) | Yes | null | Linked appointment |
| dose_number | INTEGER | No | -- | Which dose (1, 2, 3...) |
| batch_number | VARCHAR(100) | Yes | null | Vaccine batch number |
| administered_by | VARCHAR(255) | Yes | null | Name of administering staff |
| administered_at | TIMESTAMP | No | current_timestamp | When vaccination was performed |
| notes | TEXT | Yes | null | Additional notes |
| created_at | TIMESTAMP | No | current_timestamp | Creation timestamp |
| updated_at | TIMESTAMP | No | current_timestamp | Update timestamp |

**Indexes:** child_id, hospital_id, vaccine_id, appointment_id, administered_at
**Foreign Keys:** child_id -> children.id ON DELETE CASCADE, hospital_id -> hospitals.id ON DELETE CASCADE, vaccine_id -> vaccines.id ON DELETE CASCADE, appointment_id -> appointments.id ON DELETE SET NULL

---

### 2.10 notifications

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | BIGINT UNSIGNED (PK) | No | auto | Primary key |
| user_id | BIGINT UNSIGNED (FK->users.id) | No | -- | Recipient user |
| title | VARCHAR(255) | No | -- | Notification title |
| message | TEXT | No | -- | Notification body |
| type | VARCHAR(50) | No | info | Category: info, success, warning, error |
| data | JSON | Yes | null | Additional metadata |
| read_at | TIMESTAMP | Yes | null | When user read the notification |
| created_at | TIMESTAMP | No | current_timestamp | Creation timestamp |
| updated_at | TIMESTAMP | No | current_timestamp | Update timestamp |

**Indexes:** user_id, read_at, type
**Foreign Key:** user_id -> users.id ON DELETE CASCADE
---

## 3. Entity Count Summary

| Category | Count |
|----------|-------|
| Application Tables | 10 |
| Total Columns (approx.) | ~130 |
| Foreign Keys | 18 |
| Unique Constraints | 4 (users.email, hospitals.code, hospitals.email, vaccine_inventory.hospital+vaccine) |
| ENUM Fields | 7 |
| JSON Fields | 1 (notifications.data) |

---

## 4. Data Ownership Rules

| Owner | Resource | Enforcement |
|-------|----------|-------------|
| Admin | System-wide access | users.role = admin |
| Parent | Own children | children.user_id = auth()->id() |
| Parent | Own appointments | appointments.parent_id = auth()->id() |
| Parent | Own notifications | notifications.user_id = auth()->id() |
| Parent | Own requests | parent_requests.user_id = auth()->id() |
| Hospital | Own vaccine inventory | vaccine_inventory.hospital_id = auth()->user()->hospital_id |
| Hospital | Own appointments | appointments.hospital_id = auth()->user()->hospital_id |
| Hospital | Own vaccination records | vaccination_records.hospital_id = auth()->user()->hospital_id |
| Hospital | Own users (staff) | users.hospital_id = auth()->user()->hospital_id |

---

## 5. Cascading Behavior

| Parent Table | Child Table | On Delete | On Update |
|-------------|-------------|-----------|-----------|
| users | children | CASCADE | CASCADE |
| users | appointments | CASCADE | CASCADE |
| users | parent_requests | CASCADE | CASCADE |
| users | notifications | CASCADE | CASCADE |
| users (hospital_id) | -- | SET NULL | CASCADE |
| hospitals | vaccine_inventory | CASCADE | CASCADE |
| hospitals | appointments | CASCADE | CASCADE |
| hospitals | vaccination_records | CASCADE | CASCADE |
| hospitals (parent_requests) | -- | SET NULL | CASCADE |
| vaccines | vaccine_inventory | CASCADE | CASCADE |
| vaccines | vaccination_schedules | CASCADE | CASCADE |
| vaccines | appointments | CASCADE | CASCADE |
| vaccines | vaccination_records | CASCADE | CASCADE |
| children | vaccination_schedules | CASCADE | CASCADE |
| children | appointments | CASCADE | CASCADE |
| children | vaccination_records | CASCADE | CASCADE |
| appointments | vaccination_records | SET NULL | CASCADE |