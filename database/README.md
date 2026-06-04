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

Run seed (the CLI replaces `<USER_UUID>` and `<BCRYPT_HASH>` automatically):
```
SEED_ADMIN_PASSWORD="ChangeMe123!" php bin/console seed
```

Notes:
- `php bin/console seed` prompts for an admin password if `SEED_ADMIN_PASSWORD` is not set.
- The seed runner injects a random `<USER_UUID>` value and hashes the provided admin password before executing the SQL.
- Migrations assume MySQL >= 5.7 for `JSON` support. Adjust types if needed.
