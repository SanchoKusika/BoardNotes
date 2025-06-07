<?php
require_once '../config.php';
require_once '../db.php';

header('Content-Type: application/json');

$login = trim($_POST['login'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$errors = [];

if ($login === '' || $email === '' || $password === '') {
    $errors[] = 'Все поля обязательны для заполнения.';
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Некорректный email.';
}

if (strlen($password) < 6) {
    $errors[] = 'Пароль должен быть не менее 6 символов.';
}

$user = R::findOne('users', 'email = ? OR username = ?', [$email, $login]);
if ($user) {
    $errors[] = 'Пользователь с таким email или логином уже существует.';
}

if ($errors) {
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

$password_hash = password_hash($password, PASSWORD_DEFAULT);

$user = R::dispense('users');
$user->username = $login;
$user->email = $email;
$user->password_hash = $password_hash;
$user->created_at = date('Y-m-d H:i:s');
$user->updated_at = date('Y-m-d H:i:s');
$user->avatar = 'assets/img/profile/avatar-placeholder.png';

try {
    $userId = R::store($user);

    $board = R::dispense('boards');
    $board->user_id = $userId;
    $board->name = 'Личная доска';
    $board->created_at = date('Y-m-d H:i:s');
    $board->updated_at = date('Y-m-d H:i:s');
    R::store($board);

    echo json_encode(['success' => true, 'message' => 'Регистрация прошла успешно! Доска создана.']);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'errors' => ['Ошибка при регистрации или создании доски.']]);
}

?> 