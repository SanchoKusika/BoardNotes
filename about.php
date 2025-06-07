<?php
require("config.php");
require("db.php");

include(ROOT . "templates/head.tpl");
include(ROOT . "templates/header.tpl");
?>

<main>
	<section class="about">
		<div class="container">
			<div class="about__row">
				<div class="about__content">
					<h1 class="about__title">О&#160;проекте BoardNotes</h1>
					<div class="about__text">
						<p>BoardNotes&#160;&#8212; это современный инструмент для организации задач и&#160;управления проектами, вдохновленный методологией Канбан.</p>

						<h2>Основные возможности:</h2>
						<ul class="about__features">
							<li>Создание и&#160;управление задачами в&#160;стиле стикеров</li>
							<li>Перетаскивание задач между колонками (К&#160;выполнению, В&#160;процессе, Готово)</li>
							<li>Загрузка и&#160;управление файлами</li>
							<li>Удобный и&#160;современный интерфейс</li>
						</ul>

						<h2>Как это работает:</h2>
						<p>Просто создавайте задачи, перетаскивайте их&#160;между колонками и&#160;следите за&#160;прогрессом. Все изменения сохраняются автоматически.</p>

						<h2>Технологии:</h2>
						<ul class="about__tech">
							<li>HTML, SCSS, JavaScript, Gulp, React, PHP, MySQL</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</section>
</main>

<?php
include(ROOT . "templates/footer.tpl");
?>
