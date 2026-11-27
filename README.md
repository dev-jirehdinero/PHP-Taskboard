# PHP TaskBoard

A secure task manager built with plain PHP 8 and MySQL, with user accounts, login sessions and an admin area. Built as a hands-on project for learning PHP, with security practices included from the start.

> Kanban boards for teams are an optional extension (see [Roadmap](#roadmap)).

## Features

- **Accounts:** register, log in, log out, change password
- **Tasks (CRUD):** create, view, edit, mark done / not done
- **Search, sort and pagination** on the task list (sort columns use a whitelist)
- **Soft delete:** deleted tasks go to a Trash page and can be restored
- **Roles:** `user` and `admin`; admins get a Users page
- **Login protection:** throttling after repeated failed attempts, and a permanent login history (`login_log`)
- **Each user sees only their own tasks**

## Tech stack

- PHP 8.1+ (developed on 8.4), `declare(strict_types=1)`
- MySQL / MariaDB via PDO (prepared statements)
- Plain HTML + CSS, no framework
- Developed with [Laragon](https://laragon.org/) on Windows (works on any Apache/Nginx + PHP + MySQL stack)

## Project structure

```
taskboard/
├── public/              # web root (point the virtual host here)
│   ├── index.php        # task list
│   ├── login.php  register.php  logout.php  profile.php
│   ├── create.php  edit.php  toggle.php  delete.php
│   ├── trash.php  restore.php
│   ├── admin_users.php  # admin only
│   └── style.css
├── src/                 # code that must NOT be web-accessible
│   ├── bootstrap.php    # sessions, security headers, includes
│   ├── db.php           # PDO connection
│   ├── helpers.php      # e(), CSRF, flash messages, auth helpers
│   ├── tasks.php        # task queries and validation
│   └── views/           # header.php, footer.php, task_form.php
└── storage/
```

## Getting started (Laragon)

1. Clone into Laragon's web folder:
   ```
   cd C:\laragon\www
   git clone https://github.com/dev-jirehdinero/PHP-Taskboard.git taskboard
   ```
2. Start Laragon (Apache + MySQL). Laragon creates `http://taskboard.test` automatically, or run:
   ```
   php -S localhost:8000 -t public
   ```
3. Create a database named `taskboard` in phpMyAdmin and run the SQL in [Database](#database).
4. Check the credentials in `src/db.php` (Laragon default: user `root`, empty password).
5. Open the site, register an account, then make yourself admin:
   ```sql
   UPDATE users SET role = 'admin' WHERE email = 'you@example.com';
   ```

## Database

Core tables: `users`, `tasks`, `login_attempts` (throttling) and `login_log` (permanent history).
Key columns to note:

- `users.role` — `VARCHAR(20) NOT NULL DEFAULT 'user'`
- `tasks.deleted_at` — `TIMESTAMP NULL DEFAULT NULL` (soft delete)

Put your full `CREATE TABLE` statements (or an exported `schema.sql`) in the repo and link them here.

## Security

| Threat | Protection |
|---|---|
| SQL injection | PDO prepared statements everywhere; ORDER BY columns come from a whitelist |
| XSS | All output escaped with `htmlspecialchars` through the `e()` helper |
| CSRF | Per-session token on every POST form, compared with `hash_equals` |
| Password theft | `password_hash` / `password_verify` (bcrypt/argon2), `password_needs_rehash` |
| Session fixation / hijacking | `session_regenerate_id(true)` at login, HttpOnly + SameSite cookies, strict mode, idle timeout (30 min) |
| Brute force | Failed-login throttling by email and IP |
| IDOR (reading others' data) | Every query includes `AND user_id = :uid` |
| Clickjacking / sniffing | `X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy` headers |
| Source exposure | Only `public/` is the web root; `src/` is outside it |

Before deploying: enable HTTPS (so the `Secure` cookie flag applies), turn `display_errors` off, and use a dedicated MySQL user instead of `root`.

## Roadmap

- [ ] Kanban boards per team (multiple boards, 8 Jira-style columns, drag & drop, priority swimlanes, comments and activity history)
- [ ] Simple department to-do boards (Admin, Purchasing) with a "Waiting" column
- [ ] Due dates and overdue filter
- [ ] "My tasks" page across all boards
- [ ] Email notifications

## What I learned

PDO and prepared statements, sessions and cookies, CSRF protection, password hashing, authorization checks, POST/Redirect/Get, soft delete and audit logging.

## TaskBoard Version2 - Released
Check new features added to our taskboard here: https://drive.google.com/drive/folders/1R4f7Me25hmVXfbHW47AFOEtqQjtG24DL?usp=drive_link
Contact me if you are interested to Taskboard Version 2.

## License

This project is licensed under the MIT License. See the LICENSE file for details.

## Author
Jireh Dinero