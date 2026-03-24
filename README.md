# The Digital Front

WordPress project for The Digital Front.

## Local Development Setup

### Prerequisites
- [Local by Flywheel](https://localwp.com/) (recommended) or any local WordPress environment

### Getting Started

1. **Create a new site** in Local by Flywheel (use default settings)

2. **Clone the repo** into the site's `app/public/` directory:
   ```bash
   cd ~/Local\ Sites/your-site-name/app/public
   git clone https://github.com/TheDigitalFront/Project.git .
   ```

3. **Configure `wp-config.php`**:
   - Copy `wp-config-sample.php` to `wp-config.php`
   - Update database credentials to match your Local site (usually `root`/`root`/`local`)
   - Generate fresh salt keys at https://api.wordpress.org/secret-key/1.1/salt/

4. **Import the seed database**:
   - Go to WP Admin → Tools → WP Migrate DB
   - Import `database/seed.sql`
   - Search-replace the old site URL with your local domain

   This activates all required plugins and the child theme automatically.

5. **Verify setup**:
   - Child theme "The Digital Front Child" should be active
   - "My Articles" should appear in the admin sidebar
   - ACF fields (Video Embed, Reading Time, Source URL) should appear when editing an Article

### Project Structure

```
wp-content/
├── themes/
│   └── the-digital-front-child/
│       ├── style.css          # Theme header + custom CSS
│       ├── functions.php      # CPT registration, ACF fields, theme setup
│       └── acf-json/          # ACF field group sync (auto-generated)
├── plugins/                   # Required plugins (tracked in git)
└── database/
    └── seed.sql               # Baseline database for onboarding
```

### Required Plugins
- Advanced Custom Fields (ACF)
- Custom Post Type UI
- Members
- WP-PageNavi
- Yoast SEO
- WPForms Lite
- WP Migrate DB
- Query Monitor

### Custom Post Types
- **Article** — Main content type for blog posts, news, and tutorials. Registered via `functions.php` (not database-dependent).

### ACF Fields (Article)
| Field | Type | Description |
|-------|------|-------------|
| `video_embed` | Text | Video embed URL (YouTube, Vimeo, etc.) |
| `reading_time` | Number | Estimated reading time in minutes |
| `source_url` | URL | Link to the original source |

### What's Tracked in Git
- Custom child theme (`wp-content/themes/the-digital-front-child/`)
- Required plugins (`wp-content/plugins/`)
- Seed database (`database/seed.sql`)
- Project docs and config samples

### What's NOT Tracked
- WordPress core files (`wp-admin/`, `wp-includes/`, etc.)
- `wp-config.php` (local credentials)
- `wp-content/uploads/` (media files)
- Default themes (`twenty*`)
- `wp-content/mu-plugins/` (auto-generated)

### Contributing

1. Pull latest from `main`
2. Make changes in the child theme or plugins
3. If you add/change ACF field groups, they auto-sync to `acf-json/` — commit those files
4. If you add a new CPT, register it in `functions.php` (not just CPT UI) so it's in code
5. After DB changes (plugin activations, settings), re-export `database/seed.sql` and commit
6. Push to `main`
