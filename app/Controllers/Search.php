<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\FilmsModel;

/**
 * Поиск по фильмам. Перенос CI3-контроллера Search.
 */
class Search extends BaseController
{
    /**
     * Страница результатов поиска.
     */
    public function index(): string
    {
        $query = trim((string) ($this->request->getGet('q_search') ?? ''));

        $viewData = [
            'title'         => 'Поиск',
            'search_result' => [],
            'q_search'      => $query,
        ];

        if ($query !== '') {
            $films   = new FilmsModel();
            $perPage = 12;
            $page    = max(1, (int) ($this->request->getGet('page') ?? 1));
            $offset  = ($page - 1) * $perPage;
            $total   = $films->countSearch($query);

            $viewData['search_result'] = $films->search($query, $perPage, $offset);
            $viewData['tCount']        = $total;

            // В CI3 строка запроса склеивалась вручную через http_build_query($_GET),
            // и пользовательский ввод попадал в HTML ссылок без экранирования.
            // Pager сам переносит GET-параметры и экранирует их.
            $viewData['pagination'] = service('pager')
                ->makeLinks($page, $perPage, $total, 'bootstrap');
        }

        return $this->renderPage('main/search', $viewData);
    }
}
