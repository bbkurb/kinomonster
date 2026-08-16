<?php

declare(strict_types=1);

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

/*
 * Маршруты КиноМонстра.
 *
 * В CI3 URL разбирался автоматически: /movies/create вызывал Movies::create()
 * без какого-либо объявления, а права проверялись внутри самих методов. Здесь
 * каждый маршрут объявлен явно и защищён фильтром, а автороутинг выключен
 * (см. Config/Routing.php) — попасть в метод контроллера в обход правил нельзя.
 *
 * Все операции изменения данных вынесены на POST: в CI3 удаление выполнялось
 * по GET-ссылке.
 */

$routes->get('/', 'Main::index');
$routes->match(['get', 'post'], 'contact', 'Main::contact');
$routes->get('rating', 'Main::rating');
$routes->get('search', 'Search::index');
$routes->get('search/index', 'Search::index');

/* ---------------------------------------------------------------- */
/* Новости                                                           */
/* ---------------------------------------------------------------- */
$routes->get('news', 'News::index');
$routes->group('news', ['filter' => 'admin'], static function ($routes): void {
    $routes->match(['get', 'post'], 'create', 'News::create');
    $routes->match(['get', 'post'], 'edit/(:segment)', 'News::edit/$1');
    $routes->post('delete/(:segment)', 'News::delete/$1');
});
$routes->get('news/view/(:segment)', 'News::view/$1');
$routes->get('news/(:segment)', 'News::view/$1');

/* ---------------------------------------------------------------- */
/* Посты                                                             */
/* ---------------------------------------------------------------- */
$routes->get('posts', 'Posts::index');
$routes->group('posts', ['filter' => 'admin'], static function ($routes): void {
    $routes->match(['get', 'post'], 'create', 'Posts::create');
    $routes->match(['get', 'post'], 'edit/(:segment)', 'Posts::edit/$1');
    $routes->post('delete/(:segment)', 'Posts::delete/$1');
});
$routes->get('posts/view/(:segment)', 'Posts::view/$1');
$routes->get('posts/(:segment)', 'Posts::view/$1');

/* ---------------------------------------------------------------- */
/* Фильмы и сериалы                                                  */
/* ---------------------------------------------------------------- */
$routes->get('movies', 'Movies::index', ['filter' => 'admin']);
$routes->get('movies/type/(:segment)', 'Movies::type/$1');
$routes->post('movies/comment', 'Movies::comment', ['filter' => 'auth']);
$routes->group('movies', ['filter' => 'admin'], static function ($routes): void {
    $routes->match(['get', 'post'], 'create', 'Movies::create');
    $routes->match(['get', 'post'], 'edit/(:segment)', 'Movies::edit/$1');
    $routes->post('delete/(:segment)', 'Movies::delete/$1');
});
$routes->get('movies/view/(:segment)', 'Movies::view/$1');
$routes->get('movies/(:segment)', 'Movies::view/$1');

/* ---------------------------------------------------------------- */
/* Аутентификация                                                    */
/* ---------------------------------------------------------------- */
$routes->match(['get', 'post'], 'auth/login', 'Auth::login');
$routes->get('auth/logout', 'Auth::logout');
$routes->match(['get', 'post'], 'auth/register', 'Auth::register');
$routes->match(['get', 'post'], 'auth/forgot-password', 'Auth::forgotPassword');
$routes->match(['get', 'post'], 'auth/reset-password/(:segment)/(:segment)', 'Auth::resetPassword/$1/$2');
$routes->match(['get', 'post'], 'auth/change-password', 'Auth::changePassword', ['filter' => 'auth']);
$routes->get('auth', 'Auth::index');

/* ---------------------------------------------------------------- */
/* Админка                                                           */
/* ---------------------------------------------------------------- */
$routes->group('backend', ['filter' => 'admin'], static function ($routes): void {
    $routes->get('/', 'Backend::index');
    $routes->match(['get', 'post'], 'users', 'Backend::users');
});
