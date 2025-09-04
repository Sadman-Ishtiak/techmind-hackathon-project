# Pure PHP Auth (Email Verification via Gmail SMTP)

No Composer, no frameworks. Uses a tiny custom SMTP client (STARTTLS + AUTH LOGIN).

## Requirements
- PHP 7.4+ (works on PHP 8.x)
- MySQL 5.7+ / MariaDB
- Apache or Nginx pointing to this folder
- A Gmail account with **2-Step Verification ON** and a **16-character App Password** for "Mail".

> Google disabled basic password login for SMTP. You must create an App Password:
> Google Account → Security → 2-Step Verification → App Passwords → Select *Mail* on *Other* → copy the 16-char password.

## Setup
1. Import `db.sql` into your MySQL database.
2. Copy the folder to your web root (e.g., `htdocs/purephp-auth-gmail`).  
3. Open `config.php` and set:
   - `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`
   - `BASE_URL` to match your local URL
   - `$SMTP_CONFIG` with your Gmail address and **app password**
4. Visit `register.php`, create an account, and click the emailed verification link.
5. Then log in from `login.php`.

## Files
- `config.php` — DB, app, and SMTP config + PDO + session
- `lib/smtp.php` — minimal SMTP sender (HTML)
- `lib/helpers.php` — CSRF, small helpers, email template
- `register.php` — registration + send verify email
- `verify.php` — verifies token
- `login.php` — login (requires verified email)
- `dashboard.php` — protected page
- `logout.php` — sign out

## Notes
- Passwords use `password_hash()`/`password_verify()`.
- CSRF protection is included on forms.
- Tokens are random 32 bytes (hex-encoded).
- For production, serve over HTTPS, set secure cookie flags, and consider rate limiting/email queue.
