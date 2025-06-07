<?php
require_once '../config.php';
require_once '../db.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'errors' => ['Вы не авторизованы.']]);
    exit;
}

$user_id = $_SESSION['user_id'];
$user = R::load('users', $user_id);

if (!$user->id) {
    echo json_encode(['success' => false, 'errors' => ['Пользователь не найден.']]);
    exit;
}

$data = [
    'id' => $user->id,
    'username' => $user->username,
    'email' => $user->email,
    'avatar' => $user->avatar ?? null
];

echo json_encode(['success' => true, 'user' => $data]);
