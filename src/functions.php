<?php
declare(strict_types=1);

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function loadJsonFile(string $path): array
{
    if (!is_file($path)) {
        throw new RuntimeException('Файл не найден: ' . basename($path));
    }

    $raw = file_get_contents($path);
    if ($raw === false) {
        throw new RuntimeException('Не удалось прочитать файл: ' . basename($path));
    }

    try {
        $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
    } catch (JsonException $exception) {
        throw new RuntimeException('Файл ' . basename($path) . ' содержит некорректный JSON.', 0, $exception);
    }

    if (!is_array($data)) {
        throw new RuntimeException('В файле ' . basename($path) . ' ожидался JSON-массив.');
    }

    return $data;
}

function ensureNotesStorage(string $datasetPath, string $storagePath): void
{
    if (is_file($storagePath)) {
        return;
    }

    $directory = dirname($storagePath);
    if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
        throw new RuntimeException('Не удалось создать папку data.');
    }

    if (!copy($datasetPath, $storagePath)) {
        throw new RuntimeException('Не удалось подготовить файл data/notes.json из dataset.');
    }
}

function saveNotes(string $path, array $notes): void
{
    $json = json_encode(
        array_values($notes),
        JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
    );

    if (file_put_contents($path, $json . PHP_EOL, LOCK_EX) === false) {
        throw new RuntimeException('Не удалось сохранить заметки в JSON.');
    }
}

function findUserById(array $users, int $userId): ?array
{
    foreach ($users as $user) {
        if ((int) ($user['id'] ?? 0) === $userId) {
            return $user;
        }
    }

    return null;
}

function renderTemplate(string $templatePath, array $data = []): void
{
    extract($data, EXTR_SKIP);
    require $templatePath;
}
