<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\PostsModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * Посты. Перенос CI3-контроллера Posts.
 */
class Posts extends BaseController
{
    private PostsModel $posts;

    public function initController($request, $response, $logger): void
    {
        parent::initController($request, $response, $logger);
        $this->posts = new PostsModel();
    }

    /**
     * Список постов.
     */
    public function index(): string
    {
        return $this->renderPage('posts/index', [
            'title' => 'Все посты',
            'posts' => $this->posts->getAll(),
        ]);
    }

    /**
     * Просмотр поста.
     */
    public function view(?string $slug = null): string
    {
        if ($slug === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $item = $this->posts->getBySlug($slug);

        if ($item === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $this->renderPage('posts/view', [
            'title'      => $item['title'],
            'content'    => $item['text'],
            'slug'       => $item['slug'],
            'posts_item' => $item,
        ]);
    }

    /**
     * Создание поста.
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

            if ($this->posts->insert($data)) {
                return redirect()->to('/posts')->with('msg', 'Пост добавлен!');
            }

            return $this->renderPage('posts/create', [
                'title'      => 'Добавить пост',
                'validation' => $this->posts->errors(),
            ]);
        }

        return $this->renderPage('posts/create', ['title' => 'Добавить пост']);
    }

    /**
     * Редактирование поста.
     *
     * @return string|RedirectResponse
     */
    public function edit(?string $slug = null)
    {
        if ($slug === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $item = $this->posts->getBySlug($slug);

        if ($item === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        if ($this->request->getMethod() === 'POST') {
            $data = [
                'slug'  => trim((string) $this->request->getPost('slug')),
                'title' => trim((string) $this->request->getPost('title')),
                'text'  => (string) $this->request->getPost('text'),
            ];

            if ($this->posts->update((int) $item['id'], $data)) {
                return redirect()->to('/posts/edit/' . $data['slug'])
                    ->with('msg', 'Успешно обновлено');
            }

            return $this->renderPage('posts/edit', [
                'title'      => 'Редактировать пост',
                'posts_item' => $item,
                'validation' => $this->posts->errors(),
            ]);
        }

        return $this->renderPage('posts/edit', [
            'title'      => 'Редактировать пост',
            'posts_item' => $item,
        ]);
    }

    /**
     * Удаление поста (только POST).
     */
    public function delete(?string $slug = null): RedirectResponse
    {
        if ($slug === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $item = $this->posts->getBySlug($slug);

        if ($item === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        if ($this->posts->deleteBySlug($slug)) {
            return redirect()->to('/posts')
                ->with('msg', $item['title'] . ' успешно удалён');
        }

        return redirect()->to('/posts')
            ->with('error', 'Ошибка удаления ' . $item['title']);
    }
}
