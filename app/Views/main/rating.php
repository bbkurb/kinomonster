<?php
/**
 * Рейтинг фильмов.
 *
 * @var array<int, array<string, mixed>> $movie
 * @var string                           $pagination
 */
?>
          <h2>Рейтинг фильмов</h2>
          <hr>

          <div class="row">
            <?php foreach ($movie as $item) : ?>
              <div class="films_block col-lg-3 col-md-3 col-sm-3 col-xs-6">
                <a href="<?= base_url('movies/view/') ?><?= esc($item['slug'], 'url') ?>">
                  <img height="275" src="<?= esc(safe_url($item['poster'])) ?>" alt="<?= esc($item['name']) ?>">
                </a>
                <div class="film_label">
                  <span class="badge"><?= esc($item['rating']) ?></span>
                  <a href="<?= base_url('movies/view/') ?><?= esc($item['slug'], 'url') ?>"><?= esc(truncate_title($item['name'])) ?></a>
                </div>
              </div>
            <?php endforeach ?>
          </div>

          <div class="text-center"><?= $pagination ?? '' ?></div>
