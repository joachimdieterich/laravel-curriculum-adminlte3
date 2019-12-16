# Browser tests

---

- [Browser Tests (Dusk)](#section-1)


<a name="section-1"></a>
## Browser Tests (Dusk)
Important! Start server in dusk environment. Never start browser tests on production server

```bash
php artisan config:clear
php artisan serve --env=dusk
```

Run browser tests

```bash
php artisan dusk
```