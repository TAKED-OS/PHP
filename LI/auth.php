<?php

/**
 * Запускает сессию, если она ещё не была запущена.
 *
 * @return void
 */
function startSessionIfNeeded(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * Проверяет, вошёл ли пользователь в систему.
 *
 * @return bool
 */
function isLoggedIn(): bool
{
    startSessionIfNeeded();

    return isset($_SESSION["user_id"]);
}

/**
 * Проверяет, является ли текущий пользователь администратором.
 *
 * @return bool
 */
function isAdmin(): bool
{
    startSessionIfNeeded();

    return isset($_SESSION["role"]) && $_SESSION["role"] === "admin";
}

/**
 * Закрывает страницу от неавторизованных пользователей.
 * Если пользователь не вошёл в систему, перенаправляет его на страницу входа.
 *
 * @return void
 */
function requireLogin(): void
{
    startSessionIfNeeded();

    if (!isLoggedIn()) {
        header("Location: login.php");
        exit;
    }
}

/**
 * Закрывает страницу от пользователей без роли администратора.
 * Если пользователь не вошёл — отправляет на login.php.
 * Если пользователь вошёл, но не является администратором — отправляет на access_denied.php.
 *
 * @return void
 */
function requireAdmin(): void
{
    startSessionIfNeeded();

    if (!isLoggedIn()) {
        header("Location: login.php");
        exit;
    }

    if (!isAdmin()) {
        header("Location: access_denied.php");
        exit;
    }
}

/**
 * Возвращает имя текущего пользователя.
 *
 * @return string
 */
function getCurrentUsername(): string
{
    startSessionIfNeeded();

    return $_SESSION["username"] ?? "Гость";
}

/**
 * Возвращает роль текущего пользователя.
 *
 * @return string
 */
function getCurrentUserRole(): string
{
    startSessionIfNeeded();

    return $_SESSION["role"] ?? "guest";
}