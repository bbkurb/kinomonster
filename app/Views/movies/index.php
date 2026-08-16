<?php
/**
 * Админский список фильмов и сериалов.
 *
 * Кнопки удаления теперь отправляют POST-форму, а не GET-ссылку.
 *
 * @var array<int, array<string, mixed>> $movies
 * @var array<int, array<string, mixed>> $serials
 */
?>
<h2>Все фильмы/сериалы
  <a href="<?= base_url('movies/create') ?>" class="btn btn-success btn-sm">добавить</a>
</h2>
<hr>

<?php
$renderTable = static function (array $items, string $caption): void { ?>
  <h3><?= esc($caption) ?></h3>
  <table class="table table-striped">
    <thead>
      <tr><th>Название</th><th>Год</th><th>Рейтинг</th><th></th></tr>
    </thead>
    <tbody>
      <?php foreach ($items as $item) : ?>
        <tr>
          <td><a href="<?= base_url('movies/view/') ?><?= esc($item['slug'], 'url') ?>"><?= esc($item['name']) ?></a></td>
          <td><?= esc($item['year']) ?></td>
          <td><?= esc($item['rating']) ?></td>
          <td>
            <a href="<?= base_url('movies/edit/') ?><?= esc($item['slug'], 'url') ?>" class="btn btn-default btn-xs">править</a>
            <form action="<?= base_url('movies/delete/') ?><?= esc($item['slug'], 'url') ?>" method="post" style="display:inline"
                  onsubmit="return confirm('Удалить «<?= esc($item['name'], 'js') ?>»?')">
              <?= csrf_field() ?>
              <button type="submit" class="btn btn-danger btn-xs">удалить</button>
            </form>
          </td>
        </tr>
      <?php endforeach ?>
    </tbody>
  </table>
<?php };

$renderTable($movies, 'Фильмы');
$renderTable($serials, 'Сериалы');
