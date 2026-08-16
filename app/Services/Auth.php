<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\UserModel;
use CodeIgniter\Session\SessionInterface;

/**
 * Сервис аутентификации — замена библиотеки DX_Auth из CI3.
 *
 * DX_Auth (2365 строк) заброшен и версии под CI4 не имеет. Здесь оставлено
 * только то, что реально использовалось приложением: вход, выход, регистрация,
 * восстановление пароля, роли и защита от перебора.
 *
 * Отличия от CI3-оригинала:
 *  - ключи генерируются random_bytes(), а не md5(rand().microtime());
 *  - в сессии хранится только id/username/role, а не весь профиль;
 *  - при входе session_regenerate — защита от session fixation;
 *  - роль admin определяется полем users.role, а не отдельной таблицей ролей.
 */
class Auth
{
    private const SESSION_KEY   = 'auth_user';
    private const MAX_ATTEMPTS  = 5;
    private const LOCKOUT_SECS  = 900;

    private UserModel $users;
    private SessionInterface $session;

    public function __construct(?UserModel $users = null, ?SessionInterface $session = null)
    {
        $this->users   = $users ?? new UserModel();
        $this->session = $session ?? session();
    }

    /**
     * Попытка входа.
     *
     * @return array{success: bool, error?: string}
     */
    public function attempt(string $username, string $password, bool $remember = false): array
    {
        if ($this->isThrottled()) {
            return [
                'success' => false,
                'error'   => 'Слишком много попыток входа. Попробуйте через 15 минут.',
            ];
        }

        $user = $this->users->findByUsername($username);

        // Одинаковый ответ на «нет пользователя» и «неверный пароль»,
        // чтобы нельзя было перебором узнать существующие логины.
        if ($user === null || ! $this->users->verifyPassword($user, $password)) {
            $this->registerFailedAttempt();

            return ['success' => false, 'error' => 'Неверный логин или пароль.'];
        }

        if ((int) ($user['banned'] ?? 0) === 1) {
            return [
                'success' => false,
                'error'   => 'Ваш аккаунт заблокирован. ' . (string) ($user['ban_reason'] ?? ''),
            ];
        }

        if ((int) ($user['activated'] ?? 1) === 0) {
            return ['success' => false, 'error' => 'Аккаунт не активирован.'];
        }

        $this->login($user, $remember);

        return ['success' => true];
    }

    /**
     * Установка сессии авторизованного пользователя.
     *
     * @param array<string, mixed> $user
     */
    public function login(array $user, bool $remember = false): void
    {
        $this->session->regenerate(true);
        $this->clearAttempts();

        $this->session->set(self::SESSION_KEY, [
            'id'       => (int) $user['id'],
            'username' => (string) $user['username'],
            'role'     => (string) ($user['role'] ?? UserModel::ROLE_USER),
        ]);

        $request = service('request');
        $this->users->touchLogin((int) $user['id'], (string) $request->getIPAddress());

        if ($remember) {
            $this->session->set('auth_remember', true);
        }
    }

    /**
     * Выход.
     */
    public function logout(): void
    {
        $this->session->remove([self::SESSION_KEY, 'auth_remember']);
        $this->session->destroy();
    }

    /**
     * Регистрация нового пользователя.
     *
     * @return array{success: bool, error?: string, user_id?: int}
     */
    public function register(string $username, string $email, string $password): array
    {
        if (! $this->users->isUsernameAvailable($username)) {
            return ['success' => false, 'error' => 'Логин занят. Выберите другой.'];
        }

        if (! $this->users->isEmailAvailable($email)) {
            return ['success' => false, 'error' => 'Email занят. Выберите другой.'];
        }

        $userId = $this->users->createUser($username, $email, $password);

        if ($userId === false) {
            return ['success' => false, 'error' => 'Не удалось создать аккаунт. Попробуйте позже.'];
        }

        return ['success' => true, 'user_id' => $userId];
    }

    /**
     * Авторизован ли пользователь.
     */
    public function isLoggedIn(): bool
    {
        return $this->session->has(self::SESSION_KEY);
    }

    /**
     * Является ли администратором.
     */
    public function isAdmin(): bool
    {
        return $this->role() === UserModel::ROLE_ADMIN;
    }

    /**
     * ID текущего пользователя.
     */
    public function id(): ?int
    {
        $user = $this->session->get(self::SESSION_KEY);

        return is_array($user) ? (int) $user['id'] : null;
    }

    /**
     * Логин текущего пользователя.
     */
    public function username(): ?string
    {
        $user = $this->session->get(self::SESSION_KEY);

        return is_array($user) ? (string) $user['username'] : null;
    }

    /**
     * Роль текущего пользователя.
     */
    public function role(): ?string
    {
        $user = $this->session->get(self::SESSION_KEY);

        return is_array($user) ? (string) $user['role'] : null;
    }

    /**
     * Запуск процедуры восстановления пароля.
     *
     * @return array{success: bool, error?: string, key?: string, user?: array<string, mixed>}
     */
    public function forgotPassword(string $login): array
    {
        $user = $this->users->findByLogin($login);

        if ($user === null) {
            // Не подтверждаем существование аккаунта — ответ контроллера
            // одинаков в обоих случаях.
            return ['success' => false, 'error' => 'not_found'];
        }

        $key = $this->users->createResetKey((int) $user['id']);

        return ['success' => true, 'key' => $key, 'user' => $user];
    }

    /**
     * Сброс пароля по ключу из письма.
     */
    public function resetPassword(string $username, string $key, string $newPassword): bool
    {
        $user = $this->users->findByResetKey($username, $key);

        if ($user === null) {
            return false;
        }

        return $this->users->changePassword((int) $user['id'], $newPassword);
    }

    /**
     * Смена пароля текущим пользователем.
     */
    public function changePassword(string $oldPassword, string $newPassword): bool
    {
        $userId = $this->id();

        if ($userId === null) {
            return false;
        }

        $user = $this->users->find($userId);

        if ($user === null || ! $this->users->verifyPassword($user, $oldPassword)) {
            return false;
        }

        return $this->users->changePassword($userId, $newPassword);
    }

    /* ------------------------------------------------------------------ */
    /* Защита от перебора паролей                                          */
    /* ------------------------------------------------------------------ */

    private function isThrottled(): bool
    {
        $attempts = $this->session->get('login_attempts');

        if (! is_array($attempts)) {
            return false;
        }

        if (($attempts['count'] ?? 0) < self::MAX_ATTEMPTS) {
            return false;
        }

        if (time() - ($attempts['first'] ?? 0) > self::LOCKOUT_SECS) {
            $this->clearAttempts();

            return false;
        }

        return true;
    }

    private function registerFailedAttempt(): void
    {
        $attempts = $this->session->get('login_attempts');

        if (! is_array($attempts) || time() - ($attempts['first'] ?? 0) > self::LOCKOUT_SECS) {
            $attempts = ['count' => 0, 'first' => time()];
        }

        $attempts['count']++;
        $this->session->set('login_attempts', $attempts);
    }

    private function clearAttempts(): void
    {
        $this->session->remove('login_attempts');
    }
}
