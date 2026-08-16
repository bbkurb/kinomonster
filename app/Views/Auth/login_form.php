<?php
/**
 * Форма входа.
 *
 * @var string|null $auth_message
 */
?>
<h2>Вход</h2>
<hr>

<?php if (! empty($auth_message)) : ?>
  <div class="alert alert-danger"><?= esc($auth_message) ?></div>
<?php endif ?>

<?= view('partials/errors', ['validation' => $validation ?? null]) ?>

<form action="<?= base_url('auth/login') ?>" method="post" class="col-lg-6">
  <?= csrf_field() ?>

  <div class="form-group">
    <label for="username">Логин</label>
    <input type="text" class="form-control input-lg" id="username" name="username" value="<?= esc(old('username') ?? '') ?>">
  </div>

  <div class="form-group">
    <label for="password">Пароль</label>
    <input type="password" class="form-control input-lg" id="password" name="password">
  </div>

  <div class="checkbox">
    <label><input type="checkbox" name="remember" value="1"> Запомнить меня</label>
  </div>

  <button type="submit" class="btn btn-warning">войти</button>
  <a href="<?= base_url('auth/forgot-password') ?>" class="btn btn-link">Забыли пароль?</a>
  <a href="<?= base_url('auth/register') ?>" class="btn btn-link">Регистрация</a>
</form>
<div class="clearfix"></div>
