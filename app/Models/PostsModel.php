<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

/**
 * Посты/статьи (таблица `posts`). Перенос CI3-модели Posts_model.
 */
class PostsModel extends Model
{
    protected $table         = 'posts';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    protected $allowedFields = ['slug', 'title', 'text'];

    protected $validationRules = [
        'slug'  => 'required|max_length[255]|alpha_dash',
        'title' => 'required|max_length[255]',
        'text'  => 'required',
    ];

    protected $validationMessages = [
        'slug' => [
            'required'   => 'Укажите slug поста.',
            'alpha_dash' => 'Slug может содержать только латиницу, цифры, дефис и подчёркивание.',
        ],
        'title' => ['required' => 'Укажите заголовок поста.'],
        'text'  => ['required' => 'Текст поста не может быть пустым.'],
    ];

    protected $skipValidation = false;

    /**
     * Все посты.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getAll(): array
    {
        return $this->orderBy('id', 'DESC')->findAll();
    }

    /**
     * Один пост по slug.
     *
     * @return array<string, mixed>|null
     */
    public function getBySlug(string $slug): ?array
    {
        return $this->where('slug', $slug)->first();
    }

    /**
     * Удаление по slug.
     */
    public function deleteBySlug(string $slug): bool
    {
        return (bool) $this->where('slug', $slug)->delete();
    }
}
