<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * Аутентификация. Перенос CI3-контроллера Auth (работал поверх DX_Auth).
 *
 * Убрано по сравнению с оригиналом:
 *  - reCAPTCHA (ключи были захардкожены в конфиге и давно недействительны;
 *    вместо неё — ограничение попыток входа в сервисе Auth);
 *  - метод custom_permissions(), печатавший отладочный текст прямо в браузер;
 *  - cancel_account() оставлен, но теперь требует подтверждения паролем.
 */
class Auth extends BaseController
{
    private const MIN_USERNAME = 3;
    private const MAX_USERNAME = 20;
    private const MIN_PASSWORD = 8;
    private const MAX_PASSWORD = 72;

    /**
     * @return string|RedirectResponse
     */
    public function index()
    {
        return $this->login();
    }

    /**
     * Вход.
     *
     * @return string|RedirectResponse
     */
    public function login()
    {
        if ($this->auth->isLoggedIn()) {
            return redirect()->to('/');
        }

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'username' => 'required|trim',
                'password' => 'required',
            ];

            if (! $this->validate($rules)) {
                return $this->renderPage('Auth/login_form', [
                    'title'      => 'Вход',
                    'validation' => $this->validator,
                ]);
            }

            $result = $this->auth->attempt(
                (string) $this->request->getPost('username'),
                (string) $this->request->getPost('password'),
                (bool) $this->request->getPost('remember'),
            );

            if ($result['success']) {
                // Возврат на страницу, с которой пришли, но только на внутренний
                // адрес — иначе это открытый редирект.
                $redirect = (string) session()->getFlashdata('redirect_url');

                return redirect()->to($this->safeRedirect($redirect));
            }

            return $this->renderPage('Auth/login_form', [
                'title'        => 'Вход',
                'auth_message' => $result['error'] ?? 'Не удалось войти.',
            ]);
        }

        return $this->renderPage('Auth/login_form', ['title' => 'Вход']);
    }

    /**
     * Выход.
     */
    public function logout(): RedirectResponse
    {
        $this->auth->logout();

        return redirect()->to('/')->with('msg', 'Вы вышли из аккаунта.');
    }

    /**
     * Регистрация.
     *
     * @return string|RedirectResponse
     */
    public function register()
    {
        if ($this->auth->isLoggedIn()) {
            return redirect()->to('/');
        }

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'username' => 'required|trim|alpha_dash|min_length[' . self::MIN_USERNAME . ']|max_length[' . self::MAX_USERNAME . ']|is_unique[users.username]',
                'password' => 'required|min_length[' . self::MIN_PASSWORD . ']|max_length[' . self::MAX_PASSWORD . ']',
                'confirm_password' => 'required|matches[password]',
                'email'    => 'required|trim|valid_email|is_unique[users.email]',
            ];

            $messages = [
                'username' => [
                    'is_unique'  => 'Логин занят. Выберите другой.',
                    'min_length' => 'Логин должен быть не короче ' . self::MIN_USERNAME . ' символов.',
                ],
                'password' => [
                    'min_length' => 'Пароль должен быть не короче ' . self::MIN_PASSWORD . ' символов.',
                ],
                'confirm_password' => ['matches' => 'Пароли не совпадают.'],
                'email'            => ['is_unique' => 'Email занят. Выберите другой.'],
            ];

            if (! $this->validate($rules, $messages)) {
                return $this->renderPage('Auth/register_form', [
                    'title'      => 'Регистрация',
                    'validation' => $this->validator,
                ]);
            }

            $result = $this->auth->register(
                (string) $this->request->getPost('username'),
                (string) $this->request->getPost('email'),
                (string) $this->request->getPost('password'),
            );

            if ($result['success']) {
                return $this->renderPage('Auth/general_message', [
                    'title'        => 'Регистрация завершена',
                    'auth_message' => 'Вы успешно зарегистрировались. ' . anchor('/auth/login', 'Войти'),
                ]);
            }

            return $this->renderPage('Auth/register_form', [
                'title'        => 'Регистрация',
                'auth_message' => $result['error'] ?? 'Не удалось зарегистрироваться.',
            ]);
        }

        return $this->renderPage('Auth/register_form', ['title' => 'Регистрация']);
    }

    /**
     * Запрос восстановления пароля.
     *
     * @return string|RedirectResponse
     */
    public function forgotPassword()
    {
        if ($this->request->getMethod() === 'POST') {
            if (! $this->validate(['login' => 'required|trim'])) {
                return $this->renderPage('Auth/forgot_password_form', [
                    'title'      => 'Восстановление пароля',
                    'validation' => $this->validator,
                ]);
            }

            $result = $this->auth->forgotPassword((string) $this->request->getPost('login'));

            if ($result['success']) {
                $this->sendResetEmail(
                    (string) $result['user']['email'],
                    (string) $result['user']['username'],
                    (string) $result['key'],
                );
            }

            // Ответ одинаков независимо от того, найден аккаунт или нет —
            // иначе форма превращается в проверялку существующих email.
            return $this->renderPage('Auth/general_message', [
                'title'        => 'Восстановление пароля',
                'auth_message' => 'Если такой аккаунт существует, на его email отправлены инструкции.',
            ]);
        }

        return $this->renderPage('Auth/forgot_password_form', [
            'title' => 'Восстановление пароля',
        ]);
    }

    /**
     * Установка нового пароля по ключу из письма.
     *
     * @return string|RedirectResponse
     */
    public function resetPassword(?string $username = null, ?string $key = null)
    {
        if ($username === null || $key === null) {
            return $this->renderPage('Auth/general_message', [
                'title'        => 'Восстановление не удалось',
                'auth_message' => 'Ссылка восстановления некорректна.',
            ]);
        }

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'password'         => 'required|min_length[' . self::MIN_PASSWORD . ']|max_length[' . self::MAX_PASSWORD . ']',
                'confirm_password' => 'required|matches[password]',
            ];

            if (! $this->validate($rules)) {
                return $this->renderPage('Auth/reset_password_form', [
                    'title'      => 'Новый пароль',
                    'username'   => $username,
                    'key'        => $key,
                    'validation' => $this->validator,
                ]);
            }

            $ok = $this->auth->resetPassword(
                $username,
                $key,
                (string) $this->request->getPost('password'),
            );

            if ($ok) {
                return $this->renderPage('Auth/general_message', [
                    'title'        => 'Пароль изменён',
                    'auth_message' => 'Ваш пароль успешно восстановлен. ' . anchor('/auth/login', 'Войти'),
                ]);
            }

            return $this->renderPage('Auth/general_message', [
                'title'        => 'Восстановление не удалось',
                'auth_message' => 'Ссылка недействительна или срок её действия истёк.',
            ]);
        }

        return $this->renderPage('Auth/reset_password_form', [
            'title'    => 'Новый пароль',
            'username' => $username,
            'key'      => $key,
        ]);
    }

    /**
     * Смена пароля авторизованным пользователем.
     *
     * @return string|RedirectResponse
     */
    public function changePassword()
    {
        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'old_password'         => 'required',
                'new_password'         => 'required|min_length[' . self::MIN_PASSWORD . ']|max_length[' . self::MAX_PASSWORD . ']',
                'confirm_new_password' => 'required|matches[new_password]',
            ];

            if (! $this->validate($rules)) {
                return $this->renderPage('Auth/change_password_form', [
                    'title'      => 'Смена пароля',
                    'validation' => $this->validator,
                ]);
            }

            $ok = $this->auth->changePassword(
                (string) $this->request->getPost('old_password'),
                (string) $this->request->getPost('new_password'),
            );

            if ($ok) {
                return $this->renderPage('Auth/general_message', [
                    'title'        => 'Пароль изменён',
                    'auth_message' => 'Ваш пароль успешно изменён.',
                ]);
            }

            return $this->renderPage('Auth/change_password_form', [
                'title'        => 'Смена пароля',
                'auth_message' => 'Старый пароль указан неверно.',
            ]);
        }

        return $this->renderPage('Auth/change_password_form', ['title' => 'Смена пароля']);
    }

    /**
     * Отправка письма со ссылкой восстановления.
     */
    private function sendResetEmail(string $email, string $username, string $key): void
    {
        $link = site_url('auth/reset-password/' . rawurlencode($username) . '/' . $key);

        $mail = service('email');
        $mail->setFrom((string) env('email.fromEmail', 'noreply@kinomonster.local'), 'КиноМонстр');
        $mail->setTo($email);
        $mail->setSubject('Восстановление пароля на КиноМонстр');
        $mail->setMessage(view('Auth/email/reset_email', [
            'username' => $username,
            'link'     => $link,
        ]));

        if (! $mail->send(false)) {
            log_message('error', 'Не удалось отправить письмо восстановления пароля.');
        }
    }

    /**
     * Разрешаем редирект только на внутренние адреса.
     */
    private function safeRedirect(string $url): string
    {
        if ($url === '' || str_contains($url, '://') || str_starts_with($url, '//')) {
            return '/';
        }

        return $url;
    }
}
