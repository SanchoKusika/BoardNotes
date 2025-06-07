<?php
session_start();

require("config.php");
require("db.php");

if (!isset($_SESSION['user_id'])) {
    header('Location: auth.php');
    exit();
}

$userId = $_SESSION['user_id'];

$user = R::findOne('users', 'id = ?', [$userId]);

if (!$user) {
    session_unset();
    session_destroy();
    header('Location: auth.php');
    exit();
}

$board = R::findOne('boards', 'user_id = ?', [$userId]);

if (!$board) {
    $board = R::dispense('boards');
    $board->user_id = $userId;
    $board->name = 'Личная доска';
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
					<button class="files__upload-btn" aria-label="Upload files">
						<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
						</svg>
						<span>Загрузить</span>
					</button>
					<input type="file" class="files__input" multiple accept="image/*,.pdf,.doc,.docx,.xls,.xlsx" hidden />
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
				<h1 class="board__main-title"><?php echo htmlspecialchars($board->name); ?></h1>
				<div class="board__header-actions">
					<button class="board__create-session-btn" aria-label="Create session">
						<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
						</svg>
						Создать сессию
					</button>
				</div>				
			</div>
			<div class="board__container">
				<!-- TO DO -->
				<div class="board__column board__column--todo">
					<div class="board__header">
						<h2 class="board__title">TO&#160;DO</h2>
						<button class="board__add-btn" aria-label="Add note">
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

		<div class="modal" id="createSessionModal">
			<div class="modal__content">
				<div class="modal__header">
					<h3 class="modal__title">Создать новую сессию</h3>
					<button class="modal__close-btn" aria-label="Close modal">
						<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
						</svg>
					</button>
				</div>
				<div class="modal__body">
					<div class="modal__form-group">
						<label for="sessionName" class="modal__label">Название сессии</label>
						<input type="text" id="sessionName" class="modal__input" placeholder="Введите название сессии" />
					</div>
					<div class="modal__form-group">
						<label class="modal__label">Участники</label>
						<div class="modal__search">
							<input type="text" class="modal__input" placeholder="Поиск пользователей..." />
							<button class="modal__search-btn" aria-label="Search">
								<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path
										d="M21 21L15 15M17 10C17 13.866 13.866 17 10 17C6.13401 17 3 13.866 3 10C3 6.13401 6.13401 3 10 3C13.866 3 17 6.13401 17 10Z"
										stroke="currentColor"
										stroke-width="2"
										stroke-linecap="round"
										stroke-linejoin="round"
									/>
								</svg>
							</button>
						</div>
						<div class="modal__users-list">
							<!-- Здесь будут отображаться найденные пользователи -->
						</div>
						<div class="modal__selected-users">
							<h4 class="modal__subtitle">Выбранные участники</h4>
							<div class="modal__selected-list">
								<!-- Здесь будут отображаться выбранные пользователи -->
							</div>
						</div>
					</div>
				</div>
				<div class="modal__footer">
					<button class="modal__cancel-btn">Отмена</button>
					<button class="modal__create-btn">Создать сессию</button>
				</div>
			</div>
		</div>
	</section>
</main>

<?php
include(ROOT . "templates/footer.tpl");
?>