<?php
/**
 * Форма смены пароля.
 *
 * @var string|null $auth_message
 */
?>
<h2>Смена пароля</h2>
<hr>

<?php if (! empty($auth_message)) : ?>
  <div class="alert alert-danger"><?= esc($auth_message) ?></div>
<?php endif ?>

<?= view('partials/errors', ['validation' => $validation ?? null]) ?>

<form action="/auth/change-password" method="post" class="col-lg-6">
  <?= csrf_field() ?>

  <div class="form-group">
    <label for="old_password">Старый пароль</label>
    <input type="password" class="form-control input-lg" id="old_password" name="old_password">
  </div>

  <div class="form-group">
    <label for="new_password">Новый пароль (минимум 8 символов)</label>
    <input type="password" class="form-control input-lg" id="new_password" name="new_password">
  </div>

  <div class="form-group">
    <label for="confirm_new_password">Повторите новый пароль</label>
    <input type="password" class="form-control input-lg" id="confirm_new_password" name="confirm_new_password">
  </div>

  <button type="submit" class="btn btn-warning">сменить пароль</button>
</form>
<div class="clearfix"></div>
