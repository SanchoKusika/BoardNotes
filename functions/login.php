<?php
require_once '../config.php';
require_once '../db.php';
session_start();

header('Content-Type: application/json');

$login = trim($_POST['login'] ?? '');
$password = $_POST['password'] ?? '';
$errors = [];

if ($login === '' || $password === '') {
    $errors[] = 'Все поля обязательны для заполнения.';
}

$user = R::findOne('users', 'username = ?', [$login]);

if (!$user || !password_verify($password, $user->password_hash)) {
    $errors[] = 'Неверный логин или пароль.';
}

if ($errors) {
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

$_SESSION['user_id'] = $user->id;

echo json_encode(['success' => true]);

