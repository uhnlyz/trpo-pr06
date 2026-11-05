<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 — Страница не найдена</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
<header class="topbar"><a class="brand" href="index.php?page=notes"><span class="brand-mark">06</span> Личные заметки</a></header>
<main class="container">
    <section class="hero not-found">
        <div class="eyebrow">ОШИБКА 404</div><h1>Страница не найдена</h1>
        <p>Неизвестное значение параметра page: <code><?= e($requestedPage) ?></code>.</p>
        <a class="button" href="index.php?page=notes">Вернуться к заметкам</a>
    </section>
</main>
</body>
</html>
