<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\CommentsModel;
use App\Models\FilmsModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * Фильмы и сериалы. Перенос CI3-контроллера Movies.
 *
 * Проверки прав (is_admin / is_logged_in) больше не живут внутри методов —
 * они объявлены фильтрами в app/Config/Routes.php.
 */
class Movies extends BaseController
{
    private FilmsModel $films;

    public function initController($request, $response, $logger): void
    {
        parent::initController($request, $response, $logger);
        $this->films = new FilmsModel();
    }

    /**
     * Список всех фильмов и сериалов (админский раздел).
     */
    public function index(): string
    {
        return $this->renderPage('movies/index', [
            'title'   => 'Все фильмы/сериалы',
            'movies'  => $this->films->getAllByCategory(FilmsModel::CATEGORY_FILM),
            'serials' => $this->films->getAllByCategory(FilmsModel::CATEGORY_SERIAL),
        ]);
    }

    /**
     * Страница фильма.
     */
    public function view(?string $slug = null): string
    {
        if ($slug === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $movie = $this->films->getBySlug($slug);

        if ($movie === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $comments = (new CommentsModel())->getForMovie((int) $movie['id']);

        return $this->renderPage('movies/view', [
            'title'              => $movie['name'],
            'id'                 => $movie['id'],
            'slug'               => $movie['slug'],
            'player_code'        => $movie['player_code'],
            'year'               => $movie['year'],
            'rating'             => $movie['rating'],
            'descriptions_movie' => $movie['descriptions'],
            'director'           => $movie['director'],
            'category'           => $movie['category_id'],
            'comments'           => $comments,
        ]);
    }

    /**
     * Список по типу: films или serials.
     */
    public function type(?string $slug = null): string
    {
        $categories = [
            'films'   => [FilmsModel::CATEGORY_FILM, 'Фильмы'],
            'serials' => [FilmsModel::CATEGORY_SERIAL, 'Сериалы'],
        ];

        if ($slug === null || ! isset($categories[$slug])) {
            throw PageNotFoundException::forPageNotFound();
        }

        [$categoryId, $title] = $categories[$slug];

        $perPage = 3;
        $page    = max(1, (int) ($this->request->getGet('page') ?? 1));
        $offset  = ($page - 1) * $perPage;
        $total   = $this->films->countByCategory($categoryId);

        return $this->renderPage('movies/type', [
            'title'      => $title,
            'category'   => (string) $categoryId,
            'movie_data' => $this->films->getPageByCategory($perPage, $offset, $categoryId),
            'pagination' => service('pager')->makeLinks($page, $perPage, $total, 'bootstrap'),
        ]);
    }

    /**
     * Добавление фильма.
     *
     * @return string|RedirectResponse
     */
    public function create()
    {
        if ($this->request->getMethod() === 'POST') {
            $data = $this->movieDataFromRequest();

            if (! $this->validateData($data, $this->movieRules())) {
                return $this->renderPage('movies/create', [
                    'title'      => 'Добавить фильм/сериал',
                    'validation' => $this->validator,
                ]);
            }

            if ($this->films->insert($data)) {
                return redirect()->to('/movies')
                    ->with('msg', 'Фильм добавлен!');
            }

            return $this->renderPage('movies/create', [
                'title' => 'Добавить фильм/сериал',
                'error' => 'Не удалось сохранить запись.',
            ]);
        }

        return $this->renderPage('movies/create', ['title' => 'Добавить фильм/сериал']);
    }

    /**
     * Редактирование фильма.
     *
     * @return string|RedirectResponse
     */
    public function edit(?string $slug = null)
    {
        if ($slug === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $movie = $this->films->getBySlug($slug);

        if ($movie === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        if ($this->request->getMethod() === 'POST') {
            $data = $this->movieDataFromRequest();

            if (! $this->validateData($data, $this->movieRules((int) $movie['id']))) {
                return $this->renderPage('movies/edit', [
                    'title'       => 'Редактировать фильм/сериал',
                    'movies_item' => $movie,
                    'validation'  => $this->validator,
                ]);
            }

            if ($this->films->update((int) $movie['id'], $data)) {
                return redirect()->to('/movies/edit/' . $data['slug'])
                    ->with('msg', 'Успешно обновлено');
            }
        }

        return $this->renderPage('movies/edit', [
            'title'       => 'Редактировать фильм/сериал',
            'movies_item' => $movie,
        ]);
    }

    /**
     * Удаление фильма.
     *
     * Требует POST: в CI3 удаление происходило по обычной GET-ссылке, что
     * позволяло удалить запись подсунутой картинкой или предзагрузкой ссылки.
     */
    public function delete(?string $slug = null): RedirectResponse
    {
        if ($slug === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $movie = $this->films->getBySlug($slug);

        if ($movie === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        if ($this->films->deleteBySlug($slug)) {
            return redirect()->to('/movies')
                ->with('msg', $movie['name'] . ' успешно удалён');
        }

        return redirect()->to('/movies')
            ->with('error', 'Ошибка удаления ' . $movie['name']);
    }

    /**
     * Добавление комментария.
     */
    public function comment(): RedirectResponse
    {
        $movieId = (int) $this->request->getPost('movie_id');
        $text    = trim((string) $this->request->getPost('comment_text'));

        // user_id берётся из сессии, а не из формы. В CI3 он приходил скрытым
        // полем ввода — любой залогиненный мог оставить комментарий от чужого
        // имени, просто подменив значение.
        $userId = $this->auth->id();

        if ($userId === null) {
            return redirect()->to('/auth/login');
        }

        $movie = $this->films->find($movieId);

        if ($movie === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        if ($text === '') {
            return redirect()->to('/movies/view/' . $movie['slug'])
                ->with('error', 'Комментарий не может быть пустым.');
        }

        (new CommentsModel())->add($userId, $movieId, $text);

        return redirect()->to('/movies/view/' . $movie['slug'])
            ->with('msg', 'Комментарий добавлен!');
    }

    /**
     * Данные фильма из запроса.
     *
     * @return array<string, mixed>
     */
    private function movieDataFromRequest(): array
    {
        return [
            'slug'         => trim((string) $this->request->getPost('slug')),
            'name'         => trim((string) $this->request->getPost('name')),
            'descriptions' => (string) $this->request->getPost('descriptions'),
            'year'         => (string) $this->request->getPost('year'),
            'rating'       => (string) $this->request->getPost('rating'),
            'poster'       => (string) $this->request->getPost('poster'),
            'player_code'  => (string) $this->request->getPost('player_code'),
            'director'     => (string) $this->request->getPost('director'),
            'add_date'     => (string) $this->request->getPost('add_date'),
            'category_id'  => (string) $this->request->getPost('category_id'),
        ];
    }

    /**
     * Правила валидации фильма.
     *
     * @return array<string, string>
     */
    private function movieRules(?int $ignoreId = null): array
    {
        $slugRule = 'required|max_length[255]|alpha_dash|is_unique[movie.slug' . ($ignoreId !== null ? ',id,' . $ignoreId : '') . ']';

        return [
            'slug'         => $slugRule,
            'name'         => 'required|max_length[255]',
            'descriptions' => 'required',
            'year'         => 'required|integer|greater_than[1887]|less_than[2200]',
            'rating'       => 'permit_empty|decimal',
            // safe_media_url запрещает схемы вроде javascript: и data:,
            // которые иначе попали бы прямо в src плеера и постера.
            'poster'       => 'required|max_length[500]|safe_media_url',
            'player_code'  => 'permit_empty|max_length[1000]|safe_media_url',
            'director'     => 'permit_empty|max_length[255]',
            'add_date'     => 'required|valid_date',
            'category_id'  => 'required|in_list[1,2]',
        ];
    }
}
