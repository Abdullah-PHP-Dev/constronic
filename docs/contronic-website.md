# Contronic UAE website

This document covers how the site is built, how to edit it, and how to deploy and back it up.

**Stack:** WordPress 7.1 · custom theme `contronic` · companion plugin `contronic-core` · no third-party plugins.

---

## 1. Architecture

| Part | Location | Responsibility |
|---|---|---|
| **Contronic Core** (plugin) | `wp-content/plugins/contronic-core/` | Content types, custom fields, inquiries, SEO, company details, setup tool. The content stays intact if the theme ever changes. |
| **Contronic** (theme) | `wp-content/themes/contronic/` | Presentation only: templates, design system, homepage sections, menus. |

WordPress core is unmodified.

### Content types (plugin)

| Admin menu | Type | Public URL | Notes |
|---|---|---|---|
| Hero Slider | `ctr_slide` | — (homepage only) | Label, two-line heading, text, two buttons, desktop and mobile image |
| Services | `ctr_service` | `/services/`, `/services/{slug}/` | Icon, tagline, scope list, delivery steps, FAQs, gallery, “feature on homepage” |
| Products & Solutions | `ctr_product` | `/products/…` | Brand, model, specifications table, features, datasheet PDF, gallery |
| Projects | `ctr_project` | `/projects/…` | Client, location, year, status, scope, linked services, gallery |
| Partners & Brands | `ctr_partner` | — (Partners page and homepage) | **Shown only when “verified” is ticked** |
| Testimonials | `ctr_testimonial` | — (homepage) | **Shown only when “verified” is ticked** |
| Inquiries | `ctr_inquiry` | private | Form submissions with status, internal notes and CSV export |

Taxonomies: Service Categories (`/service-category/…`), Product Categories, Project Sectors. Each listing page shows its categories as filter chips.

### Page templates (theme)

About · Contact · Partners & Brands · Gallery · Legal (Privacy, Terms). Pick one under **Page → Template**.

### Theme structure

```
contronic/
├── functions.php            loads inc/*
├── inc/
│   ├── setup.php            supports, menus, image sizes, assets, page fields
│   ├── customizer.php       ALL homepage/header/footer/brand settings + defaults
│   ├── template-tags.php    reusable helpers (buttons, headings, cards, art, gallery…)
│   ├── icons.php            inline SVG icon set
│   ├── menus.php            hides menu links to empty sections; gallery collection
│   ├── performance.php      head clean-up, WebP output, image quality
│   └── compat.php           fallbacks if the plugin is inactive
├── template-parts/
│   ├── home/*.php           one file per homepage section
│   ├── cards/*.php          service / product / project / post cards
│   ├── hero-art.php         original SVG hero illustration (used until photos exist)
│   └── inquiry-form.php
├── page-templates/*.php
├── archive-ctr_*.php, single-ctr_*.php, taxonomy-ctr_*.php
└── assets/ css/main.css · js/main.js (no dependencies) · fonts/ (self-hosted) · img/
```

---

## 2. Editing the website (no code needed)

| What | Where |
|---|---|
| Logo, site icon (favicon) | Appearance → Customize → Site Identity |
| Phone, email, address, WhatsApp, hours, map, social links | Customize → **Company & contact details** |
| Brand colours, text logo | Customize → Brand & colours |
| Header button, top bar | Customize → Header |
| Every homepage section (text, facts, strengths, process, show/hide) | Customize → **Homepage sections** |
| Footer text and copyright | Customize → Footer |
| Main and footer menus | Appearance → Menus |
| Hero slides | **Hero Slider** (the slide image is the featured image, 1920×1080) |
| Services, products, projects | Their admin menus. **Order** is in the “Page attributes” box. |
| Page banner text and image | On each page: “Page banner & gallery” box and the Featured image |
| Gallery page | Add images to the Gallery page’s gallery field. All published project galleries are included automatically. |
| SEO title, description, share image, noindex, canonical | “SEO & sharing” box on every page, service, product, project and post |
| Homepage SEO and default share image | Customize → SEO defaults |
| Inquiry emails, auto-reply, consent text, CSV export | Inquiries → Settings & export |

Lists in the Customizer and in custom fields use **one item per line**. Columns are separated with `|`, for example `Plan | Quotation, programme and method statements.`

### Automatic behaviours

- **Empty sections hide themselves.** Projects, Products, Partners, Gallery and News stay out of the menu, the homepage and the sitemap, and are marked `noindex`, until something is published. This means there are never dead links.
- **Missing images.** Wherever no photo has been uploaded yet, the theme shows generated blueprint-style artwork. Upload a featured image and it replaces the artwork.
- **Sample content.** Items created by the setup tool show a **“Sample — review”** badge. Edit the item, tick **“Content reviewed and approved”**, and update.
- **Launch checklist.** The Dashboard shows a checklist of what is still missing before launch.

---

## 3. Business information still needed (not invented)

Nothing about Contronic UAE was published online, so these items need to come from the company:

1. **Logo files**: SVG or a transparent PNG, plus a square icon.
2. **Contact details**: phone(s), email(s), WhatsApp, office address, working hours, Google Maps link.
3. **Trade licence / legal name**, for the footer and schema.
4. **Company story** for the About page: founding year, locations, team, sectors, licences, certifications (only real ones).
5. **Confirmation of the service list.** The 8 services are sample descriptions of typical construction and MEP work. Confirm, edit or remove each one.
6. **Projects**: names, client permission, location, year, scope and real site photos.
7. **Products / brands supplied**, if any.
8. **Partners and clients** that are approved for publication.
9. **Testimonials**, genuine and approved.
10. **Photography**: hero images, team and site photos. Recommended sizes: 1920×1080 (hero), 1600×1200 (cards).
11. **Legal review** of the sample Privacy Policy and Terms (UAE PDPL, Federal Decree-Law 45/2021).
12. **Social media** account URLs.

---

## 4. Local development

- URL: `http://constronic.test` (Laravel Valet serves `~/Sites`)
- Database: `constronic` (MySQL, user `root`)
- `wp-config.php` has `WP_DEBUG` and `WP_DEBUG_LOG` turned on, with display off. Errors go to `wp-content/debug.log`. **Turn these off on production.**
- Re-run the structure setup any time from **Tools → Contronic setup**. It is idempotent and never overwrites existing content.

---

## 5. Deployment to the live server

1. **Back up the live site** (files and database) before anything else. See section 6.
2. Upload `wp-content/themes/contronic/` and `wp-content/plugins/contronic-core/`.
3. Move the database:
   ```bash
   mysqldump -u root -p constronic > contronic-$(date +%F).sql
   # import on the server, then replace the URL (handles serialized data):
   wp search-replace 'http://constronic.test' 'https://controniceuae.com' --all-tables --precise
   ```
   Without WP-CLI, use a serialization-safe tool such as the “Better Search Replace” plugin, then remove it afterwards.
4. In `wp-config.php` on the server:
   - Set `WP_DEBUG` to `false` and remove `WP_ENVIRONMENT_TYPE` (or set it to `'production'`).
   - Generate new salts: https://api.wordpress.org/secret-key/1.1/salt/
   - Use a dedicated database user with a strong password (not `root`).
   - Add `define( 'DISALLOW_FILE_EDIT', true );`
5. Activate the **Contronic Core** plugin, then the **Contronic** theme.
6. **Settings → Permalinks → Save**, which refreshes the URL rules.
7. **Settings → Reading → untick “Discourage search engines”.** This turns on `/wp-sitemap.xml` and the full `robots.txt`.
8. **Email:** configure SMTP (Microsoft 365, Google Workspace, SES…) so inquiry notifications are delivered reliably, using one SMTP plugin or the host’s mail settings. Then send a test inquiry.
9. Force HTTPS and set up a page cache (the host’s cache, or LiteSpeed/W3TC). The inquiry form keeps working behind full-page caching.
10. Submit `https://controniceuae.com/wp-sitemap.xml` in Google Search Console.

### Nginx / Valet note

On Valet, `/robots.txt` is treated as a static file and returns 404 locally. On Apache, or on nginx with the standard WordPress `try_files` rule, WordPress serves it normally.

---

## 6. Backups

- **Database** (before every deployment or bulk change):
  `mysqldump -u USER -p DBNAME > backups/contronic-$(date +%F-%H%M).sql`
- **Files:** `wp-content/uploads/`, plus the theme and plugin (both are in git).
- Keep automated daily backups on the host with at least 14 days of retention and an off-site copy.
- **Restore:** import the SQL dump and restore `uploads/`. If the domain changed, run the search-replace from section 5.
- Local pre-change backups are in `backups/` (git-ignored).

---

## 7. Security notes

- Every form input is sanitized and validated server-side. Output is escaped.
- Inquiry form protection: HMAC-signed timestamp (rejects forged and instant bot posts), honeypot field, rate limit of 5 per 10 minutes per visitor (IPs are stored only as hashes), a nonce for logged-in users, and same-host redirects only.
- Admin actions (meta saves, inquiry status, CSV export, setup) check user capabilities and nonces. The CSV export neutralises spreadsheet formula injection.
- Partners and testimonials cannot appear publicly unless an editor confirms they are genuine.
- Recommended: strong admin passwords with 2FA, a security plugin or host WAF for login rate limiting, and prompt core updates.

## 8. Performance

- No jQuery or other libraries on the front end. One CSS file and one deferred JS file, around 60 KB unminified. Fonts are self-hosted, subset to Latin and preloaded.
- Responsive `srcset` images with lazy loading. The first hero image is eager with `fetchpriority=high`. Uploads are converted to WebP at quality 80 where the server supports it.
- Emoji scripts, embeds and block-library CSS are removed on pages that don’t need them.
- Animations respect `prefers-reduced-motion`.
