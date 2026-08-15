<?php
/**
 * Страница фильма/сериала.
 *
 * @var array<int, array<string, mixed>> $comments
 * @var \App\Services\Auth               $auth
 */
?>
<h1><?= esc($title) ?>
  <?php if ($auth->isAdmin()) : ?>
    <a href="/movies/edit/<?= esc($slug, 'url') ?>"><button type="button" class="btn btn-default">
      <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button></a>
  <?php endif ?>
</h1>
<hr>

<div class="embed-responsive embed-responsive-16by9">
  <iframe class="embed-responsive-item" src="<?= esc(safe_url($player_code)) ?>" frameborder="0" allowfullscreen></iframe>
</div>

<div class="well info-block text-center">
  Год: <span class="badge"><?= esc($year) ?></span>
  Рейтинг: <span class="badge"><?= esc($rating) ?></span>
  Режиссер: <span class="badge"><?= esc($director) ?></span>
</div>

<div class="margin-8"></div>

<h2>Описание <?= (int) $category === 1 ? 'фильма' : 'сериала' ?> <?= esc($title) ?></h2>
<hr>

<div class="well">
  <?= esc($descriptions_movie) ?>
</div>

<div class="margin-8"></div>

<h2>Отзывы о <?= (int) $category === 1 ? 'фильме' : 'сериале' ?> <?= esc($title) ?></h2>
<hr>

<?php foreach ($comments as $comment) : ?>
  <div class="panel panel-info">
    <div class="panel-heading">
      <i class="glyphicon glyphicon-user"></i>
      <span><?= esc($comment['username'] ?? 'Пользователь удалён') ?></span>
    </div>
    <div class="panel-body">
      <?= esc($comment['comment_text']) ?>
    </div>
  </div>
<?php endforeach ?>

<?php if ($auth->isLoggedIn()) : ?>

  <form action="/movies/comment" method="post">
    <?= csrf_field() ?>
    <?php /* user_id больше не передаётся из формы — сервер берёт его из сессии. */ ?>
    <input type="hidden" name="movie_id" value="<?= esc($id) ?>">

    <div class="form-group">
      <textarea class="form-control" name="comment_text" placeholder="ваш комментарий" required></textarea>
    </div>

    <button class="btn btn-lg btn-warning pull-right">отправить</button>
    <div class="clearfix"></div>
  </form>

<?php else : ?>

  <br>
  <p>Чтобы оставить комментарий, <a href="/auth/login">войдите</a> или <a href="/auth/register">зарегистрируйтесь</a>!</p>

<?php endif ?>
