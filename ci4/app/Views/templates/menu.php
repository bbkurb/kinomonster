<?php
/**
 * Боковая колонка: поиск, вход, новости, рейтинг.
 *
 * @var array<int, array<string, mixed>> $sidebarNews
 * @var array<int, array<string, mixed>> $sidebarFilms
 * @var \App\Services\Auth               $auth
 */
?>
        <div class="col-lg-3 col-lg-pull-9">

          <div class="panel panel-info hidden-xs">
            <div class="panel-heading"><div class="sidebar-header">Поиск</div></div>
            <div class="panel-body">
              <form role="search" action="/search" method="get">
                <div class="form-group">
                  <div class="input-group">
                    <input type="search" name="q_search" class="form-control input-lg" placeholder="Ваш запрос">
                    <div class="input-group-btn">
                      <button class="btn btn-default btn-lg" type="submit"><i class="glyphicon glyphicon-search"></i></button>
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>

          <div class="panel panel-info">
            <div class="panel-heading"><div class="sidebar-header">Вход</div></div>
            <div class="panel-body">

              <?php if (! $auth->isLoggedIn()) : ?>

              <form role="form" action="/auth/login" method="post">
                <?= csrf_field() ?>
                <div class="form-group">
                  <input type="text" class="form-control input-lg" placeholder="Логин" name="username" value="<?= esc(old('username') ?? '') ?>">
                </div>
                <div class="form-group">
                  <input type="password" class="form-control input-lg" placeholder="Пароль" name="password">
                </div>
                <button type="submit" class="btn btn-warning pull-right">вход</button>
                <dd>
                  <label>
                    <input type="checkbox" name="remember" value="1"> Запомнить меня
                  </label>
                  <br>
                  <a href="/auth/forgot-password">Восстановить пароль</a><br>
                  <a href="/auth/register">Регистрация</a>
                </dd>
              </form>

              <?php else : ?>

                Здравствуйте, <?= esc($auth->username()) ?>
                <a href="/auth/logout" class="btn btn-warning pull-right">выход</a>

                <?php if ($auth->isAdmin()) : ?>
                  <div class="clearfix"></div>
                  <hr>
                  <a href="/backend/users">Панель управления</a>
                <?php endif ?>

              <?php endif ?>

            </div>
          </div>

          <div class="panel panel-info">
            <div class="panel-heading"><div class="sidebar-header">Новости</div></div>
            <div class="panel-body">
              <?php foreach ($sidebarNews as $item) : ?>
                <p><a href="/news/view/<?= esc($item['slug'], 'url') ?>"><?= esc($item['title']) ?></a></p>
              <?php endforeach ?>
            </div>
          </div>

          <div class="panel panel-info">
            <div class="panel-heading"><div class="sidebar-header">Рейтинг фильмов</div></div>
            <div class="panel-body">
                <ul class="list-group">
                  <?php foreach ($sidebarFilms as $item) : ?>
                    <li class="list-group-item list-group-warning">
                      <span class="badge"><?= esc($item['rating']) ?></span>
                      <a href="/movies/view/<?= esc($item['slug'], 'url') ?>"><?= esc($item['name']) ?></a>
                    </li>
                  <?php endforeach ?>
                </ul>
            </div>
          </div>

        </div>
