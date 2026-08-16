<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

/**
 * Обратная связь (таблица `feedback`). Перенос CI3-модели Contact_model.
 */
class FeedbackModel extends Model
{
    protected $table         = 'feedback';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    protected $allowedFields = ['username', 'user_email', 'feedback'];

    protected $validationRules = [
        'username'   => 'required|max_length[255]',
        'user_email' => 'required|valid_email|max_length[255]',
        'feedback'   => 'required',
    ];

    protected $validationMessages = [
        'username'   => ['required' => 'Укажите ваше имя.'],
        'user_email' => [
            'required'    => 'Укажите ваш email.',
            'valid_email' => 'Email указан некорректно.',
        ],
        'feedback' => ['required' => 'Сообщение не может быть пустым.'],
    ];

    protected $skipValidation = false;

    /**
     * Сохранение сообщения обратной связи.
     */
    public function add(string $username, string $email, string $feedback): bool
    {
        return (bool) $this->insert([
            'username'   => $username,
            'user_email' => $email,
            'feedback'   => $feedback,
        ]);
    }
}
