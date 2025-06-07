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
$board_id = isset($_GET['board_id']) ? (int)$_GET['board_id'] : null;

if (!$board_id) {
    echo json_encode(['success' => false, 'errors' => ['Не указан board_id.']]);
    exit;
}

try {
    $board = R::load('boards', $board_id);
    if (!$board->id) {
        echo json_encode(['success' => false, 'errors' => ['Доска не найдена.']]);
        exit;
    }
    if ($board->session_id) {
        $isParticipant = R::findOne('sessionuser', 'session_id = ? AND user_id = ?', [$board->session_id, $user_id]);
        $isOwner = ($board->user_id == $user_id);
        if (!$isParticipant && !$isOwner) {
            echo json_encode(['success' => false, 'errors' => ['Нет доступа к сессионной доске.']]);
            exit;
        }
    } else {
        if ($board->user_id != $user_id) {
            echo json_encode(['success' => false, 'errors' => ['Нет доступа к личной доске.']]);
            exit;
        }
    }

    $query = 'SELECT f.*, u.username as uploader_name 
              FROM files f 
              LEFT JOIN users u ON f.user_id = u.id 
              WHERE f.board_id = ?
              ORDER BY f.uploaded_at DESC';
    $params = [$board_id];

    $files = R::getAll($query, $params);

    // данные для фронтенда
    $formatted_files = array_map(function($file) {
        return [
            'id' => $file['id'],
            'name' => $file['filename'],
            'path' => $file['filepath'],
            'type' => $file['mimetype'],
            'size' => $file['size'],
            'uploaded_at' => $file['uploaded_at'],
            'uploader' => $file['uploader_name']
        ];
    }, $files);

    echo json_encode([
        'success' => true,
        'files' => $formatted_files,
        'is_session' => (bool)$board->session_id
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'errors' => ['Ошибка при получении списка файлов.']]);
}
