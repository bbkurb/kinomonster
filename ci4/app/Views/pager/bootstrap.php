<?php
/**
 * Шаблон пагинации в разметке Bootstrap 3.
 *
 * Заменяет ~30 строк $p_config['*_tag_open'], которые в CI3 были скопированы
 * в Main, Movies, Rating, Search и Backend.
 *
 * @var \CodeIgniter\Pager\PagerRenderer $pager
 */
$pager->setSurroundCount(2);
?>

<?php if ($pager->getPageCount() > 1) : ?>
<ul class="pagination">

  <?php if ($pager->hasPrevious()) : ?>
    <li>
      <a href="<?= esc($pager->getFirst(), 'url') ?>" aria-label="Первая">&laquo;</a>
    </li>
    <li>
      <a href="<?= esc($pager->getPrevious(), 'url') ?>" aria-label="Назад">&lsaquo;</a>
    </li>
  <?php endif ?>

  <?php foreach ($pager->links() as $link) : ?>
    <li <?= $link['active'] ? 'class="active"' : '' ?>>
      <a href="<?= esc($link['uri'], 'url') ?>"><?= esc($link['title']) ?></a>
    </li>
  <?php endforeach ?>

  <?php if ($pager->hasNext()) : ?>
    <li>
      <a href="<?= esc($pager->getNext(), 'url') ?>" aria-label="Вперёд">&rsaquo;</a>
    </li>
    <li>
      <a href="<?= esc($pager->getLast(), 'url') ?>" aria-label="Последняя">&raquo;</a>
    </li>
  <?php endif ?>

</ul>
<?php endif ?>
