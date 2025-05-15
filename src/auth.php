<?php
// src/auth.php
session_start();

require_once __DIR__ . '/config.php';

/**
 * Attempt login; returns true on success.
 */
function login(string $email, string $password): bool {
    $pdo = getPDO();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        return true;
    }
    return false;
}

/**
 * Log out current user.
 */
function logout(): void {
    session_unset();
    session_destroy();
}

/**
 * Redirect to login if not authenticated.
 */
function require_login(): void {
    if (empty($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Get current user ID.
 */
function current_user_id(): ?int {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current user email.
 */
function current_user_email(): ?string {
    return $_SESSION['user_email'] ?? null;
}
