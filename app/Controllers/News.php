<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\NewsModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * Новости. Перенос CI3-контроллера News.
 */
class News extends BaseController
{
    private NewsModel $news;

    public function initController($request, $response, $logger): void
    {
        parent::initController($request, $response, $logger);
        $this->news = new NewsModel();
    }

    /**
     * Список новостей.
     */
    public function index(): string
    {
        return $this->renderPage('news/index', [
            'title' => 'Все новости',
            'news'  => $this->news->getAll(),
        ]);
    }

    /**
     * Просмотр новости.
     */
    public function view(?string $slug = null): string
    {
        if ($slug === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $item = $this->news->getBySlug($slug);

        if ($item === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $this->renderPage('news/view', [
            'title'     => $item['title'],
            'content'   => $item['text'],
            'slug'      => $item['slug'],
            'news_item' => $item,
        ]);
    }

    /**
     * Создание новости.
     *
     * @return string|RedirectResponse
     */
    public function create()
    {
        if ($this->request->getMethod() === 'POST') {
            $data = [
                'slug'  => trim((string) $this->request->getPost('slug')),
                'title' => trim((string) $this->request->getPost('title')),
                'text'  => (string) $this->request->getPost('text'),
            ];

            if ($this->news->insert($data)) {
                return redirect()->to('/news')->with('msg', 'Новость добавлена!');
            }

            return $this->renderPage('news/create', [
                'title'      => 'Добавить новость',
                'validation' => $this->news->errors(),
            ]);
        }

        return $this->renderPage('news/create', ['title' => 'Добавить новость']);
    }

    /**
     * Редактирование новости.
     *
     * @return string|RedirectResponse
     */
    public function edit(?string $slug = null)
    {
        if ($slug === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $item = $this->news->getBySlug($slug);

        if ($item === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        if ($this->request->getMethod() === 'POST') {
            $data = [
                'slug'  => trim((string) $this->request->getPost('slug')),
                'title' => trim((string) $this->request->getPost('title')),
                'text'  => (string) $this->request->getPost('text'),
            ];

            if ($this->news->update((int) $item['id'], $data)) {
                return redirect()->to('/news/edit/' . $data['slug'])
                    ->with('msg', 'Успешно обновлено');
            }

            return $this->renderPage('news/edit', [
                'title'      => 'Редактировать новость',
                'news_item'  => $item,
                'validation' => $this->news->errors(),
            ]);
        }

        return $this->renderPage('news/edit', [
            'title'     => 'Редактировать новость',
            'news_item' => $item,
        ]);
    }

    /**
     * Удаление новости (только POST).
     */
    public function delete(?string $slug = null): RedirectResponse
    {
        if ($slug === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $item = $this->news->getBySlug($slug);

        if ($item === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        if ($this->news->deleteBySlug($slug)) {
            return redirect()->to('/news')
                ->with('msg', $item['title'] . ' успешно удалена');
        }

        return redirect()->to('/news')
            ->with('error', 'Ошибка удаления ' . $item['title']);
    }
}
