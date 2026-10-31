# PRD: Cashier & Sales Recording App for Toko Sumber Urip

**Version:** 1.0  |  **Stack:** Laravel 13 + Tailwind CSS  |  **Audience:** AI Agent (developer)

## 0. Mandatory Instructions for the AI Agent

1. **Read `DESIGN.md` in the project root first.** All frontend visuals (colors, typography, spacing, components, style) MUST follow `DESIGN.md`. If this PRD and `DESIGN.md` conflict on visual matters, `DESIGN.md` wins.
2. Laravel 13 is already installed in the project root. **Do not reinstall Laravel**; build on top of the existing project.
3. Keep the code **simple and easy to understand**: use the default Laravel structure, with no extra layers (no Repository, Service, DTO, or architecture packages).
4. Use **Blade + Tailwind CSS** (via Vite). **Alpine.js** is allowed for light interactivity (cashier cart) and **Chart.js** for charts. Do not use Livewire, Inertia, Vue, or React.
5. Use packages and versions compatible with Laravel 13 and the installed PHP version. Check `composer.json` before starting.
6. **Language rule:** this PRD and all code (variables, methods, comments, commit messages, README) are in **English**. All **user-facing text** (labels, buttons, menus, validation messages, flash messages, empty states) MUST be in **Bahasa Indonesia**, because the end users are Indonesian. Set locale `id` and timezone `Asia/Jakarta`. Where this PRD quotes UI text, it is given in Indonesian on purpose.
7. Database enum-like values (`tunai`, `transfer`, `selesai`, `batal`, `masuk`, `keluar`) and route URLs (`/kasir`, `/transaksi`, `/produk`, `/stok`) stay in Indonesian exactly as written in this PRD.
8. Work in the order given in **Implementation Phases (section 13)** and make sure each phase runs before moving on.

## 1. Product Summary

Toko Sumber Urip is a drinking-water gallon refill shop. The owner/cashier needs a web application to record daily sales, serve customers at the cashier, manage product stock, and view simple sales trends.

The app has **one user type only** (no roles), and login uses **username and password**.

## 2. Goals and Non-Goals

**Goals**

- Speed up checkout at the cashier.
- Record every sale neatly and keep it traceable.
- Keep stock accurate, and reduce it automatically when items are sold.
- Let the owner see sales summaries and charts quickly.

**Non-Goals (out of scope)**

- Multi-role / multi-user management, registration, forgot password.
- Multiple branches, customers/members, receivables/debts, discounts and promotions.
- Digital payment integration, direct thermal printer integration, native mobile app.
- Full financial reports (profit and loss, cost of goods).

## 3. Users

| User | Description |
| --- | --- |
| Admin/Cashier | The only user. Can access every feature after logging in. |

## 4. Tech Stack

| Component | Choice |
| --- | --- |
| Backend | Laravel 13 (already installed) |
| Database | MySQL (or SQLite for development) |
| Frontend | Blade + Tailwind CSS via Vite |
| Interactivity | Alpine.js (optional, lightweight) |
| Charts | Chart.js (via npm) |
| Auth | Laravel session auth, login by `username` |

## 5. Functional Requirements

### 5.1 Authentication (Priority: Must)

- Login page with **username** and **password** fields.
- Logout via a button in the navbar/sidebar.
- Every page except login requires authentication (`auth` middleware). Unauthenticated users are redirected to the login page.
- **No** registration, forgot password, email verification, or roles. A single account is created by the seeder (username `admin`, initial password `password`, with a note in the README to change it).
- The `email` column is not used for login. Remove it or make it nullable in the `users` table.
- Failed login message (Indonesian): `Username atau password salah.`

### 5.2 Dashboard and Sales Chart (Must)

The home page after login contains:

- **Today summary cards:** total revenue, number of transactions, number of items sold.
- **Simple sales chart** (Chart.js): bar or line chart of revenue per day, with a **7-day** and **30-day** range switch (default 7 days).
- **Top products** (top 5, last 30 days) as a simple list.
- **Low stock warning:** list of products whose stock is less than or equal to the minimum stock.
- Only transactions with status `selesai` are counted. Transactions with status `batal` are excluded.

### 5.3 Cashier / POS (Must)

The cashier page is a single screen with no page reloads:

- Active products are shown as clickable cards/buttons, with a product search box.
- Clicking a product adds it to the **cart** (initial qty 1). Clicking again increases the qty.
- In the cart: change qty (+/- buttons and number input), remove an item, clear the cart.
- Show per-item subtotal and the **grand total** in real time.
- **Payment method** input: `Tunai` or `Transfer`.
- For cash: enter **amount received**; the system calculates **change** automatically. The pay button is disabled while the amount is less than the total. Provide quick amount buttons (Uang Pas, 50.000, 100.000).
- Optional **note** input (for example customer name or delivery address).
- The **Bayar / Simpan Transaksi** button posts the data to the server. After success, show a simple **printable receipt** (`window.print()` with print CSS) and clear the cart.
- Out-of-stock products are marked and cannot be added beyond available stock (only for products with stock tracking enabled).

### 5.4 Transaction List (Must)

- A table of transactions, newest first, with columns: invoice number, date and time, item count, total, payment method, status, actions.
- **Filter** by date range (default today) and **search** by invoice number.
- Pagination (15 rows per page).
- Show the total revenue of the filtered result above the table.
- **Transaction detail page:** item breakdown, price, qty, subtotal, total, paid amount, change, note, and a button to reprint the receipt.
- **Cancel transaction** (with confirmation): status becomes `batal` and item stock is returned automatically. An already cancelled transaction cannot be cancelled again. Transactions cannot be permanently deleted.

### 5.5 Product Management (Must)

Simple product CRUD:

- Fields: name, unit (for example galon, pcs), selling price, stock, minimum stock, **track stock** (yes/no), active status.
- **Track stock = no** is used for services such as **Isi Ulang Air Galon** (no physical stock). These products never reduce stock and never appear in low stock warnings.
- Products already used in transactions cannot be deleted; they can only be deactivated.
- Product list with name search and pagination.
- After a product is created, its stock is not edited directly through the product form. Stock changes only happen through Add Stock or transactions (see 5.6). The initial stock is set once when the product is created.

### 5.6 Add Stock (Must)

- An **Add Stock** page/form: choose a product (only products with stock tracking), enter quantity received (integer > 0), date, and an optional note (for example supplier name).
- On save, the product stock increases and a record is written to the `stock_movements` table.
- A **Stock History** page lists all movements: date, product, type (`masuk`/`keluar`), quantity, note. Filterable by product.
- `keluar` movements are created automatically when a transaction happens. `masuk` movements are created automatically when a transaction is cancelled (with a cancellation note) or when stock is added.

## 6. Database Schema

Use standard Laravel migrations. Money values are stored as **integer rupiah** (no decimals).

**users** (modify the default table)

| Column | Type | Notes |
| --- | --- | --- |
| id | bigint | PK |
| name | string | Display name |
| username | string, unique | Used for login |
| password | string | Hashed |
| remember\_token, timestamps | - | Default |

**products**

| Column | Type | Notes |
| --- | --- | --- |
| id | bigint | PK |
| name | string | Product name |
| unit | string | Unit (galon, pcs) |
| price | unsigned integer | Selling price |
| stock | integer, default 0 | Current stock |
| min\_stock | integer, default 0 | Low stock threshold |
| track\_stock | boolean, default true | Whether stock is tracked |
| is\_active | boolean, default true | Shown in cashier or not |
| timestamps | - | - |

**transactions**

| Column | Type | Notes |
| --- | --- | --- |
| id | bigint | PK |
| invoice\_number | string, unique | Format `INV-YYYYMMDD-0001` (sequence resets daily) |
| total | unsigned integer | Grand total |
| payment\_method | string | `tunai` or `transfer` |
| paid\_amount | unsigned integer | Amount received (transfer: equals total) |
| change\_amount | unsigned integer | Change |
| status | string, default `selesai` | `selesai` or `batal` |
| note | text, nullable | Note |
| cancelled\_at | timestamp, nullable | Cancellation time |
| timestamps | - | created\_at = transaction time |

**transaction\_items**

| Column | Type | Notes |
| --- | --- | --- |
| id | bigint | PK |
| transaction\_id | FK | To transactions |
| product\_id | FK | To products |
| product\_name | string | Name snapshot at sale time |
| price | unsigned integer | Price snapshot at sale time |
| quantity | unsigned integer | Quantity |
| subtotal | unsigned integer | price x quantity |

**stock\_movements**

| Column | Type | Notes |
| --- | --- | --- |
| id | bigint | PK |
| product\_id | FK | To products |
| type | string | `masuk` or `keluar` |
| quantity | unsigned integer | Quantity |
| note | string, nullable | Description (supplier, invoice number, cancellation) |
| transaction\_id | FK, nullable | Set when it originates from a transaction |
| movement\_date | date | Movement date |
| timestamps | - | - |

Add indexes on `transactions.created_at`, `transactions.status`, and `stock_movements.product_id`.

## 7. Routes

| Method | URL | Controller@method | Purpose |
| --- | --- | --- | --- |
| GET | /login | AuthController@showLogin | Login form |
| POST | /login | AuthController@login | Process login |
| POST | /logout | AuthController@logout | Logout |
| GET | / | DashboardController@index | Dashboard and chart |
| GET | /dashboard/chart?range=7 | DashboardController@chart | Chart data (JSON) |
| GET | /kasir | CashierController@index | Cashier page |
| POST | /kasir | CashierController@store | Save transaction |
| GET | /transaksi | TransactionController@index | Transaction list |
| GET | /transaksi/{transaction} | TransactionController@show | Detail and receipt |
| POST | /transaksi/{transaction}/batal | TransactionController@cancel | Cancel transaction |
| Resource | /produk | ProductController | Product CRUD (no show) |
| GET | /stok | StockController@index | Stock history |
| GET | /stok/tambah | StockController@create | Add stock form |
| POST | /stok | StockController@store | Save added stock |

All routes except `/login` are protected by the `auth` middleware.

## 8. Folder Structure (Simple)

Use the default Laravel structure. Only create or modify the following files:

```
app/
  Http/Controllers/
    AuthController.php
    DashboardController.php
    CashierController.php
    TransactionController.php
    ProductController.php
    StockController.php
  Models/
    User.php
    Product.php
    Transaction.php
    TransactionItem.php
    StockMovement.php
database/
  migrations/       (users, products, transactions, transaction_items, stock_movements)
  seeders/
    DatabaseSeeder.php   (1 admin user + sample products)
resources/
  views/
    layouts/app.blade.php        (sidebar/navbar + content slot)
    auth/login.blade.php
    dashboard.blade.php
    cashier/index.blade.php
    cashier/receipt.blade.php    (receipt, also reused in transaction detail)
    transactions/index.blade.php
    transactions/show.blade.php
    products/index.blade.php
    products/create.blade.php
    products/edit.blade.php
    products/_form.blade.php
    stocks/index.blade.php
    stocks/create.blade.php
  css/app.css        (Tailwind + tokens from DESIGN.md)
  js/app.js          (Alpine, Chart.js)
routes/
  web.php
```

**Structure rules:**

- Write validation directly in controllers with `$request->validate()`. No Form Requests needed.
- Business logic (calculate totals, reduce stock, cancel) lives in controllers or as methods on Models. No Service or Repository classes.
- Use Blade components (`resources/views/components`) only for truly repeated UI (for example button, card, input).

## 9. UI/UX Guidelines

- Follow **`DESIGN.md`** for every visual decision. Do not invent a separate style. If `DESIGN.md` does not cover a component, derive its style from the closest component that it does cover.
- Tailwind CSS is used directly in Blade. Color and font tokens from `DESIGN.md` are defined in the Tailwind theme configuration for consistency.
- Main layout: sidebar (desktop) or bottom menu/hamburger (mobile) with menu items **Dashboard, Kasir, Transaksi, Produk, Stok** and a **Logout** button.
- The cashier page must be **comfortable on tablets and touch screens**: large touch targets, clear text, no excessive scrolling.
- All pages are **responsive** (mobile, tablet, desktop).
- Money format: `Rp 15.000` (dot as thousands separator). Date format: `31 Okt 2026, 14:30`.
- Show success/error notifications (flash messages) after actions, and friendly empty states on tables without data.

## 10. Business Rules

1. **Checkout must be atomic:** use `DB::transaction()`. When saving a transaction, lock product rows (`lockForUpdate`), validate stock is sufficient, create `transactions` and `transaction_items`, reduce stock, and write `stock_movements` of type `keluar`. If any step fails, everything rolls back.
2. **Validate on the server, not only in the browser:** prices always come from the database, never from client input. The total is recalculated on the server.
3. Stock must never go negative. If stock is insufficient, show an Indonesian message that names the product.
4. Products with `track_stock = false` do not reduce stock and do not create `stock_movements`.
5. For cash payments, `paid_amount` must be >= total. `change_amount = paid_amount - total`.
6. The invoice number is generated on the server inside the database transaction to avoid duplicates.
7. Cancelling a transaction returns stock (tracked products only) and writes `stock_movements` of type `masuk`.
8. Product name and price snapshots are stored in `transaction_items` so history does not change when a product price changes.
9. Basic security: CSRF enabled, passwords hashed, all input validated, queries through Eloquent/Query Builder (no raw queries built from user input).

## 11. Seed Data

- 1 user: name `Administrator`, username `admin`, password `password`.
- Sample products:

| Name | Unit | Price | Track Stock | Stock |
| --- | --- | --- | --- | --- |
| Isi Ulang Air Galon | galon | 6.000 | No | - |
| Galon Kosong Baru | pcs | 45.000 | Yes | 20 |
| Air Galon + Galon (Paket) | pcs | 65.000 | Yes | 10 |
| Segel/Tutup Galon | pcs | 2.000 | Yes | 100 |

The prices above are examples only; the shop owner can change them on the Product page.

## 12. Non-Functional Requirements

- **Performance:** the dashboard and cashier pages load in under 2 seconds under normal conditions. Avoid N+1 queries (use `with()`).
- **Maintainability:** clean code following Laravel conventions (PSR-12), clear variable and method names, short comments on important logic (checkout, cancellation).
- **Browser support:** latest Chrome, Edge, Safari, and Firefox.
- **Backup:** no dedicated feature needed; document in the README how to export the database.

## 13. Implementation Phases

| Phase | Work | Result |
| --- | --- | --- |
| 1. Setup | Read DESIGN.md, install Tailwind + Vite (if missing), Alpine.js, Chart.js, set locale and timezone, set theme tokens from DESIGN.md | Project ready, empty pages styled per DESIGN.md |
| 2. Database | Migrations and models for all tables, seeder for user and products | `php artisan migrate --seed` succeeds |
| 3. Auth and Layout | Username login/logout, auth middleware, main layout with navigation | Can log in and switch menus |
| 4. Products and Stock | Product CRUD, add stock form, stock history | Stock can be managed |
| 5. Cashier | Cashier page, cart, atomic checkout, printable receipt | Transactions saved, stock reduced |
| 6. Transactions | List, filter, detail, cancel transaction | History and cancellation work |
| 7. Dashboard | Summary cards, 7/30-day chart, top products, low stock | Dashboard works |
| 8. Finishing | Responsiveness, empty states, flash messages, manual testing, README | Ready to use |

## 14. Acceptance Criteria (Definition of Done)

- [ ] A user can log in with username and password, and cannot reach any other page without logging in.
- [ ] There is no registration, forgot password, or role feature.
- [ ] The cashier can add products to the cart, change qty, pay by cash/transfer, and the change is calculated correctly.
- [ ] After a transaction, stock of tracked products decreases and appears in the stock history. The Isi Ulang product does not reduce stock.
- [ ] A transaction cannot be created when stock is insufficient or cash received is too low.
- [ ] The transaction list can be filtered by date and searched by invoice number, and shows the total revenue of the filtered result.
- [ ] Cancelling a transaction changes its status, returns stock, and removes it from the chart and revenue figures.
- [ ] Add Stock increases product stock and appears in the stock history.
- [ ] The dashboard shows today's summary, 7-day and 30-day sales charts, top products, and low stock warnings.
- [ ] The UI is consistent with `DESIGN.md` and responsive on mobile, tablet, and desktop.
- [ ] `php artisan migrate:fresh --seed` runs without errors and the README explains installation and how to run the app.

## 15. Future Enhancements (Not Built Now)

Monthly reports and Excel/PDF export, discounts, customer data and gallon subscriptions, receivables/debts, delivery tracking, shop settings (name, address, logo on receipt), and multi-user with roles.
