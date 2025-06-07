<?php

session_start();
require("../config.php");
require("../db.php");

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Пользователь не авторизован.']);
    exit();
}

$userId = $_SESSION['user_id'];

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['content']) || !isset($input['board_id'])) {
    echo json_encode(['success' => false, 'message' => 'Требуется содержимое заметки и board_id.']);
    exit();
}

$content = $input['content'];
$status = $input['status'] ?? 'todo';
$boardId = (int)$input['board_id'];

// Проверка прав доступа к доске (аналогично get_notes.php)
$board = R::load('boards', $boardId);
if (!$board->id) {
    echo json_encode(['success' => false, 'message' => 'Доска не найдена.']);
    exit();
}
if ($board->session_id) {
    $isParticipant = R::findOne('sessionuser', 'session_id = ? AND user_id = ?', [$board->session_id, $userId]);
    $isOwner = ($board->user_id == $userId);
    if (!$isParticipant && !$isOwner) {
        echo json_encode(['success' => false, 'message' => 'Нет прав для добавления заметки в сессионную доску.']);
        exit();
    }
} else {
    if ($board->user_id != $userId) {
        echo json_encode(['success' => false, 'message' => 'Нет прав для добавления заметки в личную доску.']);
        exit();
    }
}

$note = R::dispense('notes');
$note->board_id = $boardId;
$note->content = $content;
$note->note_column = $status;
$note->created_at = R::isoDateTime();
$note->updated_at = R::isoDateTime();
$note->marker = 'created';
$note->updated_by = $userId;

try {
    $id = R::store($note);
    echo json_encode(['success' => true, 'message' => 'Заметка успешно создана.', 'note_id' => $id]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Ошибка при создании заметки: ' . $e->getMessage()]);
}

?>
