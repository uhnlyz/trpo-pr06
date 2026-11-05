<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/../src/functions.php';

$root = dirname(__DIR__);
$datasetPath = $root . '/dataset';
$storagePath = $root . '/data/notes.json';

$storageError = null;
$users = [];
$scenarios = [];
$notes = [];

try {
    $users = loadJsonFile($datasetPath . '/users.json');
    $scenarios = loadJsonFile($datasetPath . '/session_scenarios.json');
    ensureNotesStorage($datasetPath . '/notes.json', $storagePath);
    $notes = loadJsonFile($storagePath);
} catch (Throwable $exception) {
    $storageError = $exception->getMessage();
}

function redirectToNotes(): void
{
    header('Location: index.php?page=notes');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string) ($_POST['action'] ?? '');

    if ($action === 'set_user') {
        $userId = (int) ($_POST['user_id'] ?? 0);
        $user = findUserById($users, $userId);

        if ($user === null) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Выберите пользователя из списка.'];
        } else {
            $_SESSION['display_name'] = (string) $user['name'];
            $_SESSION['user_id'] = (int) $user['id'];
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Имя сохранено в текущей сессии.'];
        }

        redirectToNotes();
    }

    if ($action === 'clear_user') {
        unset($_SESSION['display_name'], $_SESSION['user_id']);
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Сессия очищена: снова отображается гость.'];
        redirectToNotes();
    }

    if ($action === 'add_note') {
        if ($storageError !== null) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Запись недоступна: ' . $storageError];
            redirectToNotes();
        }

        $title = trim((string) ($_POST['title'] ?? ''));
        $errors = [];

        if ($title === '') {
            $errors[] = 'Введите текст заметки.';
        } elseif (preg_match_all('/./us', $title) > 120) {
            $errors[] = 'Заметка должна содержать не более 120 символов.';
        }

        if ($errors !== []) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['old_title'] = $title;
            redirectToNotes();
        }

        $nextId = 1;
        foreach ($notes as $note) {
            $nextId = max($nextId, (int) ($note['id'] ?? 0) + 1);
        }

        $notes[] = [
            'id' => $nextId,
            'title' => $title,
            'done' => false,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        try {
            saveNotes($storagePath, $notes);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Заметка добавлена и сохранена в JSON.'];
        } catch (Throwable $exception) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => $exception->getMessage()];
        }

        redirectToNotes();
    }

    if ($action === 'toggle_note') {
        if ($storageError === null) {
            $noteId = (int) ($_POST['note_id'] ?? 0);
            foreach ($notes as &$note) {
                if ((int) ($note['id'] ?? 0) === $noteId) {
                    $note['done'] = !(bool) ($note['done'] ?? false);
                    break;
                }
            }
            unset($note);

            try {
                saveNotes($storagePath, $notes);
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'Статус заметки обновлён.'];
            } catch (Throwable $exception) {
                $_SESSION['flash'] = ['type' => 'error', 'message' => $exception->getMessage()];
            }
        }

        redirectToNotes();
    }

    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Неизвестное действие формы.'];
    redirectToNotes();
}

$page = (string) ($_GET['page'] ?? 'notes');
$displayName = (string) ($_SESSION['display_name'] ?? 'Гость');
$isGuest = !isset($_SESSION['display_name']);
$currentUserId = (int) ($_SESSION['user_id'] ?? 0);
$initial = preg_match('/^./u', $displayName, $match) ? $match[0] : 'Г';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$formErrors = $_SESSION['form_errors'] ?? [];
$oldTitle = (string) ($_SESSION['old_title'] ?? '');
unset($_SESSION['form_errors'], $_SESSION['old_title']);

$completedCount = count(array_filter(
    $notes,
    static fn (array $note): bool => (bool) ($note['done'] ?? false)
));

$viewData = [
    'notes' => $notes,
    'users' => $users,
    'scenarios' => $scenarios,
    'displayName' => $displayName,
    'initial' => $initial,
    'isGuest' => $isGuest,
    'currentUserId' => $currentUserId,
    'flash' => $flash,
    'formErrors' => $formErrors,
    'oldTitle' => $oldTitle,
    'storageError' => $storageError,
    'completedCount' => $completedCount,
];

if ($page === 'notes') {
    renderTemplate(__DIR__ . '/../templates/list.php', $viewData);
    exit;
}

if ($page === 'about') {
    renderTemplate(__DIR__ . '/../templates/about.php', $viewData);
    exit;
}

http_response_code(404);
renderTemplate(__DIR__ . '/../templates/not-found.php', ['requestedPage' => $page]);
