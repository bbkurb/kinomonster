<?php
/**
 * Форма редактирования новости.
 *
 * @var array<string, mixed> $news_item
 */
?>
<h2>Редактировать новость</h2>
<hr>

<?= view('partials/errors', ['validation' => $validation ?? null]) ?>

<form action="<?= base_url('news/edit/') ?><?= esc($news_item['slug'], 'url') ?>" method="post">
  <?= csrf_field() ?>
  <?= view('partials/article_form', ['item' => $news_item]) ?>
  <button type="submit" class="btn btn-warning pull-right">сохранить</button>
  <div class="clearfix"></div>
</form>
