<?php
/**
 * Форма установки нового пароля по ссылке из письма.
 *
 * @var string $username
 * @var string $key
 */
?>
<h2>Новый пароль</h2>
<hr>

<?= view('partials/errors', ['validation' => $validation ?? null]) ?>

<form action="/auth/reset-password/<?= esc($username, 'url') ?>/<?= esc($key, 'url') ?>" method="post" class="col-lg-6">
  <?= csrf_field() ?>

  <div class="form-group">
    <label for="password">Новый пароль (минимум 8 символов)</label>
    <input type="password" class="form-control input-lg" id="password" name="password">
  </div>

  <div class="form-group">
    <label for="confirm_password">Повторите пароль</label>
    <input type="password" class="form-control input-lg" id="confirm_password" name="confirm_password">
  </div>

  <button type="submit" class="btn btn-warning">сохранить</button>
</form>
<div class="clearfix"></div>
