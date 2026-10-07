# ProJobs Portal

> Job board and recruitment marketplace

**Live demo:** https://projobs-portal.freedev.app

## Screenshots

![Home](docs/screenshots/home.png)
![Jobs](docs/screenshots/jobs.png)
![Admin Dashboard](docs/screenshots/admin-dashboard.png)

## Features

- Public job board with keyword, location, category, type and work-mode filters
- Job details, apply with cover letter, save/bookmark jobs
- Job seeker profile with photo, CV, skills and application tracking
- Admin panel: jobs, companies, categories, applications and seekers
- CSRF tokens, PDO prepared statements, safe file uploads

## Tech stack

PHP 8 · MySQL (PDO) · Bootstrap 5

## Demo login (local install)

| Role | Email / user | Password |
|---|---|---|
| Admin | `admin@projobs.local` | `Admin123!` |
| Job seeker | `abebe@example.com` | `Seeker123!` |

These are the default credentials of the seed data. The live demo's admin password is private.

## Run locally (XAMPP)

1. Copy this folder to `C:\xampp\htdocs\ProJobs_Portal`
2. Start **Apache** and **MySQL** in XAMPP
3. Open phpMyAdmin → **Import** → `database/projobs_portal.sql` (optional extra sample data: `database/seed_us_jobs.sql`, import it after)
4. Check the database settings in `config/config.php` (default: host `localhost`, user `root`, empty password)
5. Open `http://localhost/ProJobs_Portal/`

### Deploying to shared hosting

Create an empty database in your hosting panel, delete the `CREATE DATABASE` / `USE` / `DROP DATABASE` lines at the top of `database/projobs_portal.sql`, import it, then put the host's database name, user and password in `config/config.php`.

## Notes

Portfolio / demo project. Before real production use add HTTPS, rate limiting, audit logs, backups and environment-based configuration.

## Author

Built by [natnaelameha213](https://github.com/natnaelameha213).
