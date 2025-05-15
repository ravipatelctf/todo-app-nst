<?php
// public/register.php

require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/config.php';

$pdo   = getPDO();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm  = trim($_POST['confirm']  ?? '');

    // Basic validations
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        if ($stmt->fetchColumn() > 0) {
            $error = 'That email is already registered.';
        } else {
            // Insert new user
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("
                INSERT INTO users (email, password)
                VALUES (:email, :pwd)
            ");
            $stmt->execute([
                ':email' => $email,
                ':pwd'   => $hash,
            ]);
            // Auto‑login new user
            login($email, $password);
            header('Location: index.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register</title>
  <style>
    body { font-family: sans-serif; margin: 2rem; }
    form { max-width: 400px; }
    label { display: block; margin-top: 1rem; }
    input { width: 100%; padding: 0.5rem; margin-top: 0.25rem; }
    .error { color: red; }
  </style>
</head>
<body>
  <h1>Create an Account</h1>

  <?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <form method="post">
    <label>
      Email *
      <input type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
    </label>

    <label>
      Password *<br>
      <small>(min 8 characters)</small>
      <input type="password" name="password" required>
    </label>

    <label>
      Confirm Password *
      <input type="password" name="confirm" required>
    </label>

    <button type="submit" style="margin-top:1rem; padding:0.5rem 1rem;">Sign Up</button>
  </form>

  <p style="margin-top:1rem;">
    Already have an account? <a href="login.php">Log in here</a>.
  </p>
</body>
</html>
