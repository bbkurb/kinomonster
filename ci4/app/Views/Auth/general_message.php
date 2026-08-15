<?php
/**
 * Универсальная страница-сообщение (успех регистрации, сброс пароля и т.п.).
 *
 * @var string $auth_message
 */
?>
<h2><?= esc($title) ?></h2>
<hr>

<div class="well">
  <?php /* Сообщение может содержать ссылку из anchor(), поэтому не экранируем. */ ?>
  <?= $auth_message ?>
</div>

<a href="/" class="btn btn-default">на главную</a>
