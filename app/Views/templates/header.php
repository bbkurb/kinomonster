<?php
/**
 * Шапка сайта.
 *
 * @var string $title
 * @var string $category
 */
?>
<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title) ?></title>

    <!-- Bootstrap -->
    <link href="<?= base_url('assets/css/bootstrap.min.css') ?>" rel="stylesheet">

    <!-- Main Style -->
    <link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
  </head>
  <body>

    <div class="container-fluid">
      <div class="row">

       <nav role="navigation" class="navbar navbar-inverse">
          <div class="container">

          <div class="navbar-header header">

            <div class="container">
              <div class="row">
                <div class="col-lg-12">
                  <h1><a href="<?= base_url() ?>">КиноМонстр</a></h1>
                  <p>Кино - наша страсть!</p>
                </div>
              </div>
            </div>

            <button type="button" data-target="#navbarCollapse" data-toggle="collapse" class="navbar-toggle">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>

          </div>

            <div id="navbarCollapse" class="collapse navbar-collapse navbar-right">
              <ul class="nav nav-pills">
                <li <?= show_active_menu('', '') ?>> <a href="<?= base_url() ?>">Главная</a> </li>
                <li <?= show_active_menu('films', $category ?? '') ?>> <a href="<?= base_url('movies/type/films') ?>">Фильмы</a> </li>
                <li <?= show_active_menu('serials', $category ?? '') ?>> <a href="<?= base_url('movies/type/serials') ?>">Сериалы</a> </li>
                <li <?= show_active_menu('rating', '') ?>> <a href="<?= base_url('rating') ?>">Рейтинг фильмов</a> </li>
                <li <?= show_active_menu('contact', '') ?>> <a href="<?= base_url('contact') ?>">Контакты</a> </li>
              </ul>
            </div>

          </div>
       </nav>

      </div>
    </div>

    <div class="wrapper">
      <div class="container">
        <div class="row">

        <div class="col-lg-9 col-lg-push-3">

          <form role="search" action="<?= base_url('search') ?>" method="get" class="visible-xs">
            <div class="form-group">
              <div class="input-group">
                <input type="search" name="q_search" class="form-control input-lg" placeholder="Ваш запрос">
                <div class="input-group-btn">
                  <button class="btn btn-default btn-lg" type="submit"><i class="glyphicon glyphicon-search"></i></button>
                </div>
              </div>
            </div>
          </form>

          <?php if (session()->getFlashdata('msg')) : ?>
            <div class="alert alert-success text-center"><?= esc(session()->getFlashdata('msg')) ?></div>
          <?php endif ?>

          <?php if (session()->getFlashdata('error')) : ?>
            <div class="alert alert-danger text-center"><?= esc(session()->getFlashdata('error')) ?></div>
          <?php endif ?>
