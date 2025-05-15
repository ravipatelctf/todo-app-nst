<?php
// src/db_init.php

// Path to SQLite file
$dbFile = __DIR__ . '/../data/todo.sqlite';
if (!is_dir(dirname($dbFile))) {
    mkdir(dirname($dbFile), 0755, true);
}

$pdo = new PDO('sqlite:' . $dbFile);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// 1. Create users table
$pdo->exec("
CREATE TABLE IF NOT EXISTS users (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  email TEXT NOT NULL UNIQUE,
  password TEXT NOT NULL,
  created_at TEXT DEFAULT CURRENT_TIMESTAMP
);
");

// 2. Create tasks table (with user_id)
$pdo->exec("
CREATE TABLE IF NOT EXISTS tasks (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  user_id INTEGER NOT NULL,
  title TEXT NOT NULL,
  description TEXT,
  is_complete INTEGER NOT NULL DEFAULT 0,
  due_date TEXT,
  created_at TEXT DEFAULT CURRENT_TIMESTAMP,
  updated_at TEXT DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
);
");

// 3. Insert test user if missing
$email = 'testuser1@example.com';
$passwordPlain = '12345678';
$exists = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
$exists->execute([':email' => $email]);
if ($exists->fetchColumn() == 0) {
    $hash = password_hash($passwordPlain, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (email, password) VALUES (:email, :pwd)");
    $stmt->execute([':email' => $email, ':pwd' => $hash]);
    echo "Seeded test user: {$email} / {$passwordPlain}\n";
} else {
    echo "Test user already exists.\n";
}

echo "Database initialized at {$dbFile}\n";
