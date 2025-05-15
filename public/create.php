<?php
// public/create.php

require_once __DIR__ . '/../src/auth.php';
require_login();
require_once __DIR__ . '/../src/config.php';

$pdo = getPDO();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $due_date    = trim($_POST['due_date'] ?? '');

    if ($title === '') {
        $error = 'Title is required.';
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO tasks (user_id, title, description, due_date)
            VALUES (:uid, :title, :description, :due_date)
        ");
        $stmt->execute([
            ':uid'         => current_user_id(),
            ':title'       => $title,
            ':description' => $description,
            ':due_date'    => $due_date ?: null,
        ]);
        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add New Task</title>
  <style>
    body { font-family: sans-serif; margin: 2rem; }
    form { max-width: 400px; }
    label { display: block; margin-top: 1rem; }
    input, textarea { width: 100%; padding: 0.5rem; margin-top: 0.25rem; }
    .error { color: red; }
    .header { display: flex; justify-content: space-between; align-items: center; }
  </style>
</head>
<body>
  <div class="header">
    <h1>Add New Task</h1>
    <div>
      Logged in as <?= htmlspecialchars(current_user_email()) ?> |
      <a href="logout.php">Logout</a>
    </div>
  </div>
  <p><a href="index.php">&larr; Back to tasks</a></p>

  <?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <form method="post">
    <label>
      Title *
      <input type="text" name="title" value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" required>
    </label>

    <label>
      Description
      <textarea name="description" rows="4"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
    </label>

    <label>
      Due Date
      <input type="date" name="due_date" value="<?= htmlspecialchars($_POST['due_date'] ?? '') ?>">
    </label>

    <button type="submit" style="margin-top:1rem; padding:0.5rem 1rem;">Create Task</button>
  </form>
</body>
</html>
