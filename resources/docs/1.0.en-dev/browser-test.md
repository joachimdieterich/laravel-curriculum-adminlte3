# Browser tests

---

- [Browser Tests (Dusk)](#section-1)


<a name="section-1"></a>
## Browser Tests (Dusk)
Important! Start server in dusk environment. Never start browser tests on production server

```bash
php artisan config:clear
php artisan serve --env=dusk.testing
```

Run browser tests

```bash
php artisan dusk
```

## Troubleshooting

Update chrome-driver
```bash
Facebook\WebDriver\Exception\SessionNotCreatedException: session not created: Chrome version must be between [70] and [73]
php artisan dusk:chrome-driver
```
Excepton:
```bash
Facebook\WebDriver\Exception\SessionNotCreatedException: session not created: This version of ChromeDriver only supports Chrome version 81
```
Update chrome