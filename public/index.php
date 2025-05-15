<?php
// public/index.php

require_once __DIR__ . '/../src/auth.php';
require_login();
require_once __DIR__ . '/../src/config.php';

$pdo = getPDO();

// 1) Handle toggle-complete POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_id'])) {
    $toggleId  = (int)$_POST['toggle_id'];
    $newStatus = isset($_POST['is_complete']) ? 1 : 0;
    $stmt = $pdo->prepare("
        UPDATE tasks
        SET is_complete = :st,
            updated_at = CURRENT_TIMESTAMP
        WHERE id = :id
          AND user_id = :uid
    ");
    $stmt->execute([
        ':st'  => $newStatus,
        ':id'  => $toggleId,
        ':uid' => current_user_id(),
    ]);
    header('Location: index.php');
    exit;
}

// 2) Fetch this user’s tasks
$stmt = $pdo->prepare("
    SELECT id, title, is_complete, due_date, created_at
    FROM tasks
    WHERE user_id = :uid
    ORDER BY created_at DESC
");
$stmt->execute([':uid' => current_user_id()]);
$tasks = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My TODO App</title>
  <style>
    body { font-family: sans-serif; margin: 2rem; }
    .complete { text-decoration: line-through; color: gray; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 0.5rem; border-bottom: 1px solid #ddd; }
    form.inline { display: inline; }
    .header { display: flex; justify-content: space-between; align-items: center; }
  </style>
</head>
<body>
  <div class="header">
    <h1>My Tasks</h1>
    <div>
      Logged in as <?= htmlspecialchars(current_user_email()) ?> |
      <a href="logout.php">Logout</a>
    </div>
  </div>
  <p><a href="create.php">+ Add New Task</a></p>

  <?php if (empty($tasks)): ?>
    <p>No tasks yet. 🎉</p>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>Done?</th>
          <th>Title</th>
          <th>Due Date</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($tasks as $task): ?>
        <tr>
          <td>
            <form method="post" class="inline">
              <input type="hidden" name="toggle_id" value="<?= $task['id'] ?>">
              <input
                type="checkbox"
                name="is_complete"
                onchange="this.form.submit()"
                <?= $task['is_complete'] ? 'checked' : '' ?>
              >
            </form>
          </td>
          <td class="<?= $task['is_complete'] ? 'complete' : '' ?>">
            <?= htmlspecialchars($task['title']) ?>
          </td>
          <td>
            <?= $task['due_date'] ? date('Y-m-d', strtotime($task['due_date'])) : '—' ?>
          </td>
          <td>
            <a href="edit.php?id=<?= $task['id'] ?>">Edit</a> |
            <a href="delete.php?id=<?= $task['id'] ?>" onclick="return confirm('Delete this task?')">Delete</a>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</body>
</html>
