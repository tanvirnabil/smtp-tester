# SMTP Tester

A simple, mobile-friendly web tool to manage multiple SMTP configurations, send test emails, manage reusable templates, and view send logs — all from a browser.

![PHP](https://img.shields.io/badge/PHP-7.4%2B-blue) ![SQLite](https://img.shields.io/badge/SQLite-3-green) ![PHPMailer](https://img.shields.io/badge/PHPMailer-6-orange)

---

## Features

- **Multiple SMTP configs** — add, edit, view (with copy), and delete
- **Send test emails** — pick a config, pick a template or write custom body, send and see result instantly
- **Clear error messages** — auth failure, connection refused, invalid recipient (550), SSL/TLS issues
- **Email templates** — 3 built-in defaults (plain text, HTML welcome, HTML alert) + add your own
- **Send logs** — last 50 sends with status and full error detail
- **From Name / From Email** — send as a different address/name than the SMTP username
- **Mobile friendly** — works on any screen size

---

## Requirements

- PHP 7.4 or higher
- PHP extensions: `pdo`, `pdo_sqlite`, `openssl`
- Composer
- Apache or Nginx with PHP enabled

---

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/your-username/smtp-tester.git
cd smtp-tester
```

### 2. Install dependencies

```bash
composer install
```

> If Composer is not installed:
> ```bash
> curl -sS https://getcomposer.org/installer | php
> sudo mv composer.phar /usr/local/bin/composer
> ```

### 3. Set permissions

The web server needs write access to create the SQLite database file:

```bash
chown -R www-data:www-data .
chmod 755 .
```

### 4. Install required PHP extensions (if missing)

**Ubuntu/Debian:**
```bash
sudo apt install php-sqlite3 php-pdo
sudo phpenmod pdo_sqlite
sudo systemctl reload apache2
```

**CentOS/RHEL:**
```bash
sudo yum install php-pdo php-pdo_sqlite
sudo systemctl restart httpd
```

### 5. Configure your web server

**Apache** — place the folder inside your web root (e.g. `/var/www/html/smtp-tester/`) and access via `http://your-server/smtp-tester/`.

No `.htaccess` changes needed.

**Nginx** — add a location block:
```nginx
location /smtp-tester/ {
    root /var/www/html;
    index index.php;
    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php7.4-fpm.sock;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}
```

---

## Database

The SQLite database (`smtp_tester.db`) is **auto-created on first request** — no manual setup needed.

The schema is documented in [`schema.sql`](schema.sql) for reference.

Three default email templates are seeded automatically on first run.

---

## File Structure

```
smtp-tester/
├── index.php       # Main UI (single-page, tab-based)
├── send.php        # AJAX API endpoint
├── db.php          # SQLite init, schema creation, default seed
├── schema.sql      # DB schema reference
├── composer.json   # PHPMailer dependency
└── vendor/         # Composer packages (not in repo)
```

---

## Usage

### Adding an SMTP Config

Go to **SMTP Configs** tab and fill in:

| Field | Example |
|---|---|
| Config Name | Gmail, SendGrid, Office365 |
| SMTP Host | `smtp.gmail.com` |
| Port | `587` |
| Encryption | TLS (recommended) |
| Username | `you@gmail.com` |
| Password | App password or SMTP password |
| From Email | `noreply@yourdomain.com` *(optional)* |
| From Name | `My App` *(optional)* |

> **Note:** If your mail server enforces sender policy (most cPanel/Postfix servers do), the **From Email must match the SMTP username**. Use a service like SendGrid or Mailgun if you need to send from a different address.

### Common SMTP Settings

| Provider | Host | Port | Encryption |
|---|---|---|---|
| Gmail | `smtp.gmail.com` | 587 | TLS |
| Gmail (SSL) | `smtp.gmail.com` | 465 | SSL |
| Outlook/Office365 | `smtp.office365.com` | 587 | TLS |
| Yahoo | `smtp.mail.yahoo.com` | 587 | TLS |
| SendGrid | `smtp.sendgrid.net` | 587 | TLS |
| Mailgun | `smtp.mailgun.org` | 587 | TLS |
| cPanel default | `mail.yourdomain.com` | 587 | TLS |

> **Gmail users:** You must use an [App Password](https://myaccount.google.com/apppasswords), not your regular password. Enable 2FA first.

### Sending a Test Email

1. Go to **Send Test** tab
2. Select an SMTP config
3. Enter recipient address
4. Optionally select a template (auto-fills subject and body)
5. Click **Send Email**

Result is shown immediately with a success or detailed error message.

### Sharing Config with a Developer

In the **SMTP Configs** tab, click **View** on any config to open a modal showing all details including the password. Click **Copy to Clipboard** to copy everything at once.

---

## Error Messages

| Error | Meaning |
|---|---|
| Authentication failed | Wrong username or password |
| Connection failed | Wrong host/port or firewall blocking the port |
| Recipient rejected (550) | The recipient email address may not exist |
| Encryption error | Try switching between TLS/SSL or changing port |
| Sender address rejected | From Email doesn't match SMTP username (server policy) |

---

## Security Notes

- This tool is intended for **internal/development use only**
- SMTP passwords are stored in plain text in the SQLite DB — do not expose this tool publicly
- Protect with HTTP Basic Auth or IP restriction if on a shared server:

```apache
# .htaccess
AuthType Basic
AuthName "SMTP Tester"
AuthUserFile /etc/apache2/.htpasswd
Require valid-user
```

```bash
htpasswd -c /etc/apache2/.htpasswd yourusername
```

---

## License

MIT
