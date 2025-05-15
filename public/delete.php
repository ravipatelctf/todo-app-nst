<?php
// public/delete.php

require_once __DIR__ . '/../src/auth.php';
require_login();
require_once __DIR__ . '/../src/config.php';

$pdo = getPDO();

// Get and validate ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id > 0) {
    $stmt = $pdo->prepare("
        DELETE FROM tasks
        WHERE id = :id
          AND user_id = :uid
    ");
    $stmt->execute([
        ':id'  => $id,
        ':uid' => current_user_id(),
    ]);
}

header('Location: index.php');
exit;
