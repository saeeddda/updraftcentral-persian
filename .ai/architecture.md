# معماری افزونه جانبی

```
┌─────────────────────────────────────────┐
│ WordPress (locale fa_IR / is_rtl)       │
└─────────────────┬───────────────────────┘
                  │
      ┌───────────▼───────────┐
      │ updraftcentral-persian│  plugins_loaded:0
      │  UCP_I18n / UCP_RTL   │
      └───────────┬───────────┘
                  │ load_textdomain(updraftcentral, fa_IR.mo)
      ┌───────────▼───────────┐
      │ updraftcentral core   │  plugins_loaded (later)
      │ load_plugin_textdomain│  → overridden mofile
      └───────────┬───────────┘
                  │
        ┌─────────┴──────────┐
        │ PHP __() strings   │
        │ JS via udclion     │
        │ Templates/Handlebars│
        └─────────┬──────────┘
                  │
        ┌─────────▼──────────┐
        │ UCP RTL CSS + font │
        │ Bootstrap.rtl swap │
        └────────────────────┘
```

## لایه‌ها

| لایه | کلاس | مسئولیت |
|------|------|---------|
| Bootstrap | `updraftcentral-persian.php` | ثابت‌ها، activation |
| Orchestrator | `UCP_Plugin` | بارگذاری includes |
| i18n | `UCP_I18n` | MO override + gettext سخت‌کدها |
| RTL | `UCP_RTL` | استایل/اسکریپت/Bootstrap |
| Admin | `UCP_Admin` | اعلان وابستگی |
