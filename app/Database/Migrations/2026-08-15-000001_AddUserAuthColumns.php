<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Приводит таблицу `users` из схемы DX_Auth к схеме UserModel.
 *
 * DX_Auth хранил роль в отдельной таблице `roles` и связывал её через
 * users.role_id. Ролей в приложении фактически было две — обычный
 * пользователь и админ, — поэтому связь заменяется на колонку users.role.
 *
 * Миграция не удаляет старые колонки и таблицы: сначала убедитесь, что всё
 * работает, и только потом чистите (см. down() и комментарий в конце файла).
 */
class AddUserAuthColumns extends Migration
{
    public function up(): void
    {
        // getFieldNames() кеширует результат внутри соединения, поэтому после
        // предыдущего addColumn/dropColumn проверки видели бы устаревший
        // список колонок и миграция падала бы на «duplicate column».
        $this->resetFieldCache();

        $fields = [];

        if (! $this->db->fieldExists('role', 'users')) {
            $fields['role'] = [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'user',
                'null'       => false,
            ];
        }

        if (! $this->db->fieldExists('reset_key', 'users')) {
            $fields['reset_key'] = ['type' => 'VARCHAR', 'constraint' => 64, 'null' => true];
        }

        if (! $this->db->fieldExists('reset_expires', 'users')) {
            $fields['reset_expires'] = ['type' => 'DATETIME', 'null' => true];
        }

        if (! $this->db->fieldExists('activated', 'users')) {
            $fields['activated'] = ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1];
        }

        if (! $this->db->fieldExists('created_at', 'users')) {
            $fields['created_at'] = ['type' => 'DATETIME', 'null' => true];
        }

        if ($fields !== []) {
            $this->forge->addColumn('users', $fields);
        }

        // Переносим роли из таблицы DX_Auth, если она ещё существует.
        if ($this->db->tableExists('roles')) {
            $admins = $this->db->table('users')
                ->select('users.id')
                ->join('roles', 'roles.id = users.role_id', 'inner')
                ->whereIn('roles.name', ['admin', 'Admin', 'administrator'])
                ->get()
                ->getResultArray();

            if ($admins !== []) {
                $this->db->table('users')
                    ->whereIn('id', array_column($admins, 'id'))
                    ->update(['role' => 'admin']);
            }
        }

        // Индексы под самые частые запросы.
        // MySQL не поддерживает CREATE INDEX IF NOT EXISTS, поэтому наличие
        // индекса проверяется по метаданным таблицы.
        foreach (['idx_users_username' => 'username', 'idx_users_email' => 'email'] as $name => $column) {
            if ($this->indexExists('users', $name)) {
                continue;
            }

            $this->db->query(sprintf(
                'CREATE INDEX %s ON %s (%s)',
                $this->db->escapeIdentifier($name),
                $this->db->protectIdentifiers('users', true),
                $this->db->protectIdentifiers($column),
            ));
        }
    }

    /**
     * Существует ли индекс с таким именем.
     */
    private function indexExists(string $table, string $index): bool
    {
        foreach ($this->db->getIndexData($table) as $data) {
            if (strcasecmp($data->name, $index) === 0) {
                return true;
            }
        }

        return false;
    }

    public function down(): void
    {
        $this->resetFieldCache();

        foreach (['role', 'reset_key', 'reset_expires', 'activated', 'created_at'] as $column) {
            if ($this->db->fieldExists($column, 'users')) {
                $this->forge->dropColumn('users', $column);
                $this->resetFieldCache();
            }
        }
    }

    /**
     * Сбрасывает кеш метаданных соединения.
     *
     * Без этого fieldExists() после изменения схемы возвращает данные,
     * собранные до изменения.
     */
    private function resetFieldCache(): void
    {
        $this->db->dataCache = [];
    }
}

/*
 * После успешного перехода можно удалить наследие DX_Auth вручную:
 *
 *   DROP TABLE IF EXISTS roles;
 *   DROP TABLE IF EXISTS permissions;
 *   DROP TABLE IF EXISTS login_attempts;
 *   DROP TABLE IF EXISTS user_autologin;
 *   DROP TABLE IF EXISTS user_temp;
 *   DROP TABLE IF EXISTS user_profile;
 *   ALTER TABLE users DROP COLUMN role_id;
 *   ALTER TABLE users DROP COLUMN newpass, DROP COLUMN newpass_key, DROP COLUMN newpass_time;
 *
 * Сделайте резервную копию базы перед этим.
 */
