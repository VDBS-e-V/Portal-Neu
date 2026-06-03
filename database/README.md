# Database migrations & seeds

Place your MySQL credentials in environment variables or pass them to the CLI when running the commands below.

Apply migrations (bash):
```
DB_NAME="your_database" ; DB_USER="root"
for f in database/migrations/*.sql; do
  mysql -u "$DB_USER" -p "$DB_NAME" < "$f"
done
```

Apply migrations (PowerShell):
```
$dbName = 'your_database'
$user = 'root'
Get-ChildItem database/migrations/*.sql | ForEach-Object { mysql -u $user -p $dbName < $_.FullName }
```

Run seed (after generating a bcrypt hash for the admin password and replacing `<BCRYPT_HASH>`):
```
mysql -u <user> -p <database> < database/seeds/seed_initial_data.sql
```

Notes:
- Replace `<BCRYPT_HASH>` in `seed_initial_data.sql` with a secure hash produced locally (e.g. `php -r "echo password_hash('ChangeMe123!', PASSWORD_DEFAULT).PHP_EOL;"`).
- Migrations assume MySQL >= 5.7 for `JSON` support. Adjust types if needed.
