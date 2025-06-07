<?php
session_start();

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated.']);
    exit();
}

$userId = $_SESSION['user_id'];

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['note_id'])) {
    echo json_encode(['success' => false, 'message' => 'Note ID is required.']);
    exit();
}

$noteId = $input['note_id'];

$note = R::load('notes', $noteId);

if (!$note->id) {
    echo json_encode(['success' => false, 'message' => 'Note not found.']);
    exit();
}

$board = R::load('boards', $note->board_id);
if (!$board->id) {
    echo json_encode(['success' => false, 'message' => 'Board not found.']);
    exit();
}

if ($board->session_id) {
    $isParticipant = R::findOne('sessionuser', 'session_id = ? AND user_id = ?', [$board->session_id, $userId]);
    $isOwner = ($board->user_id == $userId);
    if (!$isParticipant && !$isOwner) {
        echo json_encode(['success' => false, 'message' => 'No permission to delete note in session board.']);
        exit();
    }
} else {
    if ($board->user_id != $userId) {
        echo json_encode(['success' => false, 'message' => 'No permission to delete note in personal board.']);
        exit();
    }
}

try {
    R::trash($note);
    echo json_encode(['success' => true, 'message' => 'Note deleted successfully.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error deleting note: ' . $e->getMessage()]);
}

?>
