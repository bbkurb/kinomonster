<?php
/**
 * Форма запроса восстановления пароля.
 */
?>
<h2>Восстановление пароля</h2>
<hr>

<?= view('partials/errors', ['validation' => $validation ?? null]) ?>

<form action="<?= base_url('auth/forgot-password') ?>" method="post" class="col-lg-6">
  <?= csrf_field() ?>

  <div class="form-group">
    <label for="login">Логин или Email</label>
    <input type="text" class="form-control input-lg" id="login" name="login" value="<?= esc(old('login') ?? '') ?>">
  </div>

  <button type="submit" class="btn btn-warning">восстановить</button>
</form>
<div class="clearfix"></div>
