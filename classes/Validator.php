<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

final class Validator
{
    private function __construct()
    {
    }

    public static function email(string $value): bool
    {
        return filter_var(trim($value), FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function phone(string $value): bool
    {
        $normalized = preg_replace('/[\s().-]+/', '', trim($value));
        return $normalized !== null && preg_match('/^\+?[0-9]{10,15}$/', $normalized) === 1;
    }

    public static function stringLength(string $value, int $min = 1, ?int $max = null): array
    {
        $length = mb_strlen(trim($value));
        $errors = [];

        if ($length < $min) {
            $errors[] = "Must contain at least {$min} characters.";
        }
        if ($max !== null && $length > $max) {
            $errors[] = "Must not exceed {$max} characters.";
        }
        return $errors;
    }

    public static function isUnique(
        string $table,
        string $column,
        string $value,
        ?int $excludeId = null,
        string $idColumn = 'id'
    ): bool {
        if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $table)
            || !preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $column)
            || !preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $idColumn)) {
            throw new InvalidArgumentException('Invalid table or column identifier.');
        }

        $db = Database::connection();

        if ($excludeId === null) {
            $sql = "SELECT 1 FROM `{$table}` WHERE `{$column}` = ? LIMIT 1";
            $stmt = $db->prepare($sql);
            if (!$stmt) {
                throw new RuntimeException('Failed to prepare uniqueness query: ' . $db->error);
            }
            $stmt->bind_param('s', $value);
        } else {
            $sql = "SELECT 1 FROM `{$table}` WHERE `{$column}` = ? AND `{$idColumn}` <> ? LIMIT 1";
            $stmt = $db->prepare($sql);
            if (!$stmt) {
                throw new RuntimeException('Failed to prepare uniqueness query: ' . $db->error);
            }
            $stmt->bind_param('si', $value, $excludeId);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;
        $stmt->close();

        return !$exists;
    }

    public static function employee(array $data, ?int $excludeId = null): array
    {
        $errors = [];
        $name = trim((string)($data['name'] ?? ''));
        $email = trim((string)($data['email'] ?? ''));
        $phone = trim((string)($data['phone'] ?? ''));

        $nameErrors = self::stringLength($name, 2, 100);
        if ($name === '') {
            $nameErrors = ['Name is required.'];
        }
        if ($nameErrors) {
            $errors['name'] = $nameErrors;
        }

        if ($email === '') {
            $errors['email'] = ['Email is required.'];
        } elseif (!self::email($email)) {
            $errors['email'] = ['Enter a valid email address.'];
        } elseif (!self::isUnique('employees', 'email', $email, $excludeId)) {
            $errors['email'] = ['This email already exists.'];
        }

        if ($phone === '') {
            $errors['phone'] = ['Phone number is required.'];
        } elseif (!self::phone($phone)) {
            $errors['phone'] = ['Enter a valid phone number with 10-15 digits.'];
        }

        return $errors;
    }
}
