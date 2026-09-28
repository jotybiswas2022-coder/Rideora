# Rideora — Vehicle Rental Platform

**Your Ride, Your Way.**

A complete vehicle rental platform with customer booking and **manual payment verification**, built with
**PHP + Laravel 12 + Blade + vanilla JavaScript only**.

There is **no** React, Vue, Angular, Tailwind, Bootstrap, jQuery, Livewire or Inertia, and **no separate CSS
files** — every page carries its own `<style>` block.

---

## Feature overview

### Customer
- Registration, login, logout with hashed passwords and rate limited sign in
- Browse, search, filter (category, brand, fuel, transmission, seats, price, location, dates) and sort vehicles
- Vehicle details with image gallery, specifications, live availability check and server-side price quote
- Booking creation with overlap protection (no double booking), auto rental duration and automatic long-rental discounts
- Manual payment: choose bKash / Nagad / bank transfer, submit transaction ID and upload a payment screenshot
- Booking status, payment status, booking timeline, cancel eligible bookings
- Reviews for completed rentals, notifications for every status change, profile and password management

### Admin
- Dashboard: fleet, customers, bookings, pending payments, completed rentals, verified revenue + 6 month revenue chart
- Vehicle management: create/edit/delete, multiple images with primary selection, pricing, status (available/booked/maintenance/inactive)
- Category, booking (status + notes), customer (activate/deactivate/delete) management
- Payment verification queue: view transaction ID, amount comparison warning and proof screenshot, then **verify** or **reject** with a reason
- Review moderation, payment method accounts, website settings (contact details, social links, currency, about copy)

---

## Requirements

- PHP 8.2 or newer with `pdo_mysql`, `fileinfo`, `mbstring`, `openssl`
- MySQL 5.7+ / MariaDB (SQLite also works)
- Composer 2

## Installation

```bash
# 1. Install PHP dependencies
composer install

# 2. Create the environment file and application key
cp .env.example .env          # Windows: copy .env.example .env
php artisan key:generate

# 3. Create the database, then point .env at it
#    CREATE DATABASE rideora CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# 4. Create the schema and seed demo data
php artisan migrate --seed

# 5. (Optional) make the public disk reachable when serving with Apache/nginx
php artisan storage:link

# 6. Start the application
php artisan serve
```

Then open <http://localhost:8000>.

Uploaded images and payment proofs are streamed through the application (`/media/...`), so they render
correctly whether you use `php artisan serve`, Apache or nginx.

### Demo accounts (local development only)

| Role     | Email                 | Password      |
|----------|-----------------------|---------------|
| Admin    | `admin@rideora.test`  | `admin12345`  |
| Customer | `rakib@example.com`   | `password123` |
| Customer | `nusrat@example.com`  | `password123` |
| Customer | `sadia@example.com`   | `password123` |

Admin credentials come from `RIDEORA_ADMIN_EMAIL` / `RIDEORA_ADMIN_PASSWORD` in `.env`. Change them before
any deployment, and set `RIDEORA_SEED_DEMO=false` to skip the demo bookings.

---

## The manual payment flow

1. The customer creates a booking. The total is always calculated **on the server**:
   `base rental (days/hours) + security deposit − tiered long-rental discount`.
2. The customer is sent to the payment page, which shows the booking summary, the amount and the admin
   configured bKash / Nagad / bank account details.
3. The customer submits the payment method, amount, transaction ID and a screenshot (JPG/PNG/WEBP, max 3 MB).
   - `payments.status` becomes `pending`
   - `bookings.payment_status` becomes `pending`, `bookings.booking_status` becomes `payment_submitted`
   - The customer and every administrator receive a notification
4. An administrator reviews the submission (transaction ID, amount comparison, screenshot) and either
   - **verifies** → `payments.status = verified`, `booking.payment_status = paid`, `booking.booking_status = confirmed`, or
   - **rejects** with a written reason → `payments.status = rejected`, `booking.payment_status = rejected`,
     `booking.booking_status = pending` so the customer can submit a valid payment again.
5. Duplicate transaction IDs for the same method are blocked while a payment is pending or verified.

## Booking and availability rules

- Overlapping `pending`, `payment_submitted`, `confirmed` and `ongoing` bookings block a vehicle for those dates
  (day granularity: `existing.pickup < new.return AND existing.return > new.pickup`).
- `cancelled`, `rejected` and `completed` bookings never block new dates.
- Only `available` and `booked` vehicles appear on the public website; `maintenance` and `inactive` are hidden.
- Booking codes follow `VR-YYYYMMDD-0001` and are generated sequentially per day.
- Discounts: 5% from 7 days, 10% from 14 days, 15% from 30 days.

---

## Project structure

```
app/
  Http/
    Controllers/
      Auth/               LoginController, RegisterController
      Admin/              Dashboard, Vehicle, Category, Booking, Payment,
                          User, Review, PaymentMethod, Setting controllers
      BookingController   customer bookings, cancellation
      PaymentController   manual payment submission + confirmation
      VehicleController   listing, filters, availability JSON endpoint
      ReviewController, NotificationController, ProfileController,
      CustomerDashboardController, ContactController, HomeController, MediaController
    Middleware/           AdminMiddleware, CustomerMiddleware
    Requests/             Auth/, Admin/ and customer form requests
  Models/                 User, Vehicle, VehicleCategory, VehicleImage, Booking,
                          Payment, PaymentMethod, Review, Notification, Setting
  Policies/               BookingPolicy, PaymentPolicy
  Services/               BookingService (pricing + availability), NotificationService
  Support/                FileUploader (secure uploads), helpers.php (bdt(), setting(), …)
resources/views/
  frontend/               layout, home, vehicles, bookings, payments, profile,
                          notifications, reviews, about, contact, auth/
  admin/                   layout (sidebar + topbar), dashboard, vehicles, categories,
                          bookings, payments, users, reviews, payment-methods, settings
  errors/                  403, 404, 419, 500, 503
routes/
  web.php                 public + customer routes
  admin.php               admin routes
```

### Technology notes

- All styling lives inside `<style>` blocks in each Blade page, using one shared visual language
  (primary `#2563EB`, dark `#0F172A`, light `#F8FAFC`, text `#1E293B`, success `#16A34A`,
  warning `#F59E0B`, danger `#DC2626`). No external CSS frameworks, fonts or icon fonts are loaded.
- JavaScript is vanilla only: mobile navigation, notification dropdown, image previews, galleries,
  date validation, live price calculation, filter interactions and delete confirmations.
- Security: CSRF tokens on every form, Laravel validation via form requests, authentication and role
  middleware, policy based ownership checks, bcrypt password hashing, MIME + size validated uploads with
  generated filenames, and server-side recalculation of every amount.

---

## Testing

```bash
php artisan test
```

The suite covers authentication and role redirects, vehicle browsing/filtering, the availability endpoint,
booking creation with server-side pricing and double-booking prevention, the full manual payment lifecycle
(submission, verification, rejection, resubmission), authorization boundaries and admin management screens
(56 tests, 277 assertions).

## Useful artisan commands

```bash
php artisan migrate:fresh --seed    # rebuild the database with demo data
php artisan route:list              # inspect every named route
php artisan optimize:clear          # clear config, route, view and cache
```
