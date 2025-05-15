<?php
// public/edit.php

require_once __DIR__ . '/../src/auth.php';
require_login();
require_once __DIR__ . '/../src/config.php';

$pdo = getPDO();
$error = '';

// 1) Get and validate task ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: index.php');
    exit;
}

// 2) Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $due_date    = trim($_POST['due_date'] ?? '');

    if ($title === '') {
        $error = 'Title is required.';
    } else {
        $stmt = $pdo->prepare("
            UPDATE tasks
            SET title = :title,
                description = :description,
                due_date = :due_date,
                updated_at = CURRENT_TIMESTAMP
            WHERE id = :id
              AND user_id = :uid
        ");
        $stmt->execute([
            ':title'       => $title,
            ':description' => $description,
            ':due_date'    => $due_date ?: null,
            ':id'          => $id,
            ':uid'         => current_user_id(),
        ]);
        header('Location: index.php');
        exit;
    }
}

// 3) On GET (or error), fetch existing task
$stmt = $pdo->prepare("
    SELECT * FROM tasks
    WHERE id = :id
      AND user_id = :uid
");
$stmt->execute([
    ':id'  => $id,
    ':uid' => current_user_id(),
]);
$task = $stmt->fetch();

if (!$task) {
    // No such task or not yours
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Task</title>
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
    <h1>Edit Task</h1>
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
      <input type="text" name="title" value="<?= htmlspecialchars($task['title']) ?>" required>
    </label>

    <label>
      Description
      <textarea name="description" rows="4"><?= htmlspecialchars($task['description']) ?></textarea>
    </label>

    <label>
      Due Date
      <input type="date" name="due_date" value="<?= htmlspecialchars($task['due_date']) ?>">
    </label>

    <button type="submit" style="margin-top:1rem; padding:0.5rem 1rem;">Save Changes</button>
  </form>
</body>
</html>
