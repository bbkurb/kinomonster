<?php
/**
 * Форма добавления новости.
 */
?>
<h2>Добавить новость</h2>
<hr>

<?= view('partials/errors', ['validation' => $validation ?? null]) ?>

<form action="/news/create" method="post">
  <?= csrf_field() ?>
  <?= view('partials/article_form', ['item' => []]) ?>
  <button type="submit" class="btn btn-warning pull-right">добавить</button>
  <div class="clearfix"></div>
</form>
