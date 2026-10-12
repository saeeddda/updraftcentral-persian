# AGENTS.md — UpdraftCentral Persian

راهنمای عامل‌های هوش مصنوعی (Cursor / Codex / Claude) برای کار روی این مخزن.

## هدف پروژه

افزونهٔ **جانبی** وردپرس که UpdraftCentral را فارسی و RTL می‌کند، بدون تغییر فایل‌های پلاگین اصلی در:

`../updraftcentral/`

هرگز فایل داخل `updraftcentral/` را ویرایش نکنید؛ فقط از هوک/فیلتر/ترجمه‌های این مخزن استفاده کنید.

## قوانین طلایی

1. **عدم پچ هسته:** هیچ `StrReplace` روی `../updraftcentral/**` مجاز نیست.
2. **Text domain هدف:** `updraftcentral` (نه domain خودمان برای رشته‌های UC).
3. **Domain خودمان:** `updraftcentral-persian` فقط برای اعلان‌ها و لینک‌های این افزونه.
4. **ترجمه JS:** از طریق MO روی PHP → `udclion`؛ فایل JSON جدا لازم نیست مگر UC به `wp.i18n` مهاجرت کند.
5. **RTL:** استایل‌ها در `assets/css/rtl.css`؛ Bootstrap RTL از خود UC enqueue شود.
6. **پس از تغییر `.po`:** حتماً `php bin/compile-mo.php` را اجرا کنید.
7. جزئیات بیشتر در پوشهٔ `.ai/`.

## نقشهٔ فایل‌های مهم

| مسیر | نقش |
|------|-----|
| `updraftcentral-persian.php` | Bootstrap + ثابت‌ها |
| `includes/class-ucp-i18n.php` | `load_textdomain_mofile` + early `load_textdomain` |
| `includes/class-ucp-rtl.php` | CSS/JS RTL و تعویض Bootstrap |
| `includes/class-ucp-admin.php` | اعلان‌های ادمین |
| `languages/updraftcentral-fa_IR.po` | منبع ترجمه |
| `languages/updraftcentral-fa_IR.mo` | باینری لودشونده توسط WP |
| `.ai/context.md` | زمینه معماری UC |
| `.ai/rules/i18n.md` | قوانین ترجمه |
| `.ai/rules/rtl.md` | قوانین RTL |

## گردش‌کار رایج

### افزودن/به‌روز کردن ترجمه

1. مقایسه با `../updraftcentral/languages/updraftcentral.pot`
2. به‌روزرسانی `languages/updraftcentral-fa_IR.po`
3. `php bin/compile-mo.php`
4. تست با locale = `fa_IR`

### رفع باگ چیدمان RTL

1. بازتولید در داشبورد UC با `dir=rtl`
2. اصلاح فقط در `assets/css/rtl.css` یا `assets/js/rtl.js`
3. اولویت: ویژگی‌های منطقی CSS (`margin-inline-*`) وقتی ممکن است

### رشتهٔ ترجمه‌نشده / هاردکد

- اگر در POT نیست و با `__()` نیست → `gettext` filter در `class-ucp-i18n.php` (حداقلی)
- اگر در POT هست ولی خالی است → پر کردن `msgstr` در `.po`

## تست دستی

- [ ] فعال‌سازی هر دو افزونه بدون fatal
- [ ] منوی ادمین UpdraftCentral به فارسی/قابل‌خواندن
- [ ] صفحهٔ شورتکد `[updraft_central]` راست‌چین + فونت فارسی
- [ ] مودال افزودن سایت، تنظیمات، به‌روزرسانی‌ها
- [ ] فیلد کلید/URL همچنان LTR
- [ ] پس از آپدیت فرضی UC، این افزونه همچنان کار می‌کند

## چیزهایی که نباید انجام دهید

- کپی کردن کل CSS هسته به این مخزن
- ترجمهٔ نام‌های تجاری ضروری به شکل گمراه‌کننده (UpdraftPlus و غیره را حفظ کنید)
- commit کردن secrets یا `.vscode/sftp.json` در صورت حساس بودن
