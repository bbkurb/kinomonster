<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

/**
 * Фильмы и сериалы (таблица `movie`).
 *
 * Перенос CI3-модели Films_model. Категории: 1 — фильмы, 2 — сериалы.
 */
class FilmsModel extends Model
{
    public const CATEGORY_FILM   = 1;
    public const CATEGORY_SERIAL = 2;

    protected $table         = 'movie';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    protected $allowedFields = [
        'slug',
        'name',
        'descriptions',
        'year',
        'rating',
        'poster',
        'player_code',
        'director',
        'add_date',
        'category_id',
    ];

    /**
     * Последние фильмы/сериалы указанной категории.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getLatest(int $limit, int $categoryId = self::CATEGORY_FILM): array
    {
        return $this->where('category_id', $categoryId)
            ->orderBy('add_date', 'DESC')
            ->findAll($limit);
    }

    /**
     * Один фильм по slug.
     *
     * @return array<string, mixed>|null
     */
    public function getBySlug(string $slug): ?array
    {
        return $this->where('slug', $slug)->first();
    }

    /**
     * Все записи категории, отсортированные по дате добавления.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getAllByCategory(int $categoryId): array
    {
        return $this->where('category_id', $categoryId)
            ->orderBy('add_date', 'DESC')
            ->findAll();
    }

    /**
     * Топ по рейтингу (только фильмы с рейтингом > 0).
     *
     * @return array<int, array<string, mixed>>
     */
    public function getTopRated(int $limit): array
    {
        return $this->where('category_id', self::CATEGORY_FILM)
            ->where('rating >', 0)
            ->orderBy('rating', 'DESC')
            ->findAll($limit);
    }

    /**
     * Страница списка по категории.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getPageByCategory(int $perPage, int $offset, int $categoryId = self::CATEGORY_FILM): array
    {
        return $this->where('category_id', $categoryId)
            ->orderBy('add_date', 'DESC')
            ->findAll($perPage, $offset);
    }

    /**
     * Страница списка по рейтингу.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getPageByRating(int $perPage, int $offset): array
    {
        return $this->where('category_id', self::CATEGORY_FILM)
            ->where('rating >', 0)
            ->orderBy('rating', 'DESC')
            ->findAll($perPage, $offset);
    }

    /**
     * Количество записей в категории — для пагинации.
     */
    public function countByCategory(int $categoryId): int
    {
        return $this->where('category_id', $categoryId)->countAllResults();
    }

    /**
     * Количество фильмов с рейтингом — для пагинации.
     */
    public function countRated(): int
    {
        return $this->where('category_id', self::CATEGORY_FILM)
            ->where('rating >', 0)
            ->countAllResults();
    }

    /**
     * Поиск по названию и описанию.
     *
     * @return array<int, array<string, mixed>>
     */
    public function search(string $query, int $perPage, int $offset): array
    {
        return $this->groupStart()
            ->like('name', $query)
            ->orLike('descriptions', $query)
            ->groupEnd()
            ->orderBy('add_date', 'DESC')
            ->findAll($perPage, $offset);
    }

    /**
     * Количество результатов поиска — для пагинации.
     */
    public function countSearch(string $query): int
    {
        return $this->groupStart()
            ->like('name', $query)
            ->orLike('descriptions', $query)
            ->groupEnd()
            ->countAllResults();
    }

    /**
     * Удаление по slug.
     */
    public function deleteBySlug(string $slug): bool
    {
        return (bool) $this->where('slug', $slug)->delete();
    }
}
