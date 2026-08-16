<?php

declare(strict_types=1);

namespace App\Filters;

use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Доступ только для администраторов.
 *
 * Заменяет `if (!$this->dx_auth->is_admin()) show_404();` из CI3-контроллеров
 * и метод check_uri_permissions() из DX_Auth.
 *
 * Как и в оригинале, отдаём 404, а не 403 — чтобы не раскрывать существование
 * админских маршрутов.
 */
class AdminFilter implements FilterInterface
{
    /**
     * @param list<string>|null $arguments
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! service('auth')->isAdmin()) {
            throw PageNotFoundException::forPageNotFound();
        }
    }

    /**
     * @param list<string>|null $arguments
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Ничего не делаем.
    }
}
