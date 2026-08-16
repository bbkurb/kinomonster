<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Индексы под запросы, которые приложение делает на каждой странице.
 *
 * В CI3-версии индексов не было: выборка «последние фильмы категории»
 * и поиск фильма по slug приводили к полному сканированию таблицы movie.
 */
class AddContentIndexes extends Migration
{
    /**
     * Индексы, которые нужно создать: имя => [таблица, колонки].
     *
     * @var array<string, array{0: string, 1: string}>
     */
    private array $indexes = [
        // Главная и списки: WHERE category_id = ? ORDER BY add_date DESC
        'idx_movie_cat_date' => ['movie', 'category_id, add_date'],
        // Рейтинг: WHERE category_id = 1 AND rating > 0 ORDER BY rating DESC
        'idx_movie_cat_rating' => ['movie', 'category_id, rating'],
        // Страницы по slug
        'idx_movie_slug' => ['movie', 'slug'],
        'idx_news_slug'  => ['news', 'slug'],
        'idx_posts_slug' => ['posts', 'slug'],
        // Комментарии к фильму: WHERE movie_id = ?
        'idx_comments_movie' => ['comments', 'movie_id'],
    ];

    public function up(): void
    {
        foreach ($this->indexes as $name => [$table, $columns]) {
            if (! $this->db->tableExists($table) || $this->indexExists($table, $name)) {
                continue;
            }

            $this->db->query(sprintf(
                'CREATE INDEX %s ON %s (%s)',
                $this->db->escapeIdentifier($name),
                $this->db->protectIdentifiers($table, true),
                $columns,
            ));
        }
    }

    public function down(): void
    {
        foreach ($this->indexes as $name => [$table, $columns]) {
            if (! $this->db->tableExists($table) || ! $this->indexExists($table, $name)) {
                continue;
            }

            // Синтаксис DROP INDEX различается: MySQL требует указания таблицы.
            if ($this->db->DBDriver === 'MySQLi') {
                $this->db->query(sprintf(
                    'DROP INDEX %s ON %s',
                    $this->db->escapeIdentifier($name),
                    $this->db->protectIdentifiers($table, true),
                ));
            } else {
                $this->db->query('DROP INDEX ' . $this->db->escapeIdentifier($name));
            }
        }
    }

    /**
     * Существует ли индекс. MySQL не поддерживает CREATE INDEX IF NOT EXISTS,
     * поэтому наличие проверяется через метаданные.
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
}
