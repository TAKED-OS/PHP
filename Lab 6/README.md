# Лабораторная работа №6. Обработка и валидация форм

## Цель работы

Освоить основные принципы работы с HTML-формами в PHP, включая отправку данных на сервер и их обработку, включая валидацию данных.

## Условие

Студенты должны выбрать тему проекта для лабораторной работы, которая будет развиваться на протяжении курса.

Например:

Дневник настроения;

## Шаг 1. Определение модели данных

- id (int) — уникальный ID

- title (string) — короткое название записи

- description (text) — что произошло (подробно)

- mood (enum):

  - happy

  - neutral

  - sad

  - angry

- trigger (enum) — причина:

  - work

  - people

  - health

  - random

- created_at (date) — дата записи

- sleep_hours (int) — сколько спал

- is_social_day (checkbox) — был ли день с общением

минимум 6 полей;

хотя бы 1 поле с типом string (текст);

хотя бы 1 поле с типом date (дата);

хотя бы 1 поле с типом enum (ограниченный набор значений) (checkbox);

хотя бы 1 поле с типом text (длинный текст).

## Шаг 2. Создание HTML-формы

1.Разработайте HTML-форму для создания новой записи (например, нового рецепта). Форма должна содержать все необходимые поля, соответствующие модели данных.

2.Форма должна использовать метод POST и отправлять данные на сервер для обработки.

3.Добавьте базовую валидацию на стороне клиента (например, с помощью атрибутов required, minlength, maxlength и т.д.) для улучшения пользовательского опыта.

4.Убедитесь, что форма корректно отображается и работает в браузере.

<img width="1847" height="975" alt="image" src="https://github.com/user-attachments/assets/9d806666-60c3-41f1-8da0-ee79a385c217" />

```
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Дневник настроения</title>

    <!-- Красивый шрифт -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #1e1e2f;
            color: #ffffff;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 30px;
        }

        .title {
            text-align: center;
            font-family: 'Poppins', sans-serif;
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #ffffff;
            letter-spacing: 1px;
        }

        .container {
            background: #2c2c3e;
            padding: 25px;
            border-radius: 10px;
            width: 350px;
            box-shadow: 0 0 10px rgba(0,0,0,0.5);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            font-size: 14px;
        }

        input, textarea, select {
            width: 100%;
            margin-top: 5px;
            margin-bottom: 15px;
            padding: 8px;
            border: none;
            border-radius: 5px;
        }

        input[type="checkbox"] {
            width: auto;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #6c63ff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }

        button:hover {
            background-color: #574fd6;
        }
    </style>
</head>

<body>

<div class="title">Дневник настроения</div>

<div class="container">
    <h2>Добавить запись</h2>

    <form action="process.php" method="POST">

        <label>Название:</label>
        <input type="text" name="title" required minlength="3" maxlength="100">

        <label>Описание:</label>
        <textarea name="description" required minlength="10" maxlength="1000"></textarea>

        <label>Настроение:</label>
        <select name="mood" required>
            <option value="">Выберите</option>
            <option value="happy">Счастлив</option>
            <option value="neutral">Нейтрально</option>
            <option value="sad">Грусть</option>
            <option value="angry">Злость</option>
        </select>

        <label>Причина:</label>
        <select name="trigger" required>
            <option value="">Выберите</option>
            <option value="work">Работа</option>
            <option value="people">Люди</option>
            <option value="health">Здоровье</option>
            <option value="random">Случайное</option>
        </select>

        <label>Дата:</label>
        <input type="date" name="created_at" required>

        <label>Часы сна:</label>
        <input type="number" name="sleep_hours" min="0" max="24" required>

        <label>
            <input type="checkbox" name="is_social_day">
            Был ли день с общением
        </label>

        <button type="submit">Сохранить</button>

    </form>
</div>

</body>
</html>

```

## Шаг 3. Обработка данных на сервере

1.Создайте PHP-скрипт для обработки данных, отправленных из формы. Этот скрипт должен:

  - принимать данные из $_POST;

  - выполнять базовую валидацию данных (например, проверять, что обязательные поля заполнены, что дата имеет правильный формат и т.д.);

  - сохранять данные в файл (например, data.txt);

  - возвращать пользователю сообщение об успешной отправке или об ошибках валидации.

Убедитесь, что данные сохраняются в читаемом формате (например, JSON или CSV) для удобства последующего использования.

## Шаг 4. Вывод данных

1.Создайте PHP-скрипт для чтения данных из файла и отображения их в виде HTML-таблицы.

2.Таблица должна отображать все записи, сохранённые в файле, и быть отформатирована для удобства чтения (например, с помощью CSS).

3.Добавьте возможность сортировки данных по различным полям (например, по дате создания, по категории и т.д.) для улучшения пользовательского опыта.

<img width="1860" height="511" alt="image" src="https://github.com/user-attachments/assets/77e648ad-24e1-4c50-b2ad-0ed83ce8e0a3" />

```
<?php

$file = "data.txt";

// читаем данные
$entries = [];

if (file_exists($file)) {
    $json = file_get_contents($file);
    $entries = json_decode($json, true);

    if (!is_array($entries)) {
        $entries = [];
    }
}

// ===== ПАРСИНГ ДАТЫ (поддержка 2 форматов) =====
function parseDate($date) {
    if (!$date) return 0;

    // формат из input type="date" → YYYY-MM-DD
    $d = DateTime::createFromFormat('Y-m-d', $date);
    if ($d) return $d->getTimestamp();

    // старый формат → DD.MM.YYYY
    $d = DateTime::createFromFormat('d.m.Y', $date);
    if ($d) return $d->getTimestamp();

    return 0;
}

// ===== СОРТИРОВКА =====
$sort = $_GET['sort'] ?? 'date_desc';

usort($entries, function ($a, $b) use ($sort) {

    $timeA = parseDate($a['created_at'] ?? '');
    $timeB = parseDate($b['created_at'] ?? '');

    switch ($sort) {

        case 'date_asc': // старые сначала
            return $timeA <=> $timeB;

        case 'date_desc': // новые сначала
            return $timeB <=> $timeA;

        case 'mood':
            return strcmp($a['mood'], $b['mood']);

        case 'trigger':
            return strcmp($a['trigger'], $b['trigger']);

        default:
            return 0;
    }
});

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Дневник настроения</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #1e1e2f;
            color: #fff;
            padding: 20px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .controls {
            text-align: center;
            margin-bottom: 20px;
        }

        .controls a {
            display: inline-block;
            margin: 5px;
            padding: 8px 12px;
            background: #6c63ff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .controls a:hover {
            background: #574fd6;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #2c2c3e;
        }

        th, td {
            padding: 10px;
            border: 1px solid #444;
            text-align: center;
        }

        th {
            background: #3a3a55;
        }

        tr:nth-child(even) {
            background: #252538;
        }
    </style>
</head>

<body>

<h2>📊 Дневник настроения</h2>

<div class="controls">
    <a href="?sort=date_desc">📅 Новые</a>
    <a href="?sort=date_asc">📅 Старые</a>
    <a href="?sort=mood">😊 Настроение</a>
    <a href="?sort=trigger">⚡ Причина</a>
</div>

<table>
    <tr>
        <th>Название</th>
        <th>Описание</th>
        <th>Настроение</th>
        <th>Причина</th>
        <th>Дата</th>
        <th>Сон</th>
        <th>Общение</th>
    </tr>

    <?php if (empty($entries)): ?>
        <tr>
            <td colspan="7">Нет записей</td>
        </tr>
    <?php else: ?>
        <?php foreach ($entries as $e): ?>
            <tr>
                <td><?= htmlspecialchars($e['title'] ?? '') ?></td>
                <td><?= htmlspecialchars($e['description'] ?? '') ?></td>
                <td><?= htmlspecialchars($e['mood'] ?? '') ?></td>
                <td><?= htmlspecialchars($e['trigger'] ?? '') ?></td>
                <td><?= htmlspecialchars($e['created_at'] ?? '') ?></td>
                <td><?= (int)($e['sleep_hours'] ?? 0) ?></td>
                <td><?= !empty($e['is_social_day']) ? "Да" : "Нет" ?></td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>

</table>

</body>
</html>
```

## Шаг 5. Дополнительные функции

### Задание 1. Добавление интерфейса

Модифицируйте систему валидации, добавив интерфейс для валидаторов. Это позволит унифицировать работу отдельных валидаторов и сделать архитектуру приложения более расширяемой.

```
<?php

interface Validator {
    public function validate($data): ?string;
}

class TitleValidator implements Validator {
    public function validate($data): ?string {
        if (strlen(trim($data)) < 3) {
            return "Название слишком короткое";
        }
        return null;
    }
}

class DescriptionValidator implements Validator {
    public function validate($data): ?string {
        if (strlen(trim($data)) < 10) {
            return "Описание слишком короткое";
        }
        return null;
    }
}

class DateValidator implements Validator {
    public function validate($data): ?string {

        $d = DateTime::createFromFormat('Y-m-d', $data);

        if (!$d || $d->format('Y-m-d') !== $data) {
            return "Неверная дата";
        }

        return null;
    }
}

class SleepValidator implements Validator {
    public function validate($data): ?string {

        if (!is_numeric($data) || $data < 0 || $data > 24) {
            return "Часы сна должны быть от 0 до 24";
        }

        return null;
    }
}
```

### Задание 2. ООП-реализация
Реализуйте решение задачи с использованием объектно-ориентированного программирования. 
Для этого необходимо разработать классы, отвечающие за управление формой, валидацию данных и их сохранение. 
Такой подход способствует лучшей организации кода, повышает его читаемость и облегчает сопровождение приложения.

```
<?php

require_once "Entry.php";
require_once "Storage.php";
require_once "EntryController.php";
require_once "Validator.php";

// валидация (из 5.1)
$data = $_POST;

$validators = [
    'title' => new TitleValidator(),
    'description' => new DescriptionValidator(),
    'created_at' => new DateValidator(),
    'sleep_hours' => new SleepValidator()
];

$errors = [];

foreach ($validators as $field => $validator) {
    $error = $validator->validate($data[$field] ?? null);
    if ($error) {
        $errors[] = $error;
    }
}

if (!empty($errors)) {
    foreach ($errors as $e) {
        echo "<p>$e</p>";
    }
    exit;
}

// ООП сохранение
$storage = new Storage("data.txt");
$controller = new EntryController($storage);

$controller->store($data);

echo "<h2>Успешно сохранено</h2>";
```

1.Какие существуют методы отправки данных из формы на сервер? Какие методы поддерживает HTML-форма?

В HTML-форме основные методы:

```
<form method="GET">
```
и

```
<form method="POST">
```

HTML-форма напрямую поддерживает только:

GET — данные передаются через URL.

Пример:

```
site.php?name=Max&age=22
```

Используется для поиска, фильтров, сортировки.

POST — данные передаются внутри тела запроса, не видны в URL.

Используется для регистрации, логина, добавления записей, отправки больших данных.

2.Какие глобальные переменные используются для доступа к данным формы в PHP?

В PHP используются суперглобальные массивы:

```
$_GET
```

Для данных, отправленных методом GET.

```
$_POST
```

Для данных, отправленных методом POST.

```
$_REQUEST
```

Может содержать данные из $_GET, $_POST и $_COOKIE, но лучше не злоупотреблять, потому что не всегда понятно, откуда пришли данные.

Также часто используется:
```
$_FILES
```

Для загруженных файлов.

Пример:
```
$name = $_POST['name'] ?? '';
```

3.Как обеспечить безопасность при обработке данных из формы (например, защититься от XSS)?

Главное правило: нельзя доверять данным от пользователя.

Чтобы защититься от XSS, перед выводом данных на страницу используют:

htmlspecialchars()

Пример:
```
echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
```
Это превращает опасные символы вроде <script> в безопасный текст.

Также нужно:

```
trim()
```

убирать лишние пробелы;

```
filter_var()
```

проверять email, числа, URL;

```
isset()
```

или
```
?? ''
```
проверять, существует ли поле;

для SQL-запросов использовать prepared statements, а не вставлять данные напрямую в SQL.

Пример безопасной обработки:
```
$name = trim($_POST['name'] ?? '');
$safeName = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');

echo $safeName;
```

Коротко: GET и POST отправляют данные, $_GET и $_POST их читают, htmlspecialchars() защищает вывод от XSS.
