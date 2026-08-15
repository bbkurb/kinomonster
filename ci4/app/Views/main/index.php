<?php
/**
 * Главная страница.
 *
 * @var array<int, array<string, mixed>> $movie
 * @var array<int, array<string, mixed>> $serials
 * @var array<int, array<string, mixed>> $posts
 * @var \App\Services\Auth               $auth
 */
?>
          <h2>Новые фильмы
            <?php if ($auth->isAdmin()) : ?>
              <a href="/movies"><button type="button" class="btn btn-default">
                <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button></a>
            <?php endif ?>
          </h2>
          <hr>
          <div class="row">
            <?php foreach ($movie as $item) : ?>
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

          <div class="margin-8"></div>

          <h2>Новые сериалы
            <?php if ($auth->isAdmin()) : ?>
              <a href="/movies"><button type="button" class="btn btn-default">
                <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button></a>
            <?php endif ?>
          </h2>
          <hr>
          <div class="row">
            <?php foreach ($serials as $item) : ?>
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

          <div class="margin-8"></div>

          <?php foreach ($posts as $post) : ?>
            <a href="/posts/view/<?= esc($post['slug'], 'url') ?>"><h3><?= esc($post['title']) ?></h3></a>
            <hr>
            <p><?= esc($post['text']) ?></p>
            <a href="/posts/view/<?= esc($post['slug'], 'url') ?>" class="btn btn-warning pull-right">читать</a>
            <div class="margin-8"></div>
          <?php endforeach ?>
