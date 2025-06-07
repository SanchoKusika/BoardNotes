<?php
require("config.php");
require("db.php");

include(ROOT . "templates/head.tpl");
include(ROOT . "templates/header.tpl");
?>

<main>
	<section class="auth">
		<div class="container">
			<div class="auth__row">
				<div class="auth__tabs">
					<button class="auth__tab active" data-tab="login">Вход</button>
					<button class="auth__tab" data-tab="register">Регистрация</button>
				</div>

				<form class="auth__form auth__form--login active" id="loginForm">
					<div class="auth__notifications"></div>
					<div class="auth__form-group">
						<label for="login" class="form__label">Логин</label>
						<input type="text" id="login" name="login" class="form__input" required />
					</div>
					<div class="auth__form-group">
						<label for="loginPassword">Пароль</label>
						<input type="password" id="loginPassword" name="password" required />
					</div>
					<button type="submit" class="auth__submit">Войти</button>
				</form>

				<form class="auth__form auth__form--register" id="registerForm">
					<div class="auth__notifications"></div>
					<div class="auth__form-group">
						<label for="registerLogin">Логин</label>
						<input type="text" id="registerLogin" name="login" required />
					</div>
					<div class="auth__form-group">
						<label for="registerEmail">Email</label>
						<input type="email" id="registerEmail" name="email" required />
					</div>
					<div class="auth__form-group">
						<label for="registerPassword">Пароль</label>
						<input type="password" id="registerPassword" name="password" required />
					</div>
					<div class="auth__form-group">
						<label for="registerPasswordConfirm">Подтвердите пароль</label>
						<input type="password" id="registerPasswordConfirm" name="passwordConfirm" required />
					</div>
					<button type="submit" class="auth__submit">Зарегистрироваться</button>
				</form>
			</div>
		</div>
	</section>
</main>

<?php
include(ROOT . "templates/footer.tpl");
?>
