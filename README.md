# Shezan Beverages: WordPress theme (portfolio demo)

Interactive WordPress theme with a scroll-driven 3D juice carton, custom post types, SEO landing pages,
an enquiry form, speed and security hardening, and a client-maintenance dashboard.
Portfolio concept only: not the official Shezan website.

**Live demo (opens a fresh WordPress in your browser, nothing to install):**
https://playground.wordpress.net/?blueprint-url=https://raw.githubusercontent.com/YOUR-USERNAME/shezan-beverages-wp/main/blueprint.json

**Admin demo (auto-logged in, opens the "Shezan Care" maintenance dashboard):**
https://playground.wordpress.net/?blueprint-url=https://raw.githubusercontent.com/YOUR-USERNAME/shezan-beverages-wp/main/blueprint-admin.json

Each visitor gets their own private sandbox; changes reset on reload.

## Stack
PHP, HTML, CSS, JavaScript, GSAP. No page builder, no required plugins.

## Structure
- `functions.php`: setup, assets, Products CPT, meta box, demo seed
- `inc/seo.php`, `inc/forms.php`, `inc/performance.php`, `inc/security.php`, `inc/maintenance.php`
- `front-page.php`, `single-sz_product.php`, `page-landing-wholesale.php`

## Install locally
Zip the `shezan-theme` folder, then Appearance > Themes > Add New > Upload.
