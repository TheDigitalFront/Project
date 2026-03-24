# The Digital Front

A student-led tech publication built with WordPress for INFO 3602 — Web Programming II (Sem II 2025/2026).

**Team:** Terrence Murray, Jeremiah Clinton, Robyn-Catherine Khan

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
   - Go to WP Admin > Tools > WP Migrate DB
   - Import `database/seed.sql`
   - Search-replace the old site URL with your local domain

5. **Visit WP Admin once** — the theme auto-configures everything:
   - Activates all 9 required plugins
   - Creates pages (Home, About Us, Team, Mission)
   - Sets static front page
   - Seeds categories (Mobile Devices, Apple, Google, Samsung)
   - Builds the nav menu with correct hierarchy
   - Enables Yoast breadcrumbs
   - Enables front-end registration and comments
   - Flushes rewrite rules

6. **Verify setup**:
   - Child theme "The Digital Front Child" should be active
   - "My Articles" should appear in the admin sidebar
   - Nav menu should show: Home | About (Team, Mission dropdown)
   - Category filter on the home page should show tabs

### Project Structure

```
wp-content/
├── themes/the-digital-front-child/
│   ├── style.css              # Theme header + all custom CSS
│   ├── functions.php          # Slim loader — enqueues styles, includes inc/
│   ├── inc/
│   │   ├── article-cpt.php            # Article CPT registration (R1)
│   │   ├── acf-fields.php             # ACF JSON sync + Article fields (R2)
│   │   ├── shortcode-category-filter.php  # [tdf_category_filter] / Query 2 (R5/R6)
│   │   └── setup.php                  # One-time environment setup
│   ├── front-page.php         # Home page template
│   ├── single.php             # Single article/post template
│   ├── page.php               # Generic page template
│   ├── archive-article.php    # Article archive with filter + pagination
│   ├── header.php             # Site header + nav + breaking news banner
│   ├── footer.php             # Site footer
│   └── acf-json/              # ACF field group sync (auto-generated)
├── plugins/
│   ├── tdf-breaking-news/     # Plugin 1: Breaking News Banner (Terrence)
│   ├── advanced-custom-fields/
│   ├── custom-post-type-ui/
│   ├── members/
│   ├── query-monitor/
│   ├── wordpress-seo/
│   ├── wp-migrate-db/
│   ├── wp-pagenavi/
│   └── wpforms-lite/
└── database/
    └── seed.sql               # Baseline database for onboarding
```

### Custom Post Types

| CPT | Slug | Description | Owner |
|-----|------|-------------|-------|
| Article | `article` | Main content type — tech posts, news, tutorials | Terrence |
| Review | `review` | Product reviews with star ratings + image gallery | Jeremiah |
| Opinion | `opinion` | Opinion pieces with author bio + related articles | Robyn |

### ACF Fields (Article)

| Field | Type | Description |
|-------|------|-------------|
| `video_embed` | Text | Video embed URL (YouTube, Vimeo, etc.) |
| `reading_time` | Number | Estimated reading time in minutes |
| `source_url` | URL | Link to the original source |

### Custom Queries (Phase 4)

| Query | Description | Location | Owner |
|-------|-------------|----------|-------|
| Q1 — Trending by view count | `WP_Query` ordered by `_post_views_count` | `page-trending.php` | Jeremiah |
| Q2 — Category + date range | `tax_query` + `date_query` with sanitized URL params | `inc/shortcode-category-filter.php` | Terrence |
| Q3 — Related posts by tag | `tag__in` + `post__not_in` on single post view | `single.php` | Robyn |

### Custom Plugins (Phase 5)

| Plugin | Description | Owner |
|--------|-------------|-------|
| TDF Breaking News Banner | Scrolling headline ticker with admin settings | Terrence |
| TDF AJAX Category Filter | AJAX-powered post filtering by category | Jeremiah |
| TDF Reading Progress Bar | Scroll progress indicator on single posts | Robyn |

### Shortcodes

| Shortcode | Description |
|-----------|-------------|
| `[tdf_breaking_news]` | Renders the breaking news ticker (placed in header.php) |
| `[tdf_category_filter per_page="6"]` | Filterable article grid with category tabs, date range, and pagination |

### Pages & Navigation

- **Home** — Static front page with hero, articles grid, topics, newsletter, stats
- **About Us** — Team intro and site overview
  - **Team** (child) — Individual bios for all 3 team members
  - **Mission** (child) — Editorial mission and values
- **Trending in Tech** — Posts ordered by view count (Jeremiah)
- **Nav menu:** Home | Trending (optional) | About > Team, Mission

### Requirements Mapping

| # | Requirement | How It's Met |
|---|-------------|-------------|
| R1 | 3 CPTs with custom fields | Article, Review, Opinion via CPTUI + ACF |
| R2 | Digital content per CPT | Article: video + image. Review: gallery + rating. Opinion: bio + quote |
| R3 | User roles + permissions | Members plugin: Subscriber, Contributor, Editor |
| R4 | Pages, child pages, menus, breadcrumbs | 5 pages, 2-level hierarchy, dynamic menu, Yoast breadcrumbs |
| R5 | Pagination + curation | WP-PageNavi on Home, Trending, Article archive |
| R6 | 3 custom queries (documented) | Q1: trending, Q2: category+date, Q3: related posts |
| R7 | 3 custom plugins | Breaking News, AJAX Filter, Progress Bar |
| R8 | Comments + registration | Native WP comments + front-end registration enabled |

### What's Tracked in Git
- Custom child theme (`wp-content/themes/the-digital-front-child/`)
- Required plugins (`wp-content/plugins/`)
- Custom plugin: TDF Breaking News (`wp-content/plugins/tdf-breaking-news/`)
- Seed database (`database/seed.sql`)
- Project docs and config samples

### What's NOT Tracked
- WordPress core files (`wp-admin/`, `wp-includes/`, etc.)
- `wp-config.php` (local credentials — use `wp-config-sample.php` as template)
- `wp-content/uploads/` (media files)
- Default themes (`twenty*`)
- `wp-content/mu-plugins/` (Local by Flywheel auto-generated)
- Query Monitor drop-in symlink (`wp-content/db.php`)

### Team Ownership

| Member | Responsibilities |
|--------|-----------------|
| **Terrence Murray** | Project setup, GitHub, child theme, Query 2 (category + date), Home page template, Breaking News plugin, deployment |
| **Jeremiah Clinton** | Reviews CPT + ACF, user roles (Members), Query 1 (trending), Trending page, AJAX Category Filter plugin |
| **Robyn-Catherine Khan** | Opinions CPT + ACF, About pages, breadcrumbs, Query 3 (related posts), content seeding, Reading Progress Bar plugin |

### Contributing

1. Pull latest from `main`
2. Create or switch to your feature branch
3. Make changes in the child theme (`inc/` for PHP, `style.css` for styles) or plugins
4. If you add/change ACF field groups, they auto-sync to `acf-json/` — commit those files
5. If you add a new CPT, register it in `inc/` (not just CPT UI) so it's in code
6. To re-run setup after changes, bump `TDF_SETUP_VERSION` in `inc/setup.php`
7. Push to your branch, open a PR to `main`
