<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>О проекте — Практическая работа № 6</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
<header class="topbar">
    <a class="brand" href="index.php?page=notes"><span class="brand-mark">06</span> Личные заметки</a>
    <nav><a href="index.php?page=notes">Заметки</a><a href="index.php?page=about">О проекте</a></nav>
</header>
<main class="container">
    <section class="hero">
        <div class="eyebrow">СТРУКТУРА ПРИЛОЖЕНИЯ</div>
        <h1>Как устроен проект</h1>
        <p>Работа показывает первый шаг к MVC: точка входа обрабатывает запрос, функции работают с JSON, а шаблоны отвечают за HTML.</p>
    </section>
    <section class="panel architecture">
        <div class="architecture-step"><span>1</span><div><strong>public/index.php</strong><p>Запускает сессию, определяет действие формы, подготавливает данные и выбирает шаблон.</p></div></div>
        <div class="architecture-step"><span>2</span><div><strong>src/functions.php</strong><p>Содержит функции чтения и записи JSON, поиска пользователя и подключения шаблона.</p></div></div>
        <div class="architecture-step"><span>3</span><div><strong>data/notes.json</strong><p>Рабочий файл, в который сохраняются новые заметки и их статусы.</p></div></div>
        <div class="architecture-step"><span>4</span><div><strong>templates/list.php</strong><p>Получает подготовленные переменные и выводит HTML. Сам шаблон не читает файлы и не меняет сессию.</p></div></div>
    </section>
    <footer class="footer"><a href="index.php?page=notes">Вернуться к заметкам</a></footer>
</main>
</body>
</html>
