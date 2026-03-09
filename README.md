# L'ivresse des mots — WordPress Theme

Custom WordPress theme for **L'Ivresse des Mots** (livressedesmots.fr), a French literary and cultural blog covering books, films, series, and podcasts.

## Overview

L'ivresse des mots is a hand-coded theme (HTML/CSS/JS → PHP) built around three content types: **chroniques** (reviews), **articles** (blog posts), and **portraits** (artist profiles - not published yet). It features a tri-color theme switcher, client-side filtering with pagination, and a modular file architecture.

### Color Schemes

The site offers three color schemes, each named after a song:

- **Lilac Wine** (light) — Jeff Buckley
- **Purple Rain** (dark) — Prince
- **Green Day** (green) — Green Day

The active scheme is saved in `localStorage` and applied before first paint via an inline script in `<head>` to prevent flash of wrong theme.


## Content Architecture

### Custom Post Types

| CPT | Registered via | Slug | Purpose |
|-----|---------------|------|---------|
| `chroniques` | Theme code (`post-types.php`) | `/chroniques/` | Book, film, series, and podcast reviews |
| `artiste` | Pods plugin | `/portrait/` (rewritten from `/artiste/`) | Artist/author profile pages |
| `post` | WordPress core | Standard | Blog articles diving deeper into themes and subjects raised by the reviewed works |

### Custom Taxonomies

| Taxonomy | Post Types | Hierarchical | Purpose |
|----------|-----------|--------------|---------|
| `auteur` | chroniques | No | Author of the reviewed work |
| `genre` | chroniques | Yes | Literary/media genre (parent → sub-genre) |
| `theme` | chroniques, artiste, post | No | Thematic tags |
| `nationalite` | chroniques, artiste, post | No | Author/artist nationality |
| `periode` | chroniques | Yes | Historical period |
| `mois_lecture` | chroniques | No | Reading month (for bilan reports) |
| `type_media` | chroniques | Yes | Media type (livre, film, série, podcast) |
| `role` | artiste (via Pods) | No | Artist role (Auteur·ice, Réalisateur·ice, etc.) |

### Meta Fields (Chroniques)

Fields managed via a custom meta box in `post-types.php`:

- `date_publication` — Publication year (books)
- `date_sortie` — Release year (films, series, podcasts)
- `pages` — Page count
- `saisons` — Number of seasons
- `duree` — Duration in minutes (films)
- `duree_episode` — Episode duration in minutes (series, podcasts)
- `heures_ecoute` — Listening hours (audiobooks)
- `note_etoiles` — Star rating (0.5 to 5)
- `_chroniques_spoiler` — Spoiler content (hidden behind toggle)
- `_post_sources` — Reference sources (shared with posts and artistes)

### ACF Fields

- `coup_de_coeur` (boolean) — Marks standout chroniques, displayed in bilan reports
- `mois_1`, `mois_2`, `mois_3` (select) — Quarter month selectors for bilan posts
- `home_title_section`, `chroniques_title_section`, `artistes_title_section`, `parentheses_title_section`, `about_title_section`, `contact_title_section` — Styled page titles with HTML support


## File Structure

```
TurningPages/                    # The blog was originally named this way
├── style.css                    # CSS variables, reset, global styles
├── functions.php                # Bootstrap — includes all inc/ files
├── header.php                   # Opens <section class="blog"> wrapper
├── footer.php                   # Closes <section class="blog"> wrapper
│
├── front-page.php               # Homepage (mixed feed, all CPTs)
├── index.php                    # Blog fallback
├── page.php                     # Default page (legal, etc.)
├── single.php                   # Single article
├── single-chroniques.php        # Single chronique
├── single-artiste.php           # Single portrait
├── single-bilan.php             # Quarterly report (Chart.js)
├── search.php / searchform.php  # Search results + form
├── comments.php                 # Custom comments
├── category.php / tag.php       # Category & tag archives
├── 404.php                      # Not found
│
├── page-chroniques.php          # Filtered chroniques listing
├── page-articles.php            # Filtered articles listing
├── page-artistes.php            # Filtered artistes listing
├── page-a-propos.php            # About page
├── page-contact.php             # Contact (CF7)
│
├── taxonomy-*.php               # Genre, nationalité, rôle, thème archives
│
├── inc/
│   ├── setup/                   # Theme supports, enqueue, customizer, security
│   ├── functions/               # CPT, taxonomies, search, logo manager, ACF, dashboard
│   ├── helpers/                 # Formatting utilities
│   └── template-parts/
│       ├── chronique/           # Header, meta, rating, sidebars, spoiler, sources
│       ├── components/          # Cards, social links, related posts
│       └── navigation/          # Nav menu, breadcrumbs
│
├── assets/
│   ├── css/                     # Components, layouts, pages (modular)
│   ├── js/                      # app.js + modules/ (filters, pagination, theme, etc.)
│   └── images/logos/            # Per-theme logo variants
```

## Technical Stack

### WordPress Plugins

| Plugin | Purpose |
|--------|---------|
| **Pods** | Manages the `artiste` CPT and relationship fields |
| **ACF** | Custom fields for bilans and page titles |
| **Rank Math** | SEO — custom `%chronique_auteur%` variable registered in `taxonomy-helpers.php` |
| **Contact Form 7** | Contact form |
| **Contact Form Entries** | Stores CF7 submissions in database |
| **LiteSpeed Cache** | Page and asset caching (production) |
| **Solid Security** | Security hardening |
| **Really Simple Security** | SSL and security headers |
| **WP Mail SMTP** | Email delivery configuration |
| **hCaptcha** | Spam protection on forms |
| **UpdraftPlus** | Backups |
| **PublishPress Planner** | Editorial calendar |
| **Anti-Spam Bee** | Comment spam filtering |
| **Koko Analytics** | Privacy-friendly site analytics (cookieless, self-hosted) |

### External Dependencies

| Library | Loaded from | Used for |
|---------|------------|----------|
| Ionicons 7.1 | unpkg.com | UI icons (ES module + nomodule fallback) |
| Chart.js 4.4 | jsDelivr | Bilan report charts (conditional) |

### Key Technical Decisions

- **No jQuery on the front end** — All front-end JS is vanilla. jQuery is only loaded on the contact page (required by the Contact Form Entries plugin) and in the WordPress admin.
- **Client-side filtering** — Listing pages load all posts at once (`posts_per_page = -1`) and filter/paginate via JS. This provides instant filtering but means all content is in the DOM. Acceptable for up to ~200-300 items per listing.
- **`filemtime()` cache busting** — All theme assets use file modification time as their version string via the `tp_asset_version()` helper. Browser caches are automatically invalidated when files change.
- **Function prefixing** — All theme functions use the `tp_` prefix to avoid namespace collisions with plugins.
- **`<section class="blog">` wrapper** — Opened in `header.php`, closed in `footer.php`. All page content sits inside this wrapper. The CSS layout depends on this structure.
- **Self-hosted fonts** — Montserrat and Cardo are served locally from `assets/fonts/` instead of Google Fonts CDN, avoiding sending visitor IPs to Google (GDPR compliance) and eliminating an external DNS lookup.
- **Self-hosted analytics** — Koko Analytics runs entirely on the server with no cookies and no external services, ensuring GDPR compliance without requiring a consent banner.

## Filtering & Pagination

### Client-Side Pattern (Listing Pages)

The three listing pages and the homepage use the same architecture:

1. **PHP** loads all posts into the DOM with `posts_per_page = -1`
2. Each card has `data-*` attributes set by the PHP template (genre, theme, nation, media, category, role)
3. **JS** reads filter `<select>` values and matches against `data-*` attributes
4. Posts that don't match are hidden via `display: none`
5. Pagination is calculated from the filtered set (8 posts per page)

| Page | Script | Filters |
|------|--------|---------|
| page-chroniques.php | filter-chroniques.js | genre, theme, nation, media |
| page-articles.php | filter-articles.js | category |
| page-artistes.php | filter-artistes.js | role, nation |
| front-page.php | pagination.js | (pagination only) |

### Server-Side Pagination (Search)

Search results use WordPress native `paginate_links()` for server-side pagination, since the search query runs server-side.


## Author Resolution (Chroniques)

Chronique headers display the author name using a two-step strategy:

1. **Primary**: Pods relational field `artistes_lies` — if the chronique is linked to artiste profiles, their names are displayed as clickable links to portrait pages.
2. **Fallback**: `auteur` taxonomy — if no Pods relation exists, the name is pulled from the taxonomy (plain text, no link).

This is implemented in `inc/template-parts/chronique/header.php`.


## Sidebar Router

The chronique sidebar dynamically loads based on media type:

```
sidebar.php reads type_media taxonomy
    ├── livre    → sidebar-livre.php    (year, pages)
    ├── film     → sidebar-film.php     (year, duration)
    ├── serie    → sidebar-serie.php    (year, seasons, episode duration)
    ├── podcast  → sidebar-podcast.php  (year, seasons, episode duration)
    └── default  → sidebar-default.php  (fallback message)
```

The star rating component (`rating.php`) is shared across all media sidebars via `get_template_part()`.


## Security Measures

Implemented in `inc/setup/security.php`:

- WordPress version removed from HTML, RSS, and core asset URLs
- XML-RPC disabled (brute-force prevention)
- Login error messages genericized
- HTTP security headers (X-Content-Type-Options, X-Frame-Options, Referrer-Policy)
- File upload restrictions (extension whitelist + MIME verification)
- REST API user endpoints removed (prevents user enumeration)
- Session cookies hardened (httponly, secure, cookies-only) — HTTPS only
- Auto-updates enabled for plugins and themes

**In `wp-config.php`** (not in theme): `define('DISALLOW_FILE_EDIT', true);` — disables the built-in code editors.


## SEO

- **Rank Math** handles meta tags, sitemaps, and canonical URLs
- Custom Rank Math variable `%chronique_auteur%` pulls author names from taxonomy for dynamic title patterns (e.g. "Misery — Stephen King | L'Ivresse des Mots")
- `<time>` elements with ISO 8601 `datetime` attributes on dates
- `aria-label` on icon-only links for screen reader accessibility
- `loading="lazy"` and responsive `srcset` via `wp_get_attachment_image()` where applicable


## Development

### Local Environment

- **XAMPP** on macOS
- **VSCode** for editing
- **Git** for version control

### Hosting

- **Hostinger** (hPanel for file management)
- **LiteSpeed** web server with caching plugin

### Key Files to Edit

| To change... | Edit... |
|-------------|---------|
| Social networks | `inc/setup/customizer.php` + `inc/template-parts/components/social-links.php` |
| Navigation links | `inc/template-parts/navigation/nav.php` |
| Chronique meta fields | `inc/functions/post-types.php` (meta box section) |
| Logo variants | Admin > Logos Thèmes (page from `logo-manager.php`) |
| Favicon | Admin > Appearance > Customize > Site Identity > Site Icon |
| Reading months | `inc/functions/acf-config.php` (years array — update when approaching 2027) |
| Theme colors | `style.css` (CSS custom properties per theme class) |


## Conventions

- **PHP functions**: Prefixed with `tp_` (e.g. `tp_format_duree()`, `tp_get_chronique_genre_display()`)
- **CSS handles**: Prefixed with `turningpages-` (e.g. `turningpages-navigation`)
- **Comments**: English, explaining *why* not just *what*
- **Template parts**: Loaded via `get_template_part()`, never `include()`
- **Escaping**: All output escaped with appropriate function (`esc_html`, `esc_attr`, `esc_url`, `wp_kses_post`)