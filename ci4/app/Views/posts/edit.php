<?php
/**
 * Форма редактирования новости.
 *
 * @var array<string, mixed> $posts_item
 */
?>
<h2>Редактировать пост</h2>
<hr>

<?= view('partials/errors', ['validation' => $validation ?? null]) ?>

<form action="/posts/edit/<?= esc($posts_item['slug'], 'url') ?>" method="post">
  <?= csrf_field() ?>
  <?= view('partials/article_form', ['item' => $posts_item]) ?>
  <button type="submit" class="btn btn-warning pull-right">сохранить</button>
  <div class="clearfix"></div>
</form>
