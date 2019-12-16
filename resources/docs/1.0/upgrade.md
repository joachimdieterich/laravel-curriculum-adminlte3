# Upgrade

---

- [Step 1 - CUpdate from github](#section-1)

<a name="section-1"></a>
## Step 1 - Update from github


To update curriculum go to project folder and run the following commands.

```bash
git pull

php artisan migrate --seed

composer update

npm install

npm run
```