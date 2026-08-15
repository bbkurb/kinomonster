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
    public function up(): void
    {
        // Главная и списки: WHERE category_id = ? ORDER BY add_date DESC
        $this->db->query('CREATE INDEX IF NOT EXISTS idx_movie_cat_date ON movie (category_id, add_date)');

        // Рейтинг: WHERE category_id = 1 AND rating > 0 ORDER BY rating DESC
        $this->db->query('CREATE INDEX IF NOT EXISTS idx_movie_cat_rating ON movie (category_id, rating)');

        // Страница фильма: WHERE slug = ?
        $this->db->query('CREATE INDEX IF NOT EXISTS idx_movie_slug ON movie (slug)');
        $this->db->query('CREATE INDEX IF NOT EXISTS idx_news_slug ON news (slug)');
        $this->db->query('CREATE INDEX IF NOT EXISTS idx_posts_slug ON posts (slug)');

        // Комментарии к фильму: WHERE movie_id = ?
        $this->db->query('CREATE INDEX IF NOT EXISTS idx_comments_movie ON comments (movie_id)');
    }

    public function down(): void
    {
        foreach ([
            'idx_movie_cat_date',
            'idx_movie_cat_rating',
            'idx_movie_slug',
            'idx_news_slug',
            'idx_posts_slug',
            'idx_comments_movie',
        ] as $index) {
            $this->db->query('DROP INDEX IF EXISTS ' . $index);
        }
    }
}
