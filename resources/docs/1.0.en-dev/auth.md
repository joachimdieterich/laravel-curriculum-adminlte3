# Authentification

---

- [oAuth2](#section-1)
- [Enabling guest login](#section-2)


<a name="section-1"></a>
## Generate oAuth2 clients

```bash
php artisan passport:install
```

<a name="section-2"></a>
## Enabling guest login

By default the guest user is seeded with ID 8.
To enable login (over login page or route ```"/guest"```) add ```GUEST_USER=8``` to ```.env```


If the organization of the guest user has a navigator, he will be redirected to the first view of this navigator. 
If there is no navigator, he is redirected to the dashboard.