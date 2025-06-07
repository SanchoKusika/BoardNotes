<?php
session_start();
require_once("../config.php");
require_once("../db.php");

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'errors' => ['Не авторизован']]);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['name']) || empty($data['name'])) {
    echo json_encode(['success' => false, 'errors' => ['Название сессии обязательно']]);
    exit();
}

if (!isset($data['users']) || !is_array($data['users']) || empty($data['users'])) {
    echo json_encode(['success' => false, 'errors' => ['Выберите хотя бы одного участника']]);
    exit();
}

try {
    R::begin();
    
    $session = R::dispense('sessions');
    $session->name = $data['name'];
    $session->owner_id = $_SESSION['user_id'];
    $session->status = 'active';
    $sessionId = R::store($session);
    
    foreach ($data['users'] as $userId) {
        $sessionUser = R::dispense('sessionuser');
        $sessionUser->session_id = $sessionId;
        $sessionUser->user_id = $userId;
        R::store($sessionUser);
    }
    
    $board = R::dispense('boards');
    $board->user_id = $_SESSION['user_id'];
    $board->name = $data['name'];
    $board->session_id = $sessionId;
    R::store($board);
    
    R::commit();
    
    echo json_encode([
        'success' => true, 
        'session_id' => $sessionId,
        'message' => 'Сессия успешно создана'
    ]);
} catch (Exception $e) {
    R::rollback();
    echo json_encode(['success' => false, 'errors' => [$e->getMessage()]]);
}
