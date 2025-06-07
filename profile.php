<?php
require("config.php");
require("db.php");
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: auth.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$user = R::load('users', $user_id);

// Получаем сессии, где пользователь — участник или владелец
$sessions = R::getAll('
    SELECT s.id, s.name, s.status, s.owner_id, s.created_at
    FROM sessions s
    LEFT JOIN sessionUser su ON su.session_id = s.id
    WHERE su.user_id = ? OR s.owner_id = ?
    GROUP BY s.id
    ORDER BY s.created_at DESC
', [$user_id, $user_id]);

include(ROOT . "templates/head.tpl");
include(ROOT . "templates/header.tpl");
?>

<main>
<section class="profile">
	<div class="container">
		<div class="profile__row">
			<div class="profile__content">
				<h1 class="profile__title">Профиль пользователя</h1>

				<div class="profile__avatar">
					<div class="profile__avatar-wrapper">
						<?php
						$avatarSrc = $user->avatar
							? (strpos($user->avatar, 'http') === 0 ? $user->avatar : (HOST . ltrim($user->avatar, '/')))
							: (HOST . 'assets/img/profile/avatar-placeholder.png');
						?>
						<img src="<?= htmlspecialchars($avatarSrc) ?>" alt="Avatar" class="profile__avatar-img" />
						<button type="button" class="profile__avatar-edit">
							<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path
									d="M3 17.25V21H6.75L17.81 9.94L14.06 6.19L3 17.25ZM20.71 7.04C21.1 6.65 21.1 6.02 20.71 5.63L18.37 3.29C17.98 2.9 17.35 2.9 16.96 3.29L15.13 5.12L18.88 8.87L20.71 7.04Z"
									fill="currentColor"
								/>
							</svg>
						</button>
						<input type="file" id="avatarInput" name="avatar" accept="image/*" style="display:none;" />
					</div>
				</div>

				<form class="profile__form" method="post" enctype="multipart/form-data" id="profileForm">
					<div class="profile__form-group">
						<label for="login" class="profile__label">Логин</label>
						<input type="text" id="login" name="login" class="profile__input" required value="<?php echo htmlspecialchars($user->username); ?>" />
					</div>

					<div class="profile__form-group">
						<label for="email" class="profile__label">Email</label>
						<input type="email" id="email" name="email" class="profile__input" required value="<?php echo htmlspecialchars($user->email); ?>" />
					</div>

					<div class="profile__section">
						<h2 class="profile__section-title">Изменить пароль</h2>
						<div class="profile__form-group">
							<label for="current-password" class="profile__label">Текущий пароль</label>
							<input type="password" id="current-password" name="current_password" class="profile__input" />
						</div>
						<div class="profile__form-group">
							<label for="new-password" class="profile__label">Новый пароль</label>
							<input type="password" id="new-password" name="password" class="profile__input" />
						</div>
						<div class="profile__form-group">
							<label for="confirm-password" class="profile__label">Подтвердите пароль</label>
							<input type="password" id="confirm-password" name="password_confirm" class="profile__input" />
						</div>
					</div>

					<div class="profile__actions">
						<button type="submit" class="profile__submit">Сохранить изменения</button>
					</div>
				</form>

				<div class="profile__logout">
					<form action="logout.php" method="post">
						<button type="submit" class="profile__logout-btn">Выйти из аккаунта</button>
					</form>
				</div>

				<div class="profile__sessions">
					<h2 class="profile__section-title">Мои сессии</h2>
					<div class="profile__sessions-list">
						<?php if (empty($sessions)): ?>
							<div style="color:#888; padding: 20px 0; text-align:center;">Вы не состоите ни в одной сессии.</div>
						<?php else: ?>
							<?php foreach ($sessions as $session): ?>
								<div class="profile__session-card">
									<div class="profile__session-info">
										<h3 class="profile__session-title"><?php echo htmlspecialchars($session['name']); ?></h3>
										<div class="profile__session-meta">
											<span class="profile__session-date"><?php echo date('d.m.Y', strtotime($session['created_at'])); ?></span>
											<span class="profile__session-status profile__session-status--<?php echo $session['status'] === 'active' ? 'active' : 'completed'; ?>">
												<?php echo $session['status'] === 'active' ? 'Активная' : 'Завершена'; ?>
											</span>
										</div>
									</div>
									<?php if ($session['status'] === 'active'): ?>
										<a href="session.php?id=<?php echo $session['id']; ?>" class="profile__session-link">Перейти к&#160;сессии</a>
									<?php else: ?>
										<span class="profile__session-link" style="pointer-events:none;opacity:0.5;cursor:default;">Перейти к&#160;сессии</span>
									<?php endif; ?>
								</div>
							<?php endforeach; ?>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
</main>

<?php
include(ROOT . "templates/footer.tpl");
?>
