<?php

declare(strict_types=1);

/**
 * Хелпер меню — перенос other_helper.php из CI3.
 *
 * Функция getUserNameByID() из старого хелпера удалена намеренно: она делала
 * отдельный SQL-запрос на каждый комментарий прямо из шаблона (проблема N+1).
 * Имя автора теперь приходит одним JOIN в CommentsModel::getForMovie().
 */
if (! function_exists('show_active_menu')) {
    /**
     * Возвращает class="active" для текущего пункта меню.
     *
     * @param string|int $slug
     * @param string|int $category
     */
    function show_active_menu($slug, $category = ''): string
    {
        $uri = service('uri');

        $segment = static fn (int $n): string => $uri->getTotalSegments() >= $n
            ? $uri->getSegment($n)
            : '';

        $slug     = (string) $slug;
        $category = (string) $category;

        $seg1 = $segment(1);
        $seg2 = $segment(2);
        $seg3 = $segment(3);

        if ($seg1 === $slug && $slug !== '') {
            return 'class="active"';
        }

        if ($seg3 === $slug && $seg1 === 'movies' && $seg2 === 'type') {
            return 'class="active"';
        }

        if ($slug === 'films' && $category === '1' && $seg1 === 'movies' && $seg2 === 'view') {
            return 'class="active"';
        }

        if ($slug === 'serials' && $category === '2' && $seg1 === 'movies' && $seg2 === 'view') {
            return 'class="active"';
        }

        return '';
    }
}

if (! function_exists('safe_url')) {
    /**
     * Разрешает в атрибутах src/href только безопасные схемы.
     *
     * Без этой проверки администратор (или тот, кто получил его доступ) мог бы
     * записать в поле «ссылка на плеер» значение вида javascript:alert(1),
     * которое выполнилось бы у каждого посетителя страницы фильма.
     * Экранирование здесь не спасает: значение остаётся валидным URL.
     */
    function safe_url(?string $url): string
    {
        $url = trim((string) $url);

        if ($url === '') {
            return '';
        }

        // Протокол-относительные ссылки (//example.com/x.jpg) отдаём как есть.
        if (str_starts_with($url, '//')) {
            return $url;
        }

        // Путь от корня (/assets/img/x.png) достраиваем через base_url():
        // иначе при размещении в подпапке (localhost/kinomonster/public)
        // картинки искались бы в корне домена и не находились.
        if (str_starts_with($url, '/')) {
            return base_url(ltrim($url, '/'));
        }

        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));

        return in_array($scheme, ['http', 'https', ''], true) ? $url : '';
    }
}

if (! function_exists('truncate_title')) {
    /**
     * Обрезка названия фильма для карточки.
     *
     * В CI3-шаблонах это был копипаст `if (strlen(...) > 19) mb_substr(...)`,
     * причём strlen() на UTF-8 считал байты, а не символы — русские названия
     * обрезались вдвое раньше нужного. Здесь везде mb_*.
     */
    function truncate_title(string $title, int $limit = 19): string
    {
        return mb_strlen($title) > $limit
            ? mb_substr($title, 0, $limit) . '...'
            : $title;
    }
}
