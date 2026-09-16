# GUI Standards
## Vaccination Management System (VMS)

---

## 1. Design System

### 1.1 Color Palette

| Token | Hex | Usage |
|-------|-----|-------|
| Primary | #1C64F2 | Buttons, links, active states, parent sidebar |
| Primary Hover | #1A56DB | Button hover |
| Primary Light | #EBF5FF | Badge backgrounds, icon backgrounds |
| Teal | #0891B2 | Hospital portal accent, charts |
| Teal Light | #E0F2FE | Hospital light backgrounds |
| Purple | #7C3AED | Admin sidebar logo, accent |
| Background | #F8FAFC | Page background |
| Surface | #FFFFFF | Cards, modals |
| Text Primary | #111928 | Headings, primary text |
| Text Secondary | #4B5563 | Body text, descriptions |
| Text Muted | #9CA3AF | Labels, timestamps, placeholders |
| Border | #E5E7EB | Card borders, dividers |
| Border Strong | #D1D5DB | Input borders |
| Success | #057A55 | Approved, completed, active |
| Success BG | #F0FDF4 | Success badge background |
| Warning | #92400E | Warning text |
| Warning BG | #FFFBEB | Warning badge background |
| Error | #C81E1E | Rejected, no-show, delete |
| Error BG | #FEF2F2 | Error badge background |
| Info | #1C64F2 | Information badges |
| Info BG | #EBF5FF | Info badge background |
| Sidebar BG | #111928 | Dark sidebar |
| Sidebar Text | #9CA3AF | Sidebar inactive items |
| Sidebar Active | #FFFFFF | Sidebar active item text |
| Sidebar Active BG | #1C64F2 | Sidebar active item background |

### 1.2 Typography

| Element | Font | Weight | Size |
|---------|------|--------|------|
| Page Title | DM Sans | 600 (semibold) | 24px / 1.5rem |
| Section Title | DM Sans | 600 (semibold) | 16px / 1rem |
| Body Text | Inter | 400 (regular) | 14px / 0.875rem |
| Small Text | Inter | 400 | 12px / 0.75rem |
| Label | Inter | 500 (medium) | 14px / 0.875rem |
| KPI Value | DM Sans | 700 (bold) | 30px / 1.875rem |
| Monospace | JetBrains Mono | 400/500 | 14px |
| Button | Inter | 500-600 | 14px |
| Badge | Inter | 500-600 | 11-12px |

### 1.3 Spacing

| Token | Value |
|-------|-------|
| Page Padding | 24px (p-6) |
| Card Padding | 20-24px (p-5 / p-6) |
| Section Gap | 24px (space-y-6) |
| Item Gap | 16px (gap-4) |
| Tight Gap | 8px (gap-2) |
| Input Padding | 8px 12px (px-3 py-2) |
| Card Border Radius | 10-12px (rounded-[10px] / rounded-xl) |
| Button Border Radius | 8px (rounded-[8px]) |
| Badge Border Radius | 9999px (rounded-full) |
| Icon Background | 8px radius |

### 1.4 Shadows

| Level | CSS |
|-------|-----|
| sm | 0 1px 2px rgba(0,0,0,0.05) |
| Default | 0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.04) |
| md | 0 4px 6px rgba(0,0,0,0.05), 0 2px 4px rgba(0,0,0,0.04) |

---

## 2. Layout System

### 2.1 Page Layout

```
┌──────────────────────────────────────────────────┐
│  Sidebar (256px, fixed)  │  Main Content Area     │
│                          │                        │
│  ┌──────────────────┐    │  ┌──────────────────┐  │
│  │ Logo + Portal     │    │  │ Topbar           │  │
│  │ Name              │    │  │ (breadcrumb,     │  │
│  ├──────────────────┤    │  │  user menu, bell)│  │
│  │ Nav Item 1  [•]  │    │  ├──────────────────┤  │
│  │ Nav Item 2       │    │  │                  │  │
│  │ Nav Item 3       │    │  │ Page Content     │  │
│  │ ...              │    │  │ (scrollable)     │  │
│  │                  │    │  │                  │  │
│  ├──────────────────┤    │  │                  │  │
│  │ Sign Out         │    │  │                  │  │
│  └──────────────────┘    │  └──────────────────┘  │
└──────────────────────────────────────────────────┘
```

### 2.2 Responsive Breakpoints

| Breakpoint | Width | Sidebar | Behavior |
|------------|-------|---------|----------|
| Mobile | < 768px | Hidden (hamburger) | Single column, stacked cards |
| Tablet | 768-1024px | Collapsed (icons only) | 2-column grid |
| Desktop | > 1024px | Full (256px) | Full layout |

---

## 3. Component Standards

### 3.1 Cards

- White background (#FFFFFF)
- Border: 1px solid #E5E7EB
- Border radius: 10-12px
- Padding: 20-24px
- Optional header with border-bottom divider

### 3.2 Buttons

| Variant | Background | Text | Border |
|---------|-----------|------|--------|
| Primary | #1C64F2 | White | None |
| Success | #057A55 | White | None |
| Danger | #C81E1E | White | None |
| Outline | White | #374151 | #D1D5DB |
| Ghost | Transparent | #6B7280 | None |

Sizes: sm (32px height), default (40px), lg (48px)

### 3.3 Badges/Status

| Status | Background | Text | Dot Color |
|--------|-----------|------|-----------|
| Active/Completed/Approved/Done | #F0FDF4 | #057A55 | #057A55 |
| Pending/Scheduled | #EBF5FF | #1C64F2 | #1C64F2 |
| Warning | #FFFBEB | #92400E | #D97706 |
| Error/Rejected/No-show | #FEF2F2 | #C81E1E | #C81E1E |
| Default/Inactive/Cancelled | #F3F4F6 | #6B7280 | #9CA3AF |
| Info | #EBF5FF | #1C64F2 | #1C64F2 |

### 3.4 Forms

- Input height: 40px (py-2 px-3)
- Border: 1px solid #D1D5DB
- Border radius: 8px
- Focus: ring-2 ring-[#1C64F2], border-transparent
- Label: Inter 500, 14px, #374151, required marker in #C81E1E
- Error state: border-[#C81E1E], error text below
- Helper text: #9CA3AF, 12px

### 3.5 Tables

- Header: bg-[#F9FAFB], text-xs uppercase tracking-wider, #9CA3AF
- Row hover: bg-[#F9FAFB]
- Row divider: border-b border-[#F3F4F6]
- Cell padding: 12px vertical, 16px horizontal
- No outer border on table

### 3.6 Navigation Sidebar

- Background: #111928 (dark)
- Width: 256px
- Nav items: 16px icon + text, rounded-[8px]
- Active: bg-[#1C64F2] text-white
- Inactive: text-[#9CA3AF] hover:bg-white/8 hover:text-white
- Badge: count indicator on nav items
- Footer: Sign out button

---

## 4. Portal-Specific Colors

| Portal | Sidebar Logo BG | Accent Color |
|--------|----------------|-------------|
| Admin | #7C3AED (Purple) | Purple |
| Parent | #1C64F2 (Blue) | Blue |
| Hospital | #0891B2 (Teal) | Teal |

---

## 5. Icons

Use Material Symbols or SVG icons (stroke-based, 2px stroke width).

Common icons:
- Dashboard: Grid 4 squares
- Children: Users/People
- Vaccines: Syringe or pill
- Hospitals: Building with cross
- Appointments: Calendar
- Reports: Chart/Document
- Notifications: Bell
- Profile: Person
- Settings: Gear
- Search: Magnifying glass

---

*Document: GUI Standards v1.0 — Vaccination Management System*
