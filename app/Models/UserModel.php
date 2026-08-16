<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

/**
 * Пользователи (таблица `users`).
 *
 * Заменяет модель dx_auth/Users из CI3. Работа с паролями вынесена сюда,
 * хеширование — только password_hash()/password_verify() (в CI3 ключи
 * активации и автологина генерировались через md5(rand()), что небезопасно).
 */
class UserModel extends Model
{
    public const ROLE_USER  = 'user';
    public const ROLE_ADMIN = 'admin';

    protected $table         = 'users';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    protected $allowedFields = [
        'username',
        'email',
        'password',
        'role',
        'banned',
        'ban_reason',
        'activated',
        'activation_key',
        'reset_key',
        'reset_expires',
        'last_ip',
        'last_login',
        'created_at',
    ];

    /**
     * Поиск по логину.
     *
     * @return array<string, mixed>|null
     */
    public function findByUsername(string $username): ?array
    {
        return $this->where('username', $username)->first();
    }

    /**
     * Поиск по email.
     *
     * @return array<string, mixed>|null
     */
    public function findByEmail(string $email): ?array
    {
        return $this->where('email', $email)->first();
    }

    /**
     * Поиск по логину или email — для формы восстановления пароля.
     *
     * @return array<string, mixed>|null
     */
    public function findByLogin(string $login): ?array
    {
        return $this->groupStart()
            ->where('username', $login)
            ->orWhere('email', $login)
            ->groupEnd()
            ->first();
    }

    /**
     * Свободен ли логин.
     */
    public function isUsernameAvailable(string $username): bool
    {
        return $this->where('username', $username)->countAllResults() === 0;
    }

    /**
     * Свободен ли email.
     */
    public function isEmailAvailable(string $email): bool
    {
        return $this->where('email', $email)->countAllResults() === 0;
    }

    /**
     * Создание пользователя. Пароль хешируется здесь, наружу не утекает.
     *
     * @return int|false ID нового пользователя
     */
    public function createUser(string $username, string $email, string $password, string $role = self::ROLE_USER)
    {
        $id = $this->insert([
            'username'   => $username,
            'email'      => $email,
            'password'   => password_hash($password, PASSWORD_DEFAULT),
            'role'       => $role,
            'banned'     => 0,
            'activated'  => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ], true);

        return $id === false ? false : (int) $id;
    }

    /**
     * Проверка пароля с автоматическим перехешированием устаревших хешей.
     *
     * @param array<string, mixed> $user
     */
    public function verifyPassword(array $user, string $password): bool
    {
        $hash = (string) ($user['password'] ?? '');

        if ($hash === '' || ! password_verify($password, $hash)) {
            return false;
        }

        // Хеш из старой БД (bcrypt) молча обновляется до текущего алгоритма.
        if (password_needs_rehash($hash, PASSWORD_DEFAULT)) {
            $this->update($user['id'], [
                'password' => password_hash($password, PASSWORD_DEFAULT),
            ]);
        }

        return true;
    }

    /**
     * Смена пароля.
     */
    public function changePassword(int $userId, string $newPassword): bool
    {
        return (bool) $this->update($userId, [
            'password'      => password_hash($newPassword, PASSWORD_DEFAULT),
            'reset_key'     => null,
            'reset_expires' => null,
        ]);
    }

    /**
     * Генерация криптостойкого ключа сброса пароля (срок жизни — 1 час).
     */
    public function createResetKey(int $userId): string
    {
        $key = bin2hex(random_bytes(32));

        $this->update($userId, [
            'reset_key'     => hash('sha256', $key),
            'reset_expires' => date('Y-m-d H:i:s', time() + 3600),
        ]);

        return $key;
    }

    /**
     * Поиск пользователя по действующему ключу сброса.
     *
     * @return array<string, mixed>|null
     */
    public function findByResetKey(string $username, string $key): ?array
    {
        $user = $this->findByUsername($username);

        if ($user === null || empty($user['reset_key']) || empty($user['reset_expires'])) {
            return null;
        }

        if (strtotime((string) $user['reset_expires']) < time()) {
            return null;
        }

        // hash_equals — защита от тайминг-атак.
        return hash_equals((string) $user['reset_key'], hash('sha256', $key)) ? $user : null;
    }

    /**
     * Отметка об успешном входе.
     */
    public function touchLogin(int $userId, string $ip): void
    {
        $this->update($userId, [
            'last_ip'    => $ip,
            'last_login' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Страница списка пользователей для админки.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getPage(int $perPage, int $offset): array
    {
        return $this->orderBy('id', 'ASC')->findAll($perPage, $offset);
    }

    /**
     * Бан / разбан.
     */
    public function setBanned(int $userId, bool $banned, ?string $reason = null): bool
    {
        return (bool) $this->update($userId, [
            'banned'     => $banned ? 1 : 0,
            'ban_reason' => $banned ? $reason : null,
        ]);
    }
}
