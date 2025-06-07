<?php

session_start();
require("../config.php");
require("../db.php");

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'errors' => ['User not authorized.']]);
    exit();
}

$userId = $_SESSION['user_id'];

$boardId = isset($_GET['board_id']) ? (int)$_GET['board_id'] : null;

if (!$boardId) {
     echo json_encode(['success' => false, 'errors' => ['Board ID is required.']]);
     exit();
}

try {
    $board = R::load('boards', $boardId);

    if (!$board->id) {
        echo json_encode(['success' => false, 'errors' => ['Invalid board ID.']]);
        exit();
    }

    if ($board->session_id) {
        $isParticipant = R::findOne('sessionuser', 'session_id = ? AND user_id = ?', [$board->session_id, $userId]);
        $isOwner = ($board->user_id == $userId);
        if (!$isParticipant && !$isOwner) {
            echo json_encode(['success' => false, 'errors' => ['Insufficient permissions for session board.']]);
            exit();
        }
    } else {
        if ($board->user_id != $userId) {
            echo json_encode(['success' => false, 'errors' => ['Insufficient permissions for personal board.']]);
            exit();
        }
    }

    $notes = R::find('notes', 'board_id = ? ORDER BY id', [$boardId]);

    $notesArray = [];
    foreach ($notes as $note) {
        $notesArray[] = $note->export();
    }

    echo json_encode(['success' => true, 'notes' => $notesArray]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'errors' => ['Database error: ' . $e->getMessage()]]);
}

?>
