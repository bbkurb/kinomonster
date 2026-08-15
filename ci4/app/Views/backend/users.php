<?php
/**
 * Админка: список пользователей.
 *
 * В CI3 чекбоксы назывались checkbox_0, checkbox_1... и контроллер искал их
 * перебором всего массива $_POST. Здесь это обычный массив users[].
 *
 * @var array<int, array<string, mixed>> $users
 * @var string                           $pagination
 */
?>
<h2>Пользователи</h2>
<hr>

<form action="/backend/users" method="post">
  <?= csrf_field() ?>

  <table class="table table-striped">
    <thead>
      <tr>
        <th></th>
        <th>ID</th>
        <th>Логин</th>
        <th>Email</th>
        <th>Роль</th>
        <th>Статус</th>
        <th>Последний вход</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($users as $user) : ?>
        <tr>
          <td><input type="checkbox" name="users[]" value="<?= esc($user['id']) ?>"></td>
          <td><?= esc($user['id']) ?></td>
          <td><?= esc($user['username']) ?></td>
          <td><?= esc($user['email']) ?></td>
          <td><?= esc($user['role'] ?? 'user') ?></td>
          <td>
            <?php if ((int) ($user['banned'] ?? 0) === 1) : ?>
              <span class="label label-danger">забанен</span>
            <?php else : ?>
              <span class="label label-success">активен</span>
            <?php endif ?>
          </td>
          <td><?= esc($user['last_login'] ?? '—') ?></td>
        </tr>
      <?php endforeach ?>
    </tbody>
  </table>

  <div class="form-group">
    <input type="text" class="form-control" name="ban_reason" placeholder="Причина блокировки (необязательно)">
  </div>

  <button type="submit" name="action" value="ban" class="btn btn-danger">забанить</button>
  <button type="submit" name="action" value="unban" class="btn btn-success">разбанить</button>
</form>

<div class="text-center"><?= $pagination ?? '' ?></div>
