<?php

declare(strict_types=1);

require_once __DIR__ . '/../classes/Validator.php';

$errors = [];
$success = null;
$form = ['name' => '', 'email' => '', 'phone' => ''];

try {
    $db = Database::connection();
    $result = $db->query('SELECT id, name, email, phone, created_at FROM employees ORDER BY id DESC');
    $employees = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
} catch (Throwable $e) {
    $employees = [];
    $errors['_database'] = [$e->getMessage()];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form = [
        'name' => trim((string)($_POST['name'] ?? '')),
        'email' => trim((string)($_POST['email'] ?? '')),
        'phone' => trim((string)($_POST['phone'] ?? '')),
    ];

    try {
        $errors = Validator::employee($form);
        if (!$errors) {
            $stmt = $db->prepare('INSERT INTO employees (name, email, phone) VALUES (?, ?, ?)');
            $stmt->bind_param('sss', $form['name'], $form['email'], $form['phone']);
            $stmt->execute();
            $stmt->close();
            $success = 'Employee registered successfully.';
            $form = ['name' => '', 'email' => '', 'phone' => ''];
            $result = $db->query('SELECT id, name, email, phone, created_at FROM employees ORDER BY id DESC');
            $employees = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        }
    } catch (Throwable $e) {
        $errors['_database'] = [$e->getMessage()];
    }
}

function old(string $key, array $form): string
{
    return htmlspecialchars((string)($form[$key] ?? ''), ENT_QUOTES, 'UTF-8');
}

function invalid(string $key, array $errors): string
{
    return isset($errors[$key]) ? 'is-invalid' : '';
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Modular Validation Utility</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<main class="container">
    <header>
        <h1>Modular Validation Utility</h1>
        <p>Centralized PHP OOP validation with MySQL uniqueness checks.</p>
    </header>

    <?php if ($success): ?>
        <div class="alert success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if (isset($errors['_database'])): ?>
        <div class="alert error">
            <?= htmlspecialchars($errors['_database'][0]) ?>
            <small>Check the database credentials and run sql/schema.sql.</small>
        </div>
    <?php endif; ?>

    <section class="card">
        <h2>Employee Registration</h2>
        <form method="post" novalidate>
            <label for="name">Name</label>
            <input id="name" name="name" type="text" value="<?= old('name', $form) ?>" class="<?= invalid('name', $errors) ?>" data-validation-key="name">
            <?php foreach (($errors['name'] ?? []) as $message): ?>
                <div class="field-error"><?= htmlspecialchars($message) ?></div>
            <?php endforeach; ?>

            <label for="email">Email</label>
            <input id="email" name="email" type="email" value="<?= old('email', $form) ?>" class="<?= invalid('email', $errors) ?>" data-validation-key="email">
            <?php foreach (($errors['email'] ?? []) as $message): ?>
                <div class="field-error"><?= htmlspecialchars($message) ?></div>
            <?php endforeach; ?>

            <label for="phone">Phone</label>
            <input id="phone" name="phone" type="tel" value="<?= old('phone', $form) ?>" class="<?= invalid('phone', $errors) ?>" data-validation-key="phone">
            <?php foreach (($errors['phone'] ?? []) as $message): ?>
                <div class="field-error"><?= htmlspecialchars($message) ?></div>
            <?php endforeach; ?>

            <button type="submit">Register Employee</button>
        </form>
    </section>

    <section class="card">
        <h2>Existing Employees</h2>
        <?php if (!$employees): ?>
            <p>No records found.</p>
        <?php else: ?>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Created</th></tr></thead>
                    <tbody>
                    <?php foreach ($employees as $employee): ?>
                        <tr>
                            <td><?= (int)$employee['id'] ?></td>
                            <td><?= htmlspecialchars($employee['name']) ?></td>
                            <td><?= htmlspecialchars($employee['email']) ?></td>
                            <td><?= htmlspecialchars($employee['phone']) ?></td>
                            <td><?= htmlspecialchars($employee['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</main>
<script src="assets/app.js"></script>
</body>
</html>
