# The Digital Front

WordPress project for The Digital Front.

## Local Development Setup

### Prerequisites
- [Local by Flywheel](https://localwp.com/) (recommended) or any local WordPress environment

### Getting Started

1. **Clone the repo** into your local WordPress `public` directory:
   ```bash
   git clone https://github.com/TheDigitalFront/Project.git .
   ```

2. **Install WordPress** core files (not tracked in git). Create a new site in Local by Flywheel, then clone this repo into the `app/public/` directory.

3. **Configure `wp-config.php`**:
   - Copy `wp-config-sample.php` to `wp-config.php`
   - Update database credentials to match your local environment
   - Generate fresh salt keys at https://api.wordpress.org/secret-key/1.1/salt/

4. **Import the seed database** using WP Migrate DB:
   - Go to WP Admin → Tools → WP Migrate DB
   - Import `database/seed.sql` from the repo
   - Search-replace the site URL to match your local domain

   This gives you all required plugins activated, the child theme active, and base settings configured.

### Project Structure

```
wp-content/
├── themes/
│   └── the-digital-front-child/   # Custom child theme (all custom work goes here)
│       ├── style.css               # Theme header + custom CSS
│       └── functions.php           # Theme functions
└── plugins/                        # Required plugins (tracked in git)
```

### What's Tracked in Git
- `wp-content/themes/the-digital-front-child/` — custom child theme
- `wp-content/plugins/` — required plugins (excluding defaults)
- `.gitignore` and project docs

### What's NOT Tracked
- WordPress core files (`wp-admin/`, `wp-includes/`, etc.)
- `wp-config.php` (contains local credentials)
- `wp-content/uploads/` (media files)
- Default themes (`twenty*`)
