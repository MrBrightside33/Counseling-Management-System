# Counseling app UI (Laravel Blade)

The Next.js app in `/frontend` has been **replaced for day-to-day use** by Blade views under `backend/resources/views`. The API in `routes/api.php` can stay for future SPA/mobile clients if needed.

## Run the full stack (one server)

From the `backend` directory:

```bash
composer install
npm install
npm run build
php artisan serve
```

Open **http://127.0.0.1:8000** — you should see the dashboard (no separate Next port).

For asset hot-reload during development:

```bash
npm run dev
```

(in a second terminal, still in `backend`)

## Routes (Blade)

| URL | Page |
|-----|------|
| `/` | Dashboard |
| `/students` | Students list |
| `/students/create` | Add student (POST `/students`) |
| `/appointments` | Appointments |
| `/appointments/schedule` | Schedule appointment (POST `/appointments`) |
| `/counselors` | Counselors |
| `/counselors/create` | Add counselor (POST `/counselors`) |
| `/reports` | Reports & analytics (simplified vs old charts) |

## Data

Demo data lives in `config/cpc_mock.php`. Replace with Eloquent models and migrations when you wire the database.

## What was not ported 1:1

- **shadcn / Radix React components** — rebuilt with plain HTML + Tailwind in Blade.
- **Recharts** — reports use tables and simple CSS bars instead.
- **Next.js routing** — use Laravel route names (`route('students.index')`, etc.).

You can delete or archive the `frontend` folder once you no longer need it for reference.
