# 🇮🇷 UpdraftCentral Persian

Companion plugin that **Persianizes** and **right-to-left (RTL)** styles the [UpdraftCentral](https://updraftcentral.com/) dashboard — without touching core plugin files.

Your translations and RTL styles survive every UpdraftCentral update. 🛡️

> 🇮🇷 مستندات فارسی: [README-FA.md](README-FA.md)

## ✨ Features

- 🌐 Loads `updraftcentral-fa_IR.mo` onto the core `updraftcentral` text domain
- 💬 Covers PHP strings and JavaScript strings (`udclion`) via `__()` in PHP
- 🔁 Swaps Bootstrap LTR for UpdraftCentral’s built-in `bootstrap.rtl`
- 📐 Custom RTL styles for dashboard, modals, tables, sidebar & modules
- 🔤 Bundled **Vazirmatn** font (local — no CDN)
- 🔒 No core UpdraftCentral edits required

## 📋 Requirements

- WordPress 5.8+
- PHP 7.4+
- **UpdraftCentral** installed and active
- Site language set to Persian (`fa_IR`) or RTL enabled

## 📥 Installation

1. Place the `updraftcentral-persian` folder in `wp-content/plugins/`.
2. Activate both plugins:
   - UpdraftCentral Dashboard
   - UpdraftCentral Persian
3. Set WordPress language to **فارسی** (`Settings → General → Site Language`).
4. Open the front-end page with the `[updraft_central]` shortcode. ✅

## 📁 Structure

```
updraftcentral-persian/
├── updraftcentral-persian.php   # Bootstrap
├── includes/                    # i18n / RTL / admin
├── assets/css/                  # fonts, rtl, admin-rtl
├── assets/fonts/vazirmatn/      # Local Vazirmatn font
├── assets/js/rtl.js
├── languages/                   # updraftcentral-fa_IR.po / .mo
├── bin/                         # Translation compile tools
├── .ai/                         # Agent context & rules
├── AGENTS.md
├── README.md                    # English docs
└── readme-fa.md                 # Persian docs
```

## 🔄 Updating translations after an UpdraftCentral upgrade

1. Merge the new `updraftcentral/languages/updraftcentral.pot` into the `.po` (Poedit / `msgmerge`).
2. Translate new strings.
3. Compile:

```bash
php bin/compile-mo.php
```

## 🛠️ Developer filters

```php
// Force localization on/off
add_filter( 'ucp_should_localize', '__return_true' );

// Custom dashboard context detection
add_filter( 'ucp_is_dashboard_context', function ( $is ) {
	return $is || is_page( 'my-uc-dashboard' );
} );
```

## 📄 License

GPL-2.0-or-later — same as the WordPress ecosystem.
