<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Создаёт схему базы с нуля.
 *
 * Нужна для чистой установки: на новом сервере (Laragon, XAMPP, боевой хостинг)
 * таблиц ещё нет. Если вы переносите существующий сайт с CI3, таблицы уже
 * созданы — тогда эта миграция ничего не трогает и просто отмечается
 * выполненной, а нужные колонки добавит следующая миграция.
 */
class CreateBaseTables extends Migration
{
    public function up(): void
    {
        $this->createMovie();
        $this->createNews();
        $this->createPosts();
        $this->createComments();
        $this->createFeedback();
        $this->createUsers();
    }

    public function down(): void
    {
        // Таблицы намеренно не удаляются: откат не должен уничтожать контент.
        // Для полной очистки удалите базу вручную.
    }

    private function createMovie(): void
    {
        if ($this->db->tableExists('movie')) {
            return;
        }

        $this->forge->addField([
            'id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'slug'         => ['type' => 'VARCHAR', 'constraint' => 255],
            'name'         => ['type' => 'VARCHAR', 'constraint' => 255],
            'descriptions' => ['type' => 'TEXT', 'null' => true],
            'year'         => ['type' => 'INT', 'constraint' => 4, 'null' => true],
            'rating'       => ['type' => 'DECIMAL', 'constraint' => '3,1', 'default' => 0],
            'poster'       => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'player_code'  => ['type' => 'VARCHAR', 'constraint' => 1000, 'null' => true],
            'director'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'add_date'     => ['type' => 'DATE', 'null' => true],
            'category_id'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('slug');
        $this->forge->createTable('movie');
    }

    private function createNews(): void
    {
        if ($this->db->tableExists('news')) {
            return;
        }

        $this->forge->addField([
            'id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'slug'  => ['type' => 'VARCHAR', 'constraint' => 255],
            'title' => ['type' => 'VARCHAR', 'constraint' => 255],
            'text'  => ['type' => 'TEXT', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('slug');
        $this->forge->createTable('news');
    }

    private function createPosts(): void
    {
        if ($this->db->tableExists('posts')) {
            return;
        }

        $this->forge->addField([
            'id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'slug'  => ['type' => 'VARCHAR', 'constraint' => 255],
            'title' => ['type' => 'VARCHAR', 'constraint' => 255],
            'text'  => ['type' => 'TEXT', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('slug');
        $this->forge->createTable('posts');
    }

    private function createComments(): void
    {
        if ($this->db->tableExists('comments')) {
            return;
        }

        $this->forge->addField([
            'id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'movie_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'comment_text' => ['type' => 'TEXT'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('comments');
    }

    private function createFeedback(): void
    {
        if ($this->db->tableExists('feedback')) {
            return;
        }

        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'username'   => ['type' => 'VARCHAR', 'constraint' => 255],
            'user_email' => ['type' => 'VARCHAR', 'constraint' => 255],
            'feedback'   => ['type' => 'TEXT'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('feedback');
    }

    private function createUsers(): void
    {
        if ($this->db->tableExists('users')) {
            return;
        }

        $this->forge->addField([
            'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'username'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'email'          => ['type' => 'VARCHAR', 'constraint' => 255],
            'password'       => ['type' => 'VARCHAR', 'constraint' => 255],
            'role'           => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'user'],
            'banned'         => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'ban_reason'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'activated'      => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'activation_key' => ['type' => 'VARCHAR', 'constraint' => 64, 'null' => true],
            'reset_key'      => ['type' => 'VARCHAR', 'constraint' => 64, 'null' => true],
            'reset_expires'  => ['type' => 'DATETIME', 'null' => true],
            'last_ip'        => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
            'last_login'     => ['type' => 'DATETIME', 'null' => true],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('username');
        $this->forge->addUniqueKey('email');
        $this->forge->createTable('users');
    }
}
