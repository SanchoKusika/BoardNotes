<?php
require_once '../config.php';
require_once '../db.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'errors' => ['Вы не авторизованы.']]);
    exit;
}

if (!isset($_FILES['file'])) {
    echo json_encode(['success' => false, 'errors' => ['Файл не был загружен.']]);
    exit;
}

$file = $_FILES['file'];
$user_id = $_SESSION['user_id'];
$board_id = $_POST['board_id'] ?? null;

if ($file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'errors' => ['Ошибка при загрузке файла.']]);
    exit;
}

if ($file['size'] > 10 * 1024 * 1024) {
    echo json_encode(['success' => false, 'errors' => ['Файл слишком большой. Максимальный размер: 10MB']]);
    exit;
}

$allowed_types = [
    'image/jpeg',
    'image/png',
    'image/gif',
    'application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'application/vnd.ms-excel',
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'application/zip',
    'application/x-rar-compressed',
    'application/x-7z-compressed'
];

if (!in_array($file['type'], $allowed_types)) {
    echo json_encode(['success' => false, 'errors' => ['Тип файла не поддерживается.']]);
    exit;
}

$upload_dir = '../uploads/files/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$new_filename = uniqid() . '.' . $file_extension;
$filepath = $upload_dir . $new_filename;

if (!move_uploaded_file($file['tmp_name'], $filepath)) {
    echo json_encode(['success' => false, 'errors' => ['Ошибка при сохранении файла.']]);
    exit;
}

$file_record = R::dispense('files');
$file_record->board_id = $board_id;
$file_record->user_id = $user_id;
$file_record->filename = $file['name'];
$file_record->filepath = 'uploads/files/' . $new_filename;
$file_record->mimetype = $file['type'];
$file_record->size = $file['size'];
$file_record->uploaded_at = date('Y-m-d H:i:s');

try {
    R::store($file_record);
    echo json_encode([
        'success' => true,
        'file' => [
            'id' => $file_record->id,
            'name' => $file_record->filename,
            'path' => $file_record->filepath,
            'type' => $file_record->mimetype,
            'size' => $file_record->size
        ]
    ]);
} catch (Exception $e) {
    // Удаляем файл, если не удалось сохранить в БД
    unlink($filepath);
    echo json_encode(['success' => false, 'errors' => ['Ошибка при сохранении информации о файле.', $e->getMessage()]]);} 