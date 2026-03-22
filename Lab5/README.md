# Лабораторная работа №5. Объектно-ориентированное программирование в PHP

## Цель работы
Освоить основы объектно-ориентированного программирования в PHP на практике. Научиться создавать собственные классы, использовать инкапсуляцию для защиты данных, разделять ответственность между классами, а также применять интерфейсы для построения гибкой архитектуры приложения.

## Условие

Необходимо разработать приложение для управления банковскими транзакциями.

Приложение должно позволять:

1.хранить банковские транзакции;

2.добавлять новые транзакции;

3.удалять транзакции;

4.искать транзакции;

5.сортировать транзакции;

6.выполнять вычисления над коллекцией транзакций;

7.выводить данные в виде HTML-таблицы.

В рамках лабораторной работы необходимо использовать объектно-ориентированный подход.

### Задание 1. Включение строгой типизации

В начале файла включите строгую типизацию:
```
<?php

declare(strict_types=1);
```

### Задание 2. Класс Transaction

Создайте класс Transaction, который описывает одну банковскую транзакцию.

Класс должен содержать следующие свойства:

- id — уникальный идентификатор транзакции;
- date — дата транзакции;
- amount — сумма транзакции;
- description — описание платежа;
- merchant — получатель платежа.
Класс должен содержать метод: getDaysSinceTransaction(): int, который возвращает количество дней с момента транзакции до текущей даты.

Требования:

1.Все свойства должны быть приватными.

2.Значения свойств должны задаваться через конструктор.

3.Для получения данных создайте getter-методы.

### Задание 3. Класс TransactionRepository

Создайте класс TransactionRepository, который будет управлять коллекцией транзакций. Этот класс должен отвечать только за хранение данных и базовые операции доступа к ним.

Класс должен:

1.хранить массив объектов Transaction;

2.добавлять новые транзакции;

  - addTransaction(Transaction $transaction): void

3.удалять транзакции по идентификатору;

  - removeTransactionById(int $id): void

4.возвращать полный список транзакций;

  - getAllTransactions(): array

5.находить транзакцию по id.

  - findById(int $id): ?Transaction

Требования:

1.Массив транзакций должен быть приватным свойством класса.

2.Доступ к данным должен осуществляться только через методы класса.

3.Не допускается прямой доступ к массиву транзакций извне.

4.Для методов и свойств используйте строгую типизацию.

### Задание 4. Класс TransactionManager

Создайте класс TransactionManager, который будет использовать TransactionRepository для выполнения бизнес-логики.

TransactionManager не должен создавать транзакции самостоятельно и не должен хранить их внутри себя. Объект TransactionRepository необходимо передать в TransactionManager через конструктор:
```
public function __construct(
    private TransactionRepository $repository
) {
}
```

Класс должен реализовать следующие функции:

1.вычисление общей суммы всех транзакций;

  - calculateTotalAmount(): float

2.вычисление суммы транзакций за определенный период;

  - calculateTotalAmountByDateRange(string $startDate, string $endDate): float

3.подсчет количества транзакций по определенному получателю;

  - countTransactionsByMerchant(string $merchant): int

4.сортировку транзакций по дате;

  - sortTransactionsByDate(): Transaction[]

5.сортировку транзакций по сумме по убыванию.

  - sortTransactionsByAmountDesc(): Transaction[]

Требования:

1.TransactionManager не должен хранить транзакции самостоятельно.

2.Для получения данных он должен обращаться к объекту репозитория.

3.В классе должны быть методы для каждой из перечисленных функций.

4.При необходимости создавайте приватные вспомогательные методы.

### Задание 5. Класс TransactionTableRenderer

Создайте отдельный класс TransactionTableRenderer, который отвечает только за вывод транзакций в HTML. Этот класс должен получать список транзакций и формировать HTML-таблицу.

Класс должен реализовать следующие функции:

- render(array $transactions): string — принимает массив транзакций и возвращает строку с HTML-кодом таблицы.

Метод должен возвращать HTML-таблицу со следующими столбцами:

- ID транзакции;
- дата;
- сумма;
- описание;
- название получателя;
- категория получателя;
- количество дней с момента транзакции.

Требования:

- HTML-код должен генерироваться внутри класса.
- В основном файле должен выполняться только вызов метода render() и вывод результата через echo.
- Класс рекомендуется объявить как final.

### Задание 6. Начальные данные

Создайте не менее 10 объектов Transaction. Каждая транзакция должна содержать:

1.разные даты;

2.разные суммы;

3.разные описания;

4.разных получателей.

После создания объектов добавьте транзакции в TransactionRepository.

### Задание 7. Интерфейс TransactionStorageInterface

После завершения основной реализации сделайте архитектуру более гибкой.

Создайте интерфейс TransactionStorageInterface.

- Интерфейс должен содержать методы:

1.addTransaction(Transaction $transaction): void

2.removeTransactionById(int $id): void

3.getAllTransactions(): array

4.findById(int $id): ?Transaction

Требования:

- TransactionRepository должен реализовывать интерфейс.

- TransactionManager должен принимать через конструктор уже интерфейс, а не конкретный класс.

После изменения конструктор TransactionManager должен выглядеть следующим образом:
```
public function __construct(
    private TransactionStorageInterface $repository
) {
}
```

### Код:
```
<?php

declare(strict_types=1);

/**
 * Интерфейс хранилища транзакций.
 */
interface TransactionStorageInterface
{
    /**
     * Добавляет транзакцию в хранилище.
     *
     * @param Transaction $transaction Объект транзакции.
     * @return void
     */
    public function addTransaction(Transaction $transaction): void;

    /**
     * Удаляет транзакцию по идентификатору.
     *
     * @param int $id Идентификатор транзакции.
     * @return void
     */
    public function removeTransactionById(int $id): void;

    /**
     * Возвращает все транзакции.
     *
     * @return Transaction[] Массив объектов Transaction.
     */
    public function getAllTransactions(): array;

    /**
     * Ищет транзакцию по идентификатору.
     *
     * @param int $id Идентификатор транзакции.
     * @return Transaction|null Найденная транзакция или null.
     */
    public function findById(int $id): ?Transaction;
}

/**
 * Класс, описывающий одну банковскую транзакцию.
 */
class Transaction
{
    /**
     * @param int $id Уникальный идентификатор транзакции.
     * @param string $date Дата транзакции в формате Y-m-d.
     * @param float $amount Сумма транзакции.
     * @param string $description Описание платежа.
     * @param string $merchant Получатель платежа.
     * @param string $category Категория получателя.
     */
    public function __construct(
        private int $id,
        private string $date,
        private float $amount,
        private string $description,
        private string $merchant,
        private string $category
    ) {
    }

    /**
     * Возвращает идентификатор транзакции.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Возвращает дату транзакции.
     *
     * @return string
     */
    public function getDate(): string
    {
        return $this->date;
    }

    /**
     * Возвращает сумму транзакции.
     *
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * Возвращает описание транзакции.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Возвращает название получателя.
     *
     * @return string
     */
    public function getMerchant(): string
    {
        return $this->merchant;
    }

    /**
     * Возвращает категорию получателя.
     *
     * @return string
     */
    public function getCategory(): string
    {
        return $this->category;
    }

    /**
     * Возвращает количество дней с момента транзакции до текущей даты.
     *
     * @return int
     */
    public function getDaysSinceTransaction(): int
    {
        $transactionDate = new DateTime($this->date);
        $currentDate = new DateTime();

        return (int)$transactionDate->diff($currentDate)->days;
    }
}

/**
 * Репозиторий для хранения и базовой работы с транзакциями.
 */
class TransactionRepository implements TransactionStorageInterface
{
    /**
     * @var Transaction[] Массив транзакций.
     */
    private array $transactions = [];

    /**
     * Добавляет транзакцию в репозиторий.
     *
     * @param Transaction $transaction Объект транзакции.
     * @return void
     */
    public function addTransaction(Transaction $transaction): void
    {
        $this->transactions[] = $transaction;
    }

    /**
     * Удаляет транзакцию по идентификатору.
     *
     * @param int $id Идентификатор транзакции.
     * @return void
     */
    public function removeTransactionById(int $id): void
    {
        $this->transactions = array_values(array_filter(
            $this->transactions,
            fn(Transaction $transaction): bool => $transaction->getId() !== $id
        ));
    }

    /**
     * Возвращает все транзакции.
     *
     * @return Transaction[]
     */
    public function getAllTransactions(): array
    {
        return $this->transactions;
    }

    /**
     * Ищет транзакцию по идентификатору.
     *
     * @param int $id Идентификатор транзакции.
     * @return Transaction|null
     */
    public function findById(int $id): ?Transaction
    {
        foreach ($this->transactions as $transaction) {
            if ($transaction->getId() === $id) {
                return $transaction;
            }
        }

        return null;
    }
}

/**
 * Класс для бизнес-логики работы с транзакциями.
 */
class TransactionManager
{
    /**
     * @param TransactionStorageInterface $repository Хранилище транзакций.
     */
    public function __construct(
        private TransactionStorageInterface $repository
    ) {
    }

    /**
     * Вычисляет общую сумму всех транзакций.
     *
     * @return float
     */
    public function calculateTotalAmount(): float
    {
        $total = 0.0;

        foreach ($this->repository->getAllTransactions() as $transaction) {
            $total += $transaction->getAmount();
        }

        return $total;
    }

    /**
     * Вычисляет сумму транзакций за указанный период.
     *
     * @param string $startDate Начальная дата в формате Y-m-d.
     * @param string $endDate Конечная дата в формате Y-m-d.
     * @return float
     */
    public function calculateTotalAmountByDateRange(string $startDate, string $endDate): float
    {
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);
        $total = 0.0;

        foreach ($this->repository->getAllTransactions() as $transaction) {
            $transactionDate = new DateTime($transaction->getDate());

            if ($transactionDate >= $start && $transactionDate <= $end) {
                $total += $transaction->getAmount();
            }
        }

        return $total;
    }

    /**
     * Подсчитывает количество транзакций у указанного получателя.
     *
     * @param string $merchant Название получателя.
     * @return int
     */
    public function countTransactionsByMerchant(string $merchant): int
    {
        $count = 0;

        foreach ($this->repository->getAllTransactions() as $transaction) {
            if (mb_strtolower($transaction->getMerchant()) === mb_strtolower($merchant)) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Сортирует транзакции по дате по возрастанию.
     *
     * @return Transaction[]
     */
    public function sortTransactionsByDate(): array
    {
        $transactions = $this->repository->getAllTransactions();

        usort($transactions, function (Transaction $a, Transaction $b): int {
            return strtotime($a->getDate()) <=> strtotime($b->getDate());
        });

        return $transactions;
    }

    /**
     * Сортирует транзакции по сумме по убыванию.
     *
     * @return Transaction[]
     */
    public function sortTransactionsByAmountDesc(): array
    {
        $transactions = $this->repository->getAllTransactions();

        usort($transactions, function (Transaction $a, Transaction $b): int {
            return $b->getAmount() <=> $a->getAmount();
        });

        return $transactions;
    }
}

/**
 * Класс для отображения транзакций в HTML-таблице.
 */
final class TransactionTableRenderer
{
    /**
     * Формирует HTML-таблицу из массива транзакций.
     *
     * @param Transaction[] $transactions Массив транзакций.
     * @return string HTML-код таблицы.
     */
    public function render(array $transactions): string
    {
        $html = '<table border="1" cellpadding="8" cellspacing="0">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th>ID транзакции</th>';
        $html .= '<th>Дата</th>';
        $html .= '<th>Сумма</th>';
        $html .= '<th>Описание</th>';
        $html .= '<th>Название получателя</th>';
        $html .= '<th>Категория получателя</th>';
        $html .= '<th>Количество дней с момента транзакции</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';

        foreach ($transactions as $transaction) {
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars((string)$transaction->getId()) . '</td>';
            $html .= '<td>' . htmlspecialchars($transaction->getDate()) . '</td>';
            $html .= '<td>' . htmlspecialchars(number_format($transaction->getAmount(), 2, '.', '')) . '</td>';
            $html .= '<td>' . htmlspecialchars($transaction->getDescription()) . '</td>';
            $html .= '<td>' . htmlspecialchars($transaction->getMerchant()) . '</td>';
            $html .= '<td>' . htmlspecialchars($transaction->getCategory()) . '</td>';
            $html .= '<td>' . htmlspecialchars((string)$transaction->getDaysSinceTransaction()) . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody>';
        $html .= '</table>';

        return $html;
    }
}

/*
|--------------------------------------------------------------------------
| Начальные данные
|--------------------------------------------------------------------------
*/

$repository = new TransactionRepository();

$transactions = [
    new Transaction(1, '2025-11-01', 783.75, 'Покупка продуктов', 'Linella', 'Супермаркет'),
    new Transaction(2, '2025-11-03', 6000.00, 'Оплата аренды', '999', 'Жильё'),
    new Transaction(3, '2025-11-05', 56.99, 'Подписка на музыку', 'Spotify', 'Развлечения'),
    new Transaction(4, '2025-11-08', 1598.50, 'Покупка одежды', 'LCWaikiki', 'Одежда'),
    new Transaction(5, '2025-11-10', 327.00, 'Обед', 'LaPlacinte', 'Кафе'),
    new Transaction(6, '2025-11-12', 400.00, 'Заправка автомобиля', 'Lukoil', 'Транспорт'),
    new Transaction(7, '2025-11-15', 342.25, 'Аптека', 'Felicia', 'Здоровье'),
    new Transaction(8, '2025-11-17', 19999.99, 'Покупка техники', 'Darwin', 'Электроника'),
    new Transaction(9, '2025-11-20', 90.00, 'Кофе и десерт', 'Naringi', 'Кафе'),
    new Transaction(10, '2025-11-22', 655.00, 'Оплата курсов', 'SkillAcademy', 'Образование'),
];

foreach ($transactions as $transaction) {
    $repository->addTransaction($transaction);
}

/*
|--------------------------------------------------------------------------
| Работа с менеджером
|--------------------------------------------------------------------------
*/

$manager = new TransactionManager($repository);
$renderer = new TransactionTableRenderer();

$totalAmount = $manager->calculateTotalAmount();
$totalInRange = $manager->calculateTotalAmountByDateRange('2025-11-01', '2025-11-15');
$merchantCount = $manager->countTransactionsByMerchant('LaPlacinte');
$sortedByDate = $manager->sortTransactionsByDate();
$sortedByAmount = $manager->sortTransactionsByAmountDesc();

// Пример удаления транзакции:
// $repository->removeTransactionById(3);

// Пример поиска транзакции:
// $foundTransaction = $repository->findById(5);

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Управление банковскими транзакциями</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        h1, h2 {
            margin-bottom: 10px;
        }

        .info-block {
            margin-bottom: 20px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 30px;
        }

        th, td {
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

<h1>Система управления банковскими транзакциями</h1>

<div class="info-block">
    <p><strong>Общая сумма всех транзакций:</strong> <?= htmlspecialchars(number_format($totalAmount, 2, '.', '')) ?></p>
    <p><strong>Сумма транзакций за период с 2025-11-01 по 2025-11-15:</strong> <?= htmlspecialchars(number_format($totalInRange, 2, '.', '')) ?></p>
    <p><strong>Количество транзакций у получателя LaPlacinte:</strong> <?= htmlspecialchars((string)$merchantCount) ?></p>
</div>

<h2>Все транзакции</h2>
<?= $renderer->render($repository->getAllTransactions()) ?>

<h2>Транзакции, отсортированные по дате</h2>
<?= $renderer->render($sortedByDate) ?>

<h2>Транзакции, отсортированные по сумме по убыванию</h2>
<?= $renderer->render($sortedByAmount) ?>

</body>
</html>
```

### Результат

<img width="1828" height="553" alt="image" src="https://github.com/user-attachments/assets/ff2f0ec1-3158-405d-a3ad-775239f16832" />

<img width="1838" height="966" alt="image" src="https://github.com/user-attachments/assets/37dd1b7f-c68c-473e-9ae5-904985789e8a" />

### Контрольные вопросы:

1.Зачем нужна строгая типизация в PHP и как она помогает при разработке?

Она нужна для того, чтобы PHP строже проверял типы данных, которые передаются в функции, методы и возвращаются из них.

2.Что такое класс в объектно-ориентированном программировании и какие основные компоненты класса вы знаете?

Класс — это шаблон, по которому создаются объекты.

Основные компоненты:

- свойства (переменные) → $id, $amount

- методы (функции) → getAmount()

- конструктор → __construct()

- модификаторы доступа → private, public

3.Объясните, что такое полиморфизм и как он может быть реализован в PHP.

Полиморфизм — это возможность работать с разными объектами через один общий интерфейс, при этом каждый объект будет вести себя по-своему.У нескольких классов может быть один и тот же метод, но реализация у каждого будет своя.

4.Что такое интерфейс в PHP и как он отличается от абстрактного класса?

Интерфейс — это список требований к классу.Если ты хочешь быть этим типом — ты обязан иметь такие методы.

| Критерий | Интерфейс | Абстрактный класс |
|----------|----------|-------------------|
| Что это | Набор требований (контракт) | Базовый класс (шаблон) |
| Готовый код |  Нет | Может быть |
| Методы | Только объявление (без реализации) | Могут быть и с реализацией, и без |
| Свойства | Нет (обычных) |  Есть |
| Наследование | Можно реализовать несколько интерфейсов | Можно наследовать только один класс |
| Назначение | Описывает, что должен уметь класс | Даёт общую логику и основу |
| Когда использовать | Когда важна гибкость и единый контракт | Когда есть общая логика для нескольких классов |

5.Какие преимущества дает использование интерфейсов при проектировании архитектуры приложения? Объясните на примере данной лабораторной работы.

Использование интерфейсов позволяет отделить логику работы программы от конкретной реализации. Это делает код более гибким, расширяемым и слабо связанным.
В данной лабораторной работе интерфейс для работы с транзакциями позволяет использовать разные способы хранения данных (например, массив или файл) без изменения основной логики программы.
