<?php
/**
 * Форма регистрации.
 *
 * @var string|null $auth_message
 */
?>
<h2>Регистрация</h2>
<hr>

<?php if (! empty($auth_message)) : ?>
  <div class="alert alert-danger"><?= esc($auth_message) ?></div>
<?php endif ?>

<?= view('partials/errors', ['validation' => $validation ?? null]) ?>

<form action="/auth/register" method="post" class="col-lg-6">
  <?= csrf_field() ?>

  <div class="form-group">
    <label for="username">Логин</label>
    <input type="text" class="form-control input-lg" id="username" name="username" value="<?= esc(old('username') ?? '') ?>">
  </div>

  <div class="form-group">
    <label for="email">Email</label>
    <input type="email" class="form-control input-lg" id="email" name="email" value="<?= esc(old('email') ?? '') ?>">
  </div>

  <div class="form-group">
    <label for="password">Пароль (минимум 8 символов)</label>
    <input type="password" class="form-control input-lg" id="password" name="password">
  </div>

  <div class="form-group">
    <label for="confirm_password">Повторите пароль</label>
    <input type="password" class="form-control input-lg" id="confirm_password" name="confirm_password">
  </div>

  <button type="submit" class="btn btn-warning">зарегистрироваться</button>
</form>
<div class="clearfix"></div>
