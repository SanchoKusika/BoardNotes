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
$login = trim($_POST['login'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$avatar = $_FILES['avatar'] ?? null;
$errors = [];

$isAvatarOnly = $avatar && !$login && !$email && !$password;

if (!$isAvatarOnly) {
    if ($login === '' || $email === '') {
        $errors[] = 'Логин и email обязательны.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Некорректный email.';
    }
    $exists = R::findOne('users', '(email = ? OR username = ?) AND id != ?', [$email, $login, $user_id]);
    if ($exists) {
        $errors[] = 'Пользователь с таким email или логином уже существует.';
    }
}

$user = R::load('users', $user_id);
if (!$user) {
    $errors[] = 'Пользователь не найден.';
}

$avatar_path = null;
if ($avatar && $avatar['error'] === UPLOAD_ERR_OK) {
    $ext = pathinfo($avatar['name'], PATHINFO_EXTENSION);
    $avatar_path = 'uploads/avatars/' . uniqid() . '.' . $ext;
    if (!is_dir('../uploads/avatars')) {
        mkdir('../uploads/avatars', 0777, true);
    }
    move_uploaded_file($avatar['tmp_name'], '../' . $avatar_path);
}

if (!$errors) {
    if (!$isAvatarOnly) {
        $user->username = $login;
        $user->email = $email;
        if ($password !== '') {
            $user->password_hash = password_hash($password, PASSWORD_DEFAULT);
        }
    }
    if ($avatar_path) {
        $user->avatar = $avatar_path;
    }
    $user->updated_at = date('Y-m-d H:i:s');
    R::store($user);
    $result = [
        'success' => true,
        'login' => $user->username,
        'email' => $user->email
    ];
    if ($user->avatar) {
        $result['avatar'] = '/' . ltrim($user->avatar, '/');
    }
    echo json_encode($result);
} else {
    echo json_encode(['success' => false, 'errors' => $errors]);
} 