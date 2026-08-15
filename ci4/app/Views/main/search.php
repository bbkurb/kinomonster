<?php
/**
 * Результаты поиска.
 *
 * @var array<int, array<string, mixed>> $search_result
 * @var string                           $q_search
 * @var int|null                         $tCount
 * @var string|null                      $pagination
 */
?>
          <h2>Поиск</h2>
          <hr>

          <?php if ($q_search === '') : ?>

            <p>Введите запрос в поле поиска.</p>

          <?php elseif ($search_result === []) : ?>

            <p>По запросу «<?= esc($q_search) ?>» ничего не найдено.</p>

          <?php else : ?>

            <p>По запросу «<?= esc($q_search) ?>» найдено: <?= esc((string) ($tCount ?? 0)) ?></p>

            <div class="row">
              <?php foreach ($search_result as $item) : ?>
                <div class="films_block col-lg-3 col-md-3 col-sm-3 col-xs-6">
                  <a href="/movies/view/<?= esc($item['slug'], 'url') ?>">
                    <img height="275" src="<?= esc(safe_url($item['poster'])) ?>" alt="<?= esc($item['name']) ?>">
                  </a>
                  <div class="film_label">
                    <a href="/movies/view/<?= esc($item['slug'], 'url') ?>"><?= esc(truncate_title($item['name'])) ?></a>
                  </div>
                </div>
              <?php endforeach ?>
            </div>

            <div class="text-center"><?= $pagination ?? '' ?></div>

          <?php endif ?>
