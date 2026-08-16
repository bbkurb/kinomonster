<?php
/**
 * Форма добавления фильма/сериала.
 *
 * @var \CodeIgniter\Validation\ValidationInterface|null $validation
 */
?>
<h2>Добавить фильм/сериал</h2>
<hr>

<?= view('partials/errors', ['validation' => $validation ?? null, 'error' => $error ?? null]) ?>

<form action="<?= base_url('movies/create') ?>" method="post">
  <?= csrf_field() ?>
  <?= view('movies/_form', ['item' => []]) ?>
  <button type="submit" class="btn btn-warning pull-right">добавить</button>
  <div class="clearfix"></div>
</form>
