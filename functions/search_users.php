<?php
session_start();
require_once("../config.php");
require_once("../db.php");

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'errors' => ['Не авторизован']]);
    exit();
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

try {
    if (empty($search)) {
        $users = R::find('users', 'id != ?', [$_SESSION['user_id']]);
    } else {
        $users = R::find('users', 'id != ? AND (username LIKE ? OR email LIKE ?)', 
            [$_SESSION['user_id'], "%$search%", "%$search%"]);
    }
    $result = [];
    foreach ($users as $user) {
        $result[] = [
            'id' => $user->id,
            'name' => $user->username,
            'email' => $user->email,
            'avatar' => $user->avatar ? $user->avatar : null
        ];
    }
    echo json_encode(['success' => true, 'users' => $result]);
    exit();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'errors' => ['Ошибка при поиске пользователей']]);
} 