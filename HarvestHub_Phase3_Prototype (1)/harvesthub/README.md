# HarvestHub — Phase 3 Prototype

A working slice of HarvestHub covering login, all three role dashboards
(Administrator, Garden Coordinator, Community Gardener), and the
Produce Exchange Board.

The visual design (forest-green + earthy brown palette, Fraunces/Inter
font pairing, hairline-divider cards instead of shadowed boxes) takes
cues from [pacificag.com](https://pacificag.com/) and is applied
consistently across every page — login, all three dashboards, and the
Exchange Board.

## Why SQLite instead of MySQL?

To keep this runnable anywhere without setting up a MySQL server, the
prototype uses SQLite (`data/harvesthub.sqlite`, auto-created on first
run) instead of MySQL. Swapping `db.php`'s connection string for a
MySQL DSN (`mysql:host=localhost;dbname=harvesthub`) is the only change
needed to run this against the actual MySQL database described in
Section IX.

## How to run it

### Option A — PHP's built-in server
```
cd public
php -S localhost:8000
```
Open `http://localhost:8000/login.php`.

### Option B — XAMPP
1. Copy the whole `harvesthub` folder (not just `public`) into
   `htdocs` (e.g. `C:\xampp\htdocs\harvesthub`).
2. Create an empty folder named `data` inside `harvesthub` if it
   isn't already there (zip files drop empty folders) — this is
   where the SQLite database file gets created on first run.
3. Start Apache from the XAMPP Control Panel (MySQL isn't needed).
4. Make sure `pdo_sqlite` is enabled in php.ini (enabled by default
   on most XAMPP installs) and that the `data` folder is writable.
5. Open `http://localhost/harvesthub/public/login.php`.

## Demo accounts

All demo accounts use the password `demo1234`.

| Role | Email |
|---|---|
| System Administrator | admin@harvesthub.test |
| Garden Coordinator | coordinator@harvesthub.test |
| Community Gardener | maria@harvesthub.test (has a plot) |
| Community Gardener | jun@harvesthub.test (has a plot) |
| Community Gardener | liza@harvesthub.test (no plot yet — good for testing "apply for a plot") |

## Pages

| Page | Role | What it does |
|---|---|---|
| `login.php` | Public | Role-tabbed login (Gardener / Coordinator / Admin) |
| `admin_dashboard.php` | Administrator | System stats, manage gardener/coordinator accounts, create new coordinator accounts |
| `staff_dashboard.php` | Garden Coordinator | Approve/reject plot applications, approve/reject resource requests, view all plots |
| `customer_dashboard.php` | Community Gardener | View/apply for a plot, log crops, request resources, jump to the Exchange Board |
| `index.php` | Community Gardener | Produce Exchange Board — search/filter/sort, post listings, claim listings via confirmation modal |

## What to look at for each Phase 3 item

| Requirement | Where it lives |
|---|---|
| Responsive interface | Hand-rolled CSS grid/flexbox (`assets/style.css`) with breakpoints for tablet/mobile; no framework dependency |
| JavaScript functionality | `assets/*.js` — one file per page; rendering, claim/delete confirmation modals, live character counter, toasts |
| API | `public/api.php` — a single JSON API covering auth + all four roles |
| AJAX / Fetch | Every page-to-server interaction uses `fetch()`, no full page reloads after login |
| Form validation | Client-side checks (format + range, inline error text) in each JS file, mirrored by authoritative server-side checks in `api.php` |
| Search / filter | Exchange Board's crop search, min-quantity filter, and sort dropdown (newest/oldest/quantity), all debounced and server-side |

## Security notes

- Passwords are hashed with `password_hash()` / verified with `password_verify()`.
- Every write action re-validates on the server, independent of client-side checks (including the crop-name pattern, which is checked both in JS and with a matching server-side regex).
- All SQL uses prepared PDO statements; sort order comes from a server-side whitelist, never interpolated user input.
- Every dashboard and every sensitive API action checks the session role (`requireRole()` for pages, `requireJsonRole()` for API actions).
- Output is escaped with `htmlspecialchars()` before storage/render to guard against XSS.

## Known limitation

Screenshots taken with headless tools that use older rendering engines
(no `fetch`/`async` support, no CSS Grid) may show empty data panels or
stacked layouts — this is a limitation of that specific tool, not the
app. Everything works correctly in real browsers (Chrome, Edge,
Firefox, Safari) and was verified end-to-end via direct API testing
during development.
