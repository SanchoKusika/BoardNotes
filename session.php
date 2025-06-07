<?php
session_start();
require_once("config.php");
require_once("db.php");

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$sessionId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$sessionId) {
    echo "<h2>Сессия не найдена</h2>";
    exit();
}

// Проверяем, что пользователь участник сессии
$isParticipant = R::findOne('sessionUser', 'session_id = ? AND user_id = ?', [$sessionId, $userId]);
$session = R::findOne('sessions', 'id = ?', [$sessionId]);

if (!$session || (!$isParticipant && $session->owner_id != $userId)) {
	include(ROOT . "templates/head.tpl");
	include(ROOT . "templates/header.tpl");
	echo '<div style="height:100vh;display:flex;align-items:center;justify-content:center;font-size:2em;color:#888;">У вас нет доступа к этой сессии</div>';
	include(ROOT . "templates/footer.tpl");
	exit();
}

$isCompleted = ($session->status === 'completed');
if ($isCompleted) {
    include(ROOT . "templates/head.tpl");
    include(ROOT . "templates/header.tpl");
    echo '<main><div style="height:100vh;display:flex;align-items:center;justify-content:center;font-size:2em;color:#888;">Эта сессия завершена</div></main>';
    include(ROOT . "templates/footer.tpl");
    exit;
}

// Получаем участников
$participants = R::getAll('SELECT u.id, u.username, u.email, u.avatar FROM users u JOIN sessionUser su ON su.user_id = u.id WHERE su.session_id = ?', [$sessionId]);
// Добавляем владельца, если его нет в списке
$owner = R::findOne('users', 'id = ?', [$session->owner_id]);
$ownerInList = false;
foreach ($participants as $p) {
    if ($p['id'] == $session->owner_id) {
        $ownerInList = true;
        break;
    }
}
if ($owner && !$ownerInList) {
    array_unshift($participants, [
        'id' => $owner->id,
        'username' => $owner->username,
        'email' => $owner->email,
        'avatar' => $owner->avatar
    ]);
}

// Получаем доску для этой сессии
$board = R::findOne('boards', 'session_id = ?', [$sessionId]);
if (!$board) {
    // Если доска не найдена, создаём
    $board = R::dispense('boards');
    $board->user_id = $session->owner_id;
    $board->name = $session->name;
    $board->session_id = $sessionId;
    R::store($board);
}
$boardId = $board->id;

include(ROOT . "templates/head.tpl");
include(ROOT . "templates/header.tpl");
?>
<main>
    <section class="files">
        <div class="container">
            <div class="files__header">
                <h2 class="files__title">Файлы</h2>
                <div class="files__actions">
                    <button class="files__upload-btn" aria-label="Upload files"<?php if ($isCompleted) echo ' disabled style="opacity:0.5;pointer-events:none;"'; ?>>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                        <span>Загрузить</span>
                    </button>
                    <input type="file" class="files__input" multiple accept="image/*,.pdf,.doc,.docx,.xls,.xlsx" hidden <?php if ($isCompleted) echo 'disabled'; ?> />
                </div>
            </div>
            <div class="swiper files-swiper">
                <div class="swiper-wrapper"></div>
                <div class="swiper-pagination"></div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
            </div>
        </div>
    </section>
    <section class="board" data-board-id="<?php echo $boardId; ?>">
        <div class="container">
            <div class="board__header mb">
                <h1 class="board__main-title"><?php echo htmlspecialchars($session->name); ?></h1>
                <div class="board__header-actions">
                    <div class="board__participants">
                        <div class="participants-list">
                            <?php 
                            $displayCount = 0;
                            foreach ($participants as $p): 
                                if ($displayCount < 4):
                            ?>
                                <div class="participant-avatar<?php echo ($p['id'] == $session->owner_id) ? ' admin' : ''; ?>" title="<?php echo htmlspecialchars($p['username']); ?>">
                                    <img src="<?php echo htmlspecialchars($p['avatar'] ? $p['avatar'] : 'assets/img/profile/avatar-placeholder.png'); ?>" alt="<?php echo htmlspecialchars($p['username']); ?>" />
                                </div>
                            <?php 
                                $displayCount++;
                                endif;
                            endforeach; 
                            if (count($participants) > 4): 
                            ?>
                                <button class="participants-more" title="Показать всех участников">
                                    <?php echo (count($participants) - 4); ?>+
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if ($session->owner_id == $userId && !$isCompleted): ?>
                        <form id="deleteSessionForm" method="post" style="display:inline;">
                            <input type="hidden" name="delete_session" value="1">
                            <button type="submit" class="board__delete-session-btn" style="color:red;">Удалить сессию</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
            <div class="board__container">
                <!-- TO DO -->
                <div class="board__column board__column--todo">
                    <div class="board__header">
                        <h2 class="board__title">TO&#160;DO</h2>
                        <button class="board__add-btn" aria-label="Add note"<?php if ($isCompleted) echo ' disabled style="opacity:0.5;pointer-events:none;"'; ?>>
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            </svg>
                        </button>
                    </div>
                    <div class="board__notes"></div>
                </div>
                <!-- DOING -->
                <div class="board__column board__column--doing">
                    <h2 class="board__title">DOING</h2>
                    <div class="board__notes"></div>
                </div>
                <!-- DONE -->
                <div class="board__column board__column--done">
                    <h2 class="board__title">DONE</h2>
                    <div class="board__notes"></div>
                </div>
            </div>
        </div>
        <div class="modal" id="createNoteModal">
            <div class="modal__content">
                <div class="modal__header">
                    <h3 class="modal__title">Создать новую задачу</h3>
                    <button class="modal__close-btn" aria-label="Close modal">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                    </button>
                </div>
                <div class="modal__body">
                    <textarea class="modal__textarea" placeholder="Введите содержимое задачи"></textarea>
                </div>
                <div class="modal__footer">
                    <button class="modal__cancel-btn">Отмена</button>
                    <button class="modal__create-btn">Создать задачу</button>
                </div>
            </div>
        </div>
        <div class="modal" id="participantsModal">
            <div class="modal__content">
                <div class="modal__header">
                    <h3 class="modal__title">Участники сессии</h3>
                    <button class="modal__close-btn" aria-label="Close modal">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                    </button>
                </div>
                <div class="modal__body">
                    <div class="participants-modal-list">
                        <?php foreach ($participants as $p): ?>
                            <div class="participant-item">
                                <div class="participant-avatar<?php echo ($p['id'] == $session->owner_id) ? ' admin' : ''; ?>" title="<?php echo htmlspecialchars($p['username']); ?>">
                                    <img src="<?php echo htmlspecialchars($p['avatar'] ? $p['avatar'] : 'assets/img/profile/avatar-placeholder.png'); ?>" alt="<?php echo htmlspecialchars($p['username']); ?>" />
                                </div>
                                <div class="participant-info">
                                    <span class="participant-name">
                                        <?php echo htmlspecialchars($p['username']); ?>
                                        <?php if ($p['id'] == $userId): ?> <span style="color:#888;font-size:0.9em;">(Вы)</span><?php endif; ?>
                                    </span>
                                    <span class="participant-email">
                                        <?php echo htmlspecialchars($p['email']); ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<?php
// Удаление сессии (только для владельца)
if (
    $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_session']) && $session->owner_id == $userId
) {
    // Завершаем сессию (меняем статус)
    $session->status = 'completed';
    R::store($session);
    header('Location: index.php');
    exit();
}

include(ROOT . "templates/footer.tpl"); 