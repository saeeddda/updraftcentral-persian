# زمینه معماری — UpdraftCentral Persian

## UpdraftCentral (هسته، فقط‌خواندنی)

- مسیر: `wp-content/plugins/updraftcentral/`
- Bootstrap: `site-management.php`
- Text domain: `updraftcentral`
- Domain Path: `/languages` (فقط `.pot`؛ بدون `.po`/`.mo` رسمی)
- بارگذاری ترجمه هسته: `plugins_loaded` → `load_plugin_textdomain('updraftcentral', …)`
- رشته‌های JS: `dashboard-translations.php` + `modules/*/translations-dashboard.php` → فیلتر `updraftcentral_udrclion` → `wp_localize_script(…, 'udclion', …)`
- هوک CSS داشبورد: `do_action('updraftcentral_load_dashboard_css', $enqueue_version)`
- Bootstrap: در `wp_footer` به‌صورت LTR enqueue می‌شود؛ فایل‌های `bootstrap.rtl*.css` موجود ولی استفاده‌نشده‌اند

## ماژول‌های هسته

`advancedtools`, `eum`, `events`, `notices`, `site_search`, `updates`, `updraftplus`, `updraftvault`, `wpo`

## این افزونهٔ جانبی

با اولویت `plugins_loaded` = `0` بالا می‌آید، سپس:

1. `load_textdomain_mofile` را برای domain `updraftcentral` به MO فارسی خودش هدایت می‌کند
2. زودتر از هسته `load_textdomain` را صدا می‌زند تا `udclion` فارسی شود
3. روی `updraftcentral_load_dashboard_css` استایل RTL و فونت را enqueue می‌کند
4. در `wp_footer` Bootstrap RTL را جایگزین می‌کند

## نسخه هدف هسته

تست‌شده روی UpdraftCentral **0.8.33** (آپدیت‌های بعدی باید با ادغام مجدد POT سازگار شوند).

## ابزار ترجمه

```bash
php bin/generate-dict-from-pot.php --force   # پر کردن دیکشنری از API/کش
php bin/merge-missing.php                   # ادغام ترجمه‌های دستی
php bin/build-fa-po.php                     # ساخت .po
php bin/compile-mo.php                      # ساخت .mo
php bin/stats.php                           # آمار کیفیت
```
