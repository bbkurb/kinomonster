<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * Админка: управление пользователями. Перенос CI3-контроллера Backend.
 *
 * Оригинал перебирал весь массив $_POST в поисках ключей `checkbox_*` и
 * выполнял действие внутри цикла. Здесь чекбоксы передаются нормальным
 * массивом `users[]`, а действие определяется одним полем.
 *
 * Разделы roles / uri_permissions / custom_permissions из DX_Auth убраны:
 * ролевая модель свелась к полю users.role (user/admin), а права на маршруты
 * задаются фильтрами в Routes.php.
 */
class Backend extends BaseController
{
    private UserModel $users;

    public function initController($request, $response, $logger): void
    {
        parent::initController($request, $response, $logger);
        $this->users = new UserModel();
    }

    /**
     * @return string|RedirectResponse
     */
    public function index()
    {
        return $this->users();
    }

    /**
     * Список пользователей и массовые действия.
     *
     * @return string|RedirectResponse
     */
    public function users()
    {
        if ($this->request->getMethod() === 'POST') {
            return $this->handleUsersAction();
        }

        $perPage = 10;
        $page    = max(1, (int) ($this->request->getGet('page') ?? 1));
        $offset  = ($page - 1) * $perPage;
        $total   = $this->users->countAllResults();

        return $this->renderPage('backend/users', [
            'title'      => 'Пользователи',
            'users'      => $this->users->getPage($perPage, $offset),
            'pagination' => service('pager')->makeLinks($page, $perPage, $total, 'bootstrap'),
        ]);
    }

    /**
     * Обработка bulk-действий над пользователями.
     */
    private function handleUsersAction(): RedirectResponse
    {
        /** @var list<string> $ids */
        $ids    = (array) ($this->request->getPost('users') ?? []);
        $action = (string) $this->request->getPost('action');

        $ids = array_values(array_filter(array_map('intval', $ids)));

        if ($ids === []) {
            return redirect()->to('/backend/users')
                ->with('error', 'Не выбран ни один пользователь.');
        }

        $currentId = $this->auth->id();

        foreach ($ids as $id) {
            // Не даём администратору забанить самого себя.
            if ($id === $currentId) {
                continue;
            }

            match ($action) {
                'ban'   => $this->users->setBanned($id, true, (string) $this->request->getPost('ban_reason')),
                'unban' => $this->users->setBanned($id, false),
                default => null,
            };
        }

        return redirect()->to('/backend/users')->with('msg', 'Готово.');
    }
}
