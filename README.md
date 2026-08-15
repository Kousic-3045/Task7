# Task 7 – Modular Validation Utility Class

A PHP + MySQL mini-project implementing an OOP `Validator` utility with static validation methods and a prepared-statement uniqueness check.

## Requirements covered

- `Validator::email()` – validates email addresses.
- `Validator::phone()` – validates 10–15 digit phone numbers, with optional `+` and common separators.
- `Validator::stringLength()` – returns an array of length validation errors.
- `Validator::isUnique()` – checks a specified table/column for duplicates using MySQLi prepared statements.
- `Validator::employee()` – combines field-specific rules for registration/update workflows.
- MySQLi `prepare()`, `bind_param()`, `get_result()` used in the uniqueness query.
- Registration form displays field-specific validation arrays.
- Invalid fields receive the `.is-invalid` CSS class.
- SQL schema uses an `employees` table and a unique email constraint.

## Project structure

```text
Task7/
├── classes/
│   └── Validator.php
├── config/
│   └── database.php
├── public/
│   ├── index.php
│   └── assets/
│       ├── app.js
│       └── style.css
├── sql/
│   └── schema.sql
├── .env.example
├── .gitignore
└── README.md
```

## Credentials / configuration

Copy `.env.example` to your environment or export the variables before running:

```text
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=validation_demo
DB_USER=root
DB_PASSWORD=your_password
```

Do **not** commit real database passwords to GitHub.

## Workflow

1. Create the database/table using `sql/schema.sql`.
2. Configure the database environment variables.
3. Start PHP's development server from the project root:

```bash
php -S localhost:8000 -t public
```

4. Open `http://localhost:8000`.
5. Submit a valid employee.
6. Try an invalid email, invalid phone, or short name to see field-specific errors.
7. Submit `demo@example.com` again to verify `Validator::isUnique()` detects an existing database email.

## Update workflow

For an existing employee, call `Validator::employee($data, $employeeId)` so the employee's own email is excluded from the uniqueness check. The `isUnique()` method supports this through `$excludeId` and `$idColumn`.

## Validation examples

```php
Validator::email('user@example.com');
Validator::phone('+919876543210');
Validator::stringLength('Kousic', 2, 100);
Validator::isUnique('employees', 'email', 'user@example.com');
```

## Security notes

- User values are passed through prepared statements for SQL operations.
- Table and column identifiers are checked with a strict identifier pattern before being interpolated into SQL.
- Output is escaped with `htmlspecialchars()`.
- Never place production credentials in source code or commit them to GitHub.

## Acceptance criteria

The class returns boolean/array validation results, field errors are displayed cleanly, invalid inputs receive `.is-invalid`, and duplicate database emails are successfully detected.
