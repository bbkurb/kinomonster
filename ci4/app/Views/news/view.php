<?php
/**
 * Страница новости.
 *
 * @var \App\Services\Auth $auth
 */
?>
<h1><?= esc($title) ?>
  <?php if ($auth->isAdmin()) : ?>
    <a href="/news/edit/<?= esc($slug, 'url') ?>"><button type="button" class="btn btn-default">
      <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button></a>
  <?php endif ?>
</h1>
<hr>

<div class="well">
  <?= esc($content) ?>
</div>

<a href="/news" class="btn btn-default">все новости</a>
