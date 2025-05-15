<?php
// public/login.php

require_once __DIR__ . '/../src/auth.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = trim($_POST['password'] ?? '');
    if (login($email, $pass)) {
        header('Location: index.php');
        exit;
    } else {
        $error = 'Invalid email or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <style>
    body { font-family: sans-serif; margin: 2rem; }
    .credentials { 
      background: #f0f8ff; 
      border: 1px solid #aac; 
      padding: 1rem; 
      margin-bottom: 1.5rem; 
      border-radius: 4px;
    }
    .error { color: red; }
  </style>
</head>
<body>
  <div class="credentials">
    <strong>Test User Credentials:</strong><br>
    Email: <code>testuser1@example.com</code><br>
    Password: <code>12345678</code>
  </div>

  <h1>Login</h1>

  <?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <form method="post">
    <label>
      Email<br>
      <input type="email" name="email" required>
    </label><br><br>

    <label>
      Password<br>
      <input type="password" name="password" required>
    </label><br><br>

    <button type="submit">Log In</button>
  </form>
    <p style="margin-top:1rem;">
        Don't have an account? <a href="register.php">Sign up here</a>.
    </p>
</body>
</html>
