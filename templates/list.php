<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Практическая работа № 6 — Менеджер заметок</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
<header class="topbar">
    <a class="brand" href="index.php?page=notes"><span class="brand-mark">06</span> Личные заметки</a>
    <nav><a href="index.php?page=notes">Заметки</a><a href="index.php?page=about">О проекте</a></nav>
</header>
<main class="container">
    <section class="hero">
        <div class="eyebrow">PHP · JSON · SESSION · ШАБЛОНЫ</div>
        <h1>Менеджер заметок</h1>
        <p>Записи хранятся в JSON-файле, имя пользователя — в сессии. Обработка запросов находится в точке входа, а HTML списка вынесен в отдельный шаблон.</p>
        <div class="hero-meta"><span>Текущий пользователь: <strong><?= e($displayName) ?></strong></span><span>Состояние: <?= $isGuest ? 'гость' : 'сессия активна' ?></span></div>
    </section>

    <?php if ($storageError !== null): ?>
        <div class="alert alert-error"><strong>Проблема с данными.</strong> <?= e($storageError) ?><br>Проверьте <code>data/notes.json</code>. Исходная копия находится в <code>dataset/notes.json</code>.</div>
    <?php endif; ?>

    <?php if (is_array($flash)): ?>
        <div class="alert <?= ($flash['type'] ?? '') === 'success' ? 'alert-success' : 'alert-error' ?>"><?= e($flash['message'] ?? '') ?></div>
    <?php endif; ?>

    <section class="stats">
        <article class="stat-card"><span>Всего заметок</span><strong><?= count($notes) ?></strong><small>Файл data/notes.json</small></article>
        <article class="stat-card"><span>Выполнено</span><strong><?= $completedCount ?></strong><small>Отмечены как готовые</small></article>
        <article class="stat-card"><span>Осталось</span><strong><?= count($notes) - $completedCount ?></strong><small>Можно изменить статус</small></article>
    </section>

    <section class="two-columns">
        <article class="panel">
            <div class="section-heading"><div><div class="eyebrow">ФАЙЛОВОЕ ХРАНИЛИЩЕ</div><h2>Мои заметки</h2></div><span class="counter"><?= count($notes) ?> записей</span></div>

            <?php if ($formErrors !== []): ?>
                <div class="alert alert-error"><ul><?php foreach ($formErrors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div>
            <?php endif; ?>

            <form class="add-form" method="post" action="index.php?page=notes">
                <input type="hidden" name="action" value="add_note">
                <label for="title">Новая заметка</label>
                <div class="inline-form">
                    <input id="title" name="title" type="text" maxlength="120" required value="<?= e($oldTitle) ?>" placeholder="Например, подготовить отчёт" <?= $storageError !== null ? 'disabled' : '' ?>>
                    <button class="button" type="submit" <?= $storageError !== null ? 'disabled' : '' ?>>Добавить</button>
                </div>
            </form>

            <div class="note-list">
                <?php foreach ($notes as $note): ?>
                    <article class="note-item <?= !empty($note['done']) ? 'is-done' : '' ?>">
                        <form method="post" action="index.php?page=notes">
                            <input type="hidden" name="action" value="toggle_note">
                            <input type="hidden" name="note_id" value="<?= (int) ($note['id'] ?? 0) ?>">
                            <button class="check-button" type="submit" aria-label="Изменить статус заметки" <?= $storageError !== null ? 'disabled' : '' ?>><?= !empty($note['done']) ? '✓' : '' ?></button>
                        </form>
                        <div class="note-content"><strong><?= e($note['title'] ?? '') ?></strong><small>Запись №<?= (int) ($note['id'] ?? 0) ?></small></div>
                        <span class="note-state"><?= !empty($note['done']) ? 'Готово' : 'В работе' ?></span>
                    </article>
                <?php endforeach; ?>
                <?php if ($notes === []): ?><p class="empty">Пока нет заметок. Добавьте первую запись.</p><?php endif; ?>
            </div>
        </article>

        <aside class="panel">
            <div class="eyebrow">ПОЛЬЗОВАТЕЛЬСКОЕ СОСТОЯНИЕ</div>
            <h2>Сессия пользователя</h2>
            <p class="muted">Имя хранится в <code>$_SESSION</code> и остаётся после обновления страницы в этой сессии браузера.</p>
            <div class="user-card"><span class="avatar"><?= e($initial) ?></span><div><strong><?= e($displayName) ?></strong><small><?= $isGuest ? 'Имя ещё не выбрано' : 'Имя сохранено в сессии' ?></small></div></div>
            <form class="stack-form" method="post" action="index.php?page=notes">
                <input type="hidden" name="action" value="set_user">
                <label for="user_id">Выбрать пользователя из users.json</label>
                <select id="user_id" name="user_id">
                    <?php foreach ($users as $user): ?>
                        <option value="<?= (int) $user['id'] ?>" <?= $currentUserId === (int) $user['id'] ? 'selected' : '' ?>><?= e($user['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <button class="button button-secondary" type="submit">Сохранить в сессию</button>
            </form>
            <form method="post" action="index.php?page=notes">
                <input type="hidden" name="action" value="clear_user">
                <button class="button button-ghost" type="submit">Очистить имя пользователя</button>
            </form>
        </aside>
    </section>

    <section class="panel scenarios">
        <div class="eyebrow">ПРОВЕРКА SESSION</div><h2>Сценарии из session_scenarios.json</h2>
        <div class="scenario-grid">
            <?php foreach ($scenarios as $scenario): ?>
                <article class="scenario-card">
                    <strong><?= e($scenario['scenario'] ?? '') ?></strong>
                    <?php if (isset($scenario['value'])): ?><span>Значение: <?= e($scenario['value']) ?></span><?php endif; ?>
                    <small>Ожидаемый результат: <code><?= e($scenario['expected'] ?? '') ?></code></small>
                </article>
            <?php endforeach; ?>
        </div>
        <p class="hint">Проверьте сценарии вручную: новая сессия показывает «Гость», выбор Student A сохраняет имя, очистка возвращает значение по умолчанию. В приватном окне создаётся отдельная сессия.</p>
    </section>
    <footer class="footer">Практическая работа № 6 · Шаблон получает готовые данные и не читает JSON самостоятельно.</footer>
</main>
</body>
</html>
