<?php
session_start();
require_once '../config.php';
require_once '../db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'errors' => ['Необходима авторизация']]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$file_id = isset($data['file_id']) ? (int)$data['file_id'] : 0;

if (!$file_id) {
    echo json_encode(['success' => false, 'errors' => ['Неверный ID файла']]);
    exit;
}

try {
    $file = R::findOne('files', 'id = ?', [$file_id]);
    if (!$file) {
        echo json_encode(['success' => false, 'errors' => ['Файл не найден']]);
        exit;
    }

    $board = R::load('boards', $file->board_id);
    if (!$board->id) {
        echo json_encode(['success' => false, 'errors' => ['Доска не найдена']]);
        exit;
    }

    $userId = $_SESSION['user_id'];
    $isOwner = ($board->user_id == $userId);
    if ($board->session_id) {
        if (!$isOwner && $file->user_id != $userId) {
            echo json_encode(['success' => false, 'errors' => ['Можно удалять только свои файлы']]);
            exit;
        }
    } else {
        if ($file->user_id != $userId) {
            echo json_encode(['success' => false, 'errors' => ['Можно удалять только свои файлы']]);
            exit;
        }
    }

    $file_path = __DIR__ . '/../' . $file->filepath;
    if (file_exists($file_path)) {
        unlink($file_path);
    }

    R::trash($file);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'errors' => ['Ошибка при удалении файла', $e->getMessage()]]);
}
