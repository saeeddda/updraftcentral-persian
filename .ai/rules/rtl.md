# قوانین RTL

- جهت داشبورد: `direction: rtl; text-align: right` روی `#updraftcentral_dashboard`
- فیلدهای فنی (کلید ارتباط، URL، کد) باید `dir=ltr` بمانند
- آیکون‌های جهت‌دار (پیکان) را با `transform: scaleX(-1)` برعکس کنید
- Bootstrap را از `bootstrap.css` به `bootstrap.rtl.css` داخل خود UC عوض کنید؛ کپی جدا نسازید مگر لازم شود
- استایل ماژول‌ها را در صورت نیاز به `rtl.css` اضافه کنید؛ فایل هسته را ویرایش نکنید
- فونت پیش‌فرض فارسی: Vazirmatn محلی از `assets/fonts/vazirmatn/vazirmatn-font.css` با fallback به Tahoma
