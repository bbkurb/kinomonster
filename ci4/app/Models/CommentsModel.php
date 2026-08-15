<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

/**
 * Комментарии к фильмам (таблица `comments`).
 *
 * Перенос CI3-модели Comments_model + метод setComments из Films_model,
 * которому здесь логически самое место.
 */
class CommentsModel extends Model
{
    protected $table         = 'comments';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    protected $allowedFields = ['user_id', 'movie_id', 'comment_text'];

    protected $validationRules = [
        'user_id'      => 'required|is_natural_no_zero',
        'movie_id'     => 'required|is_natural_no_zero',
        'comment_text' => 'required|max_length[5000]',
    ];

    protected $validationMessages = [
        'comment_text' => [
            'required'   => 'Комментарий не может быть пустым.',
            'max_length' => 'Комментарий слишком длинный.',
        ],
    ];

    protected $skipValidation = false;

    /**
     * Комментарии к фильму вместе с именем автора.
     *
     * Раньше имя пользователя доставалось в шаблоне вызовом getUserNameByID()
     * на каждый комментарий — классическая проблема N+1. Теперь это один JOIN.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getForMovie(int $movieId, int $limit = 100): array
    {
        return $this->select('comments.*, users.username')
            ->join('users', 'users.id = comments.user_id', 'left')
            ->where('comments.movie_id', $movieId)
            ->orderBy('comments.id', 'ASC')
            ->findAll($limit);
    }

    /**
     * Добавление комментария.
     */
    public function add(int $userId, int $movieId, string $text): bool
    {
        return (bool) $this->insert([
            'user_id'      => $userId,
            'movie_id'     => $movieId,
            'comment_text' => $text,
        ]);
    }
}
