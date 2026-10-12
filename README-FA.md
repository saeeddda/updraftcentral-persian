# 🇮🇷 UpdraftCentral Persian

افزونهٔ جانبی برای **فارسی‌سازی** و **راستی‌چین‌سازی (RTL)** داشبورد [UpdraftCentral](https://updraftcentral.com/) بدون دست‌کاری فایل‌های اصلی.

با هر بروزرسانی UpdraftCentral، ترجمه‌ها و استایل RTL این افزونه باقی می‌مانند. 🛡️

> 🇬🇧 English docs: [README.md](README.md)

## ✨ ویژگی‌ها

- 🌐 بارگذاری فایل ترجمه `updraftcentral-fa_IR.mo` روی text domain اصلی (`updraftcentral`)
- 💬 پوشش رشته‌های PHP و رشته‌های جاوااسکریپت (`udclion`) چون از طریق `__()` در PHP ساخته می‌شوند
- 🔁 جایگزینی Bootstrap LTR با `bootstrap.rtl` موجود در خود UpdraftCentral
- 📐 استایل RTL اختصاصی برای داشبورد، مودال‌ها، جدول‌ها، منوی کناری و ماژول‌ها
- 🔤 فونت فارسی Vazirmatn (باندل محلی، بدون CDN)
- 🔒 بدون نیاز به ویرایش هستهٔ UpdraftCentral

## 📋 پیش‌نیاز

- WordPress 5.8+
- PHP 7.4+
- افزونهٔ **UpdraftCentral** نصب و فعال
- زبان سایت روی فارسی (`fa_IR`) یا فعال بودن RTL

## 📥 نصب

1. پوشهٔ `updraftcentral-persian` را در `wp-content/plugins/` قرار دهید (همین مسیر).
2. از **افزونه‌ها** هر دو مورد را فعال کنید:
   - UpdraftCentral Dashboard
   - UpdraftCentral Persian
3. زبان وردپرس را روی **فارسی** بگذارید (`تنظیمات → عمومی → زبان سایت`).
4. صفحهٔ فرانت دارای شورتکد `[updraft_central]` را باز کنید. ✅

## 📁 ساختار

```
updraftcentral-persian/
├── updraftcentral-persian.php   # نقطه ورود
├── includes/                    # i18n / RTL / admin
├── assets/css/                  # fonts, rtl, admin-rtl
├── assets/fonts/vazirmatn/      # فونت محلی Vazirmatn
├── assets/js/rtl.js
├── languages/                   # updraftcentral-fa_IR.po / .mo
├── bin/                         # ابزار کامپایل ترجمه
├── .ai/                         # زمینه و قوانین برای Agentها
├── AGENTS.md
├── README.md                    # مستندات انگلیسی
└── readme-fa.md                 # مستندات فارسی
```

## 🔄 بروزرسانی ترجمه پس از آپدیت UpdraftCentral

1. فایل جدید `updraftcentral/languages/updraftcentral.pot` را با `.po` ادغام کنید (Poedit / `msgmerge`).
2. رشته‌های جدید را ترجمه کنید.
3. کامپایل:

```bash
php bin/compile-mo.php
```

## 🛠️ فیلترهای توسعه‌دهنده

```php
// اجبار به فعال/غیرفعال بودن فارسی‌سازی
add_filter( 'ucp_should_localize', '__return_true' );

// تشخیص سفارشی کانتکست داشبورد
add_filter( 'ucp_is_dashboard_context', function ( $is ) {
	return $is || is_page( 'my-uc-dashboard' );
} );
```

## 📄 مجوز

GPL-2.0-or-later — مانند اکوسیستم وردپرس.
