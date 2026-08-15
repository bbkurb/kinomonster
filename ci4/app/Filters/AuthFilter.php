<?php

declare(strict_types=1);

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Доступ только для авторизованных пользователей.
 *
 * В CI3 это была строка `if (!$this->dx_auth->is_logged_in()) show_404();`,
 * скопированная в каждый метод. Теперь проверка объявляется в Routes.php.
 */
class AuthFilter implements FilterInterface
{
    /**
     * @param list<string>|null $arguments
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! service('auth')->isLoggedIn()) {
            return redirect()->to('/auth/login')
                ->with('error', 'Для доступа к этой странице нужно войти.');
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
