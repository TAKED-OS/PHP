<h1 align="center">Лабораторная работа №2</h1>

# Цель работы: 

Целью данной лабораторной работы является установка и настройка среды разработки для работы с языком программирования PHP, а также создание первой программы на PHP.

# Условие

## Шаг 1: Установка PHP

1.Перейдите на сайт: https://www.apachefriends.org.

2.Скачайте и установите XAMPP, выбрав компоненты:

    Apache
    PHP
    phpMyAdmin

<img width="1839" height="909" alt="image" src="https://github.com/user-attachments/assets/49d12260-53b8-48c4-95ed-2f3594eb8cd1" />

3.Запустите XAMPP Control Panel и включите Apache.

<img width="828" height="540" alt="image" src="https://github.com/user-attachments/assets/e00fa368-4e60-404c-9294-df71213d1a4e" />

4.Проверьте работу сервера, открыв http://localhost в браузере.

<img width="811" height="512" alt="image" src="https://github.com/user-attachments/assets/0fa371ac-5707-4021-974d-07e40cb090b5" />

## Шаг 2. Написание первой PHP-программы

1.Создайте директорию для проекта, например: D:\Projects\PHP\01_Introduction.

2.Создайте файл index.php и откройте его в текстовом редакторе.

<img width="119" height="42" alt="image" src="https://github.com/user-attachments/assets/ace0b096-b2d3-447b-9931-e5cd51b48a60" />

3.Вставьте следующий код:

```php
<?php

echo "Привет, мир!";
```

<img width="426" height="189" alt="image" src="https://github.com/user-attachments/assets/288179fa-dea9-41dd-8abd-6f22dc1f21ab" />

4.Запустите программу с помощью встроенного веб-сервера PHP или с помощью XAMPP.

<img width="342" height="90" alt="image" src="https://github.com/user-attachments/assets/004ec458-ba17-453f-b4cd-126c8a3575b2" />

## Шаг 3. Вывод данных в PHP

1.Выведите строку "Hello, World!" используя функцию echo и print.

echo "Hello, World with echo!";

print "Hello, World with print!";

```php
<?php

echo "Hello, World with echo!<br>";
print "Hello, World with print!";
```

<img width="357" height="105" alt="image" src="https://github.com/user-attachments/assets/d37770ff-6e40-4eb6-aaf2-886005edb90e" />

## Шаг 4. Работа с переменными и выводом

1.Создайте две переменные:

- Целочисленную переменную $days со значением 288.

- Строковую переменную $message с текстом: Все возвращаются на работу!.

- Выведите значения переменных на экран несколькими способами:

- С использованием конкатенации. Конкатенация - это объединение строк, в PHP используется оператор .:

- С использованием двойных кавычек.

- Используйте переход на новую строку в выводе используя тэг `<br />`.

```php
<?php

$days = 288;
$message = "Все возвращаются на работу!";

echo "1. Через конкатенацию:<br />";
echo "Прошло " . $days . " дней. " . $message . "<br /><br />";

echo "2. Через двойные кавычки:<br />";
echo "Прошло $days дней. $message<br />";
```

<img width="455" height="176" alt="image" src="https://github.com/user-attachments/assets/d17db5e1-493f-40c2-9d42-f31a69b34c9b" />

## Контрольные вопросы

### Какие способы установки PHP существуют?

Способ 1 — Через готовый пакет.Идеально подходит для новичков.
- XAMPP
- OpenServer
- MAMP
- WAMP
Они устанавливают сразу:
- PHP
- Apache
- MySQL

Ты просто запускаешь сервер — и всё работает.

Способ 2 — Установить только PHP

Можно установить отдельно:

- Через официальный сайт php.net
- Через пакетный менеджер (если Linux):

### Как проверить, что PHP установлен и работает?

1.Проверка через терминал

Напиши:
```
php -v
```
Если появится версия — значит PHP установлен.

2.Проверка через браузер

Создай файл test.php:
```
<?php
phpinfo();
```
Открой в браузере:
```
http://localhost/test.php
```
Если появится большая страница с настройками PHP — всё работает.

### Чем отличается оператор echo от print?

1.echo

- Быстрее
- Может выводить несколько строк
- Не возвращает значение
```
echo "Hello", " World";
```

2.print

- Медленнее (чуть-чуть)
- Выводит только одну строку
- Возвращает значение 1 (можно использовать в выражениях)
```
print "Hello World";
```
