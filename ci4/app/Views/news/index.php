<?php
/**
 * Список новостей.
 *
 * @var array<int, array<string, mixed>> $news
 * @var \App\Services\Auth               $auth
 */
?>
<h2>Все новости
  <?php if ($auth->isAdmin()) : ?>
    <a href="/news/create" class="btn btn-success btn-sm">добавить</a>
  <?php endif ?>
</h2>
<hr>

<?php foreach ($news as $item) : ?>
  <div class="news-item">
    <a href="/news/view/<?= esc($item['slug'], 'url') ?>"><h3><?= esc($item['title']) ?></h3></a>
    <p><?= esc(mb_substr((string) $item['text'], 0, 300)) ?><?= mb_strlen((string) $item['text']) > 300 ? '...' : '' ?></p>

    <a href="/news/view/<?= esc($item['slug'], 'url') ?>" class="btn btn-warning">читать</a>

    <?php if ($auth->isAdmin()) : ?>
      <a href="/news/edit/<?= esc($item['slug'], 'url') ?>" class="btn btn-default btn-sm">править</a>
      <form action="/news/delete/<?= esc($item['slug'], 'url') ?>" method="post" style="display:inline"
            onsubmit="return confirm('Удалить новость?')">
        <?= csrf_field() ?>
        <button type="submit" class="btn btn-danger btn-sm">удалить</button>
      </form>
    <?php endif ?>

    <hr>
  </div>
<?php endforeach ?>
