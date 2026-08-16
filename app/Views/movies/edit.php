<?php
/**
 * Форма редактирования фильма/сериала.
 *
 * @var array<string, mixed> $movies_item
 */
?>
<h2>Редактировать: <?= esc($movies_item['name']) ?></h2>
<hr>

<?= view('partials/errors', ['validation' => $validation ?? null]) ?>

<form action="<?= base_url('movies/edit/') ?><?= esc($movies_item['slug'], 'url') ?>" method="post">
  <?= csrf_field() ?>
  <?= view('movies/_form', ['item' => $movies_item]) ?>
  <button type="submit" class="btn btn-warning pull-right">сохранить</button>
  <div class="clearfix"></div>
</form>
