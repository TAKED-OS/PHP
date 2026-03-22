# Лабораторная работа №4. Массивы и Функции

## Цель работы

Освоить работу с массивами в PHP, применяя различные операции: создание, добавление, удаление, сортировка и поиск. Закрепить навыки работы с функциями, включая передачу аргументов, возвращаемые значения и анонимные функции.

## Условие

### Задание 1. Работа с массивами

#### Разработать систему управления банковскими транзакциями с возможностью:

- добавления новых транзакций;

- удаления транзакций;

- сортировки транзакций по дате или сумме;

- поиска транзакций по описанию.

##### Задание 1.1. Подготовка среды

1.Убедитесь, что у вас установлен PHP 8+.

2.Создайте новый PHP-файл index.php.

3.В начале файла включите строгую типизацию:

```
<?php

declare(strict_types=1);
```

#### Задание 1.2. Создание массива транзакций

Создайте массив $transactions, содержащий информацию о банковских транзакциях. Каждая транзакция представлена в виде ассоциативного массива с полями:

- id – уникальный идентификатор транзакции;
- date – дата совершения транзакции (YYYY-MM-DD);
- amount – сумма транзакции;
- description – описание назначения платежа;
- merchant – название организации, получившей платеж.

Пример массива:
```
$transactions = [
    [
        "id" => 1,
        "date" => "2019-01-01",
        "amount" => 100.00,
        "description" => "Payment for groceries",
        "merchant" => "Linella",
    ],
    [
        "id" => 2,
        "date" => "2020-02-15",
        "amount" => 75.50,
        "description" => "Dinner with friends",
        "merchant" => "Andy’s Pizza",
    ],
    [
        "id" => 3,
        "date" => "2021-03-10",
        "amount" => 250.00,
        "description" => "Internet payment",
        "merchant" => "Orange",
    ],
    [
        "id" => 4,
        "date" => "2022-07-21",
        "amount" => 50.25,
        "description" => "Taxi ride",
        "merchant" => "Yandex Go",
    ],
];

```

#### Задание 1.3. Вывод списка транзакций

Используйте foreach, чтобы вывести список транзакций в HTML-таблице.

#### Задание 1.4. Реализация функций

Создайте и используйте следующие функции:

1.Создайте функцию calculateTotalAmount(array $transactions): float, которая вычисляет общую сумму всех транзакций.

  - Выведите сумму всех транзакций в конце таблицы.

2.Создайте функцию findTransactionByDescription(string $descriptionPart), которая ищет транзакцию по части описания.

3.Создайте функцию findTransactionById(int $id), которая ищет транзакцию по идентификатору.

  - Реализуйте данную функцию с помощью обычного цикла foreach.

  - Реализуйте данную функцию с помощью функции array_filter (на высшую оценку).

4.Создайте функцию daysSinceTransaction(string $date): int, которая возвращает количество дней между датой транзакции и текущим днем.

  - Добавьте в таблицу столбец с количеством дней с момента транзакции.

5.Создайте функцию addTransaction(int $id, string $date, float $amount, string $description, string $merchant): void для добавления новой транзакции.

  - Примите во внимание, что массив $transactions должен быть доступен внутри функции как глобальная переменная.

#### Задание 1.5. Сортировка транзакций

Отсортируйте транзакции по дате с использованием usort().

Отсортируйте транзакции по сумме (по убыванию).

#### Полный код:
```
<?php

declare(strict_types=1);

$transactions = [
    [
        "id" => 1,
        "date" => "2019-01-01",
        "amount" => 100.00,
        "description" => "Payment for groceries",
        "merchant" => "Linella",
    ],
    [
        "id" => 2,
        "date" => "2020-02-15",
        "amount" => 75.50,
        "description" => "Dinner with friends",
        "merchant" => "Andy’s Pizza",
    ],
    [
        "id" => 3,
        "date" => "2021-03-10",
        "amount" => 250.00,
        "description" => "Internet payment",
        "merchant" => "Orange",
    ],
    [
        "id" => 4,
        "date" => "2022-07-21",
        "amount" => 50.25,
        "description" => "Taxi ride",
        "merchant" => "Yandex Go",
    ],
];


function calculateTotalAmount(array $transactions): float
{
    $total = 0.0;

    foreach ($transactions as $transaction) {
        $total += $transaction["amount"];
    }

    return $total;
}


function findTransactionByDescription(string $descriptionPart): array
{
    global $transactions;

    return array_filter(
        $transactions,
        function (array $transaction) use ($descriptionPart): bool {
            return stripos($transaction["description"], $descriptionPart) !== false;
        }
    );
}


function findTransactionById(int $id): ?array
{
    global $transactions;

    foreach ($transactions as $transaction) {
        if ($transaction["id"] === $id) {
            return $transaction;
        }
    }

    return null;
}


function findTransactionByIdWithFilter(int $id): ?array
{
    global $transactions;

    $filtered = array_filter(
        $transactions,
        function (array $transaction) use ($id): bool {
            return $transaction["id"] === $id;
        }
    );

    if (empty($filtered)) {
        return null;
    }

    return array_values($filtered)[0];
}


function daysSinceTransaction(string $date): int
{
    $transactionDate = new DateTime($date);
    $currentDate = new DateTime();

    $difference = $transactionDate->diff($currentDate);

    return (int)$difference->days;
}


function addTransaction(
    int $id,
    string $date,
    float $amount,
    string $description,
    string $merchant
): void {
    global $transactions;

    $transactions[] = [
        "id" => $id,
        "date" => $date,
        "amount" => $amount,
        "description" => $description,
        "merchant" => $merchant,
    ];
}


addTransaction(5, "2024-11-10", 120.75, "Book purchase", "BookStore");


$foundByDescription = findTransactionByDescription("payment");
$foundById = findTransactionById(3);
$foundByIdFilter = findTransactionByIdWithFilter(2);


$transactionsByDate = $transactions;

usort($transactionsByDate, function (array $a, array $b): int {
    return strtotime($a["date"]) <=> strtotime($b["date"]);
});


$transactionsByAmount = $transactions;

usort($transactionsByAmount, function (array $a, array $b): int {
    return $b["amount"] <=> $a["amount"];
});

$totalAmount = calculateTotalAmount($transactionsByDate);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Transactions</title>
</head>
<body>

    <h1>Bank Transactions</h1>

    <h2>Transactions sorted by date</h2>
    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Date</th>
                <th>Amount</th>
                <th>Description</th>
                <th>Merchant</th>
                <th>Days Since Transaction</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($transactionsByDate as $transaction): ?>
                <tr>
                    <td><?= htmlspecialchars((string)$transaction["id"]) ?></td>
                    <td><?= htmlspecialchars($transaction["date"]) ?></td>
                    <td><?= htmlspecialchars(number_format($transaction["amount"], 2)) ?></td>
                    <td><?= htmlspecialchars($transaction["description"]) ?></td>
                    <td><?= htmlspecialchars($transaction["merchant"]) ?></td>
                    <td><?= htmlspecialchars((string)daysSinceTransaction($transaction["date"])) ?></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="5"><strong>Total Amount</strong></td>
                <td><strong><?= htmlspecialchars(number_format($totalAmount, 2)) ?></strong></td>
            </tr>
        </tbody>
    </table>

    <h2>Transactions sorted by amount (descending)</h2>
    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Date</th>
                <th>Amount</th>
                <th>Description</th>
                <th>Merchant</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($transactionsByAmount as $transaction): ?>
                <tr>
                    <td><?= htmlspecialchars((string)$transaction["id"]) ?></td>
                    <td><?= htmlspecialchars($transaction["date"]) ?></td>
                    <td><?= htmlspecialchars(number_format($transaction["amount"], 2)) ?></td>
                    <td><?= htmlspecialchars($transaction["description"]) ?></td>
                    <td><?= htmlspecialchars($transaction["merchant"]) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Search by description: "payment"</h2>
    <?php if (!empty($foundByDescription)): ?>
        <ul>
            <?php foreach ($foundByDescription as $transaction): ?>
                <li>
                    ID: <?= htmlspecialchars((string)$transaction["id"]) ?> |
                    <?= htmlspecialchars($transaction["description"]) ?> |
                    <?= htmlspecialchars($transaction["merchant"]) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No transactions found.</p>
    <?php endif; ?>

    <h2>Search by ID using foreach</h2>
    <?php if ($foundById !== null): ?>
        <p>
            Found transaction:
            ID <?= htmlspecialchars((string)$foundById["id"]) ?>,
            <?= htmlspecialchars($foundById["description"]) ?>,
            <?= htmlspecialchars($foundById["merchant"]) ?>
        </p>
    <?php else: ?>
        <p>Transaction not found.</p>
    <?php endif; ?>

    <h2>Search by ID using array_filter</h2>
    <?php if ($foundByIdFilter !== null): ?>
        <p>
            Found transaction:
            ID <?= htmlspecialchars((string)$foundByIdFilter["id"]) ?>,
            <?= htmlspecialchars($foundByIdFilter["description"]) ?>,
            <?= htmlspecialchars($foundByIdFilter["merchant"]) ?>
        </p>
    <?php else: ?>
        <p>Transaction not found.</p>
    <?php endif; ?>

</body>
</html>
```

#### Результаты:

<img width="674" height="960" alt="image" src="https://github.com/user-attachments/assets/58fc3e93-255d-4941-84b2-37b74d284e2b" />

### Задание 2. Работа с файловой системой

1.Создайте директорию "image", в которой сохраните не менее 20-30 изображений с расширением .jpg.

2.Затем создайте файл index.php, в котором определите веб-страницу с хедером, меню, контентом и футером.

3.Выведите изображения из директории "image" на веб-страницу в виде галереи.

#### Код:

```
<?php

declare(strict_types=1);

$files = scandir("image");
$count = 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Memes Gallery</title>
</head>
<body>

<!-- HEADER -->
<h1>Memes</h1>

<!-- MENU -->
<div>
    <a href="#">Funny</a> |
    <a href="#">Cats</a> |
    <a href="#">Top Memes</a> |
    <a href="#">Random</a>
</div>

<hr>

<!-- CONTENT -->
<table>

<?php
echo "<tr>";

foreach ($files as $file) {
    if ($file !== "." && $file !== ".." && pathinfo($file, PATHINFO_EXTENSION) === "jpg") {

        echo "<td style='padding:10px;'>";
        echo "<img src='image/$file' width='200'>";
        echo "</td>";

        $count++;

        if ($count % 3 == 0) {
            echo "</tr><tr>";
        }

        if ($count == 9) {
            break;
        }
    }
}

echo "</tr>";
?>

</table>

<hr>

<!-- FOOTER -->
<p>USM 2024</p>

</body>
</html>
```

#### Результат:

<img width="648" height="795" alt="image" src="https://github.com/user-attachments/assets/2cb79246-092c-4f24-a7c3-9d8e7c1c48a5" />

### Контрольные вопросы

1.Что такое массивы в PHP?

Массив — это переменная, которая хранит несколько значений сразу.
```
$numbers = [1, 2, 3, 4];
```

В одной переменной лежит сразу 4 числа.

2.Каким образом можно создать массив в PHP?

- Короткий способ (самый популярный)
```
$fruits = ["apple", "banana", "orange"];
```
- Старый способ (тоже используется)
```
$fruits = array("apple", "banana", "orange");
```

- Ассоциативный массив (ключ → значение)
```
$user = [
    "name" => "Petru",
    "age" => 21
];
```

Тут уже не просто список, а пары:

- name → Petru
- age → 21

3.Для чего используется цикл foreach?

foreach нужен, чтобы перебрать массив (пройтись по всем элементам).
```
$fruits = ["apple", "banana", "orange"];

foreach ($fruits as $fruit) {
    echo $fruit;
}
```

Что происходит:

- берётся каждый элемент массива
- кладётся в $fruit
- выполняется код внутри
