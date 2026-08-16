<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\FeedbackModel;
use App\Models\FilmsModel;
use App\Models\PostsModel;

/**
 * Главная страница, рейтинг и контакты. Перенос CI3-контроллера Main.
 */
class Main extends BaseController
{
    /**
     * Главная страница.
     */
    public function index(): string
    {
        $films = new FilmsModel();
        $posts = new PostsModel();

        return $this->renderPage('main/index', [
            'title'   => 'Главная страница',
            'movie'   => $films->getLatest(8, FilmsModel::CATEGORY_FILM),
            'serials' => $films->getLatest(8, FilmsModel::CATEGORY_SERIAL),
            'posts'   => $posts->getAll(),
        ]);
    }

    /**
     * Рейтинг фильмов с пагинацией.
     *
     * В CI3 здесь было 30 строк ручной настройки Bootstrap-разметки пагинации,
     * продублированных в четырёх контроллерах. В CI4 разметка вынесена в
     * шаблон пагинации (app/Views/pager/bootstrap.php).
     */
    public function rating(): string
    {
        $films   = new FilmsModel();
        $perPage = 5;
        $page    = max(1, (int) ($this->request->getGet('page') ?? 1));
        $offset  = ($page - 1) * $perPage;

        $total = $films->countRated();

        return $this->renderPage('main/rating', [
            'title'      => 'Рейтинг фильмов',
            'movie'      => $films->getPageByRating($perPage, $offset),
            'pagination' => service('pager')->makeLinks($page, $perPage, $total, 'bootstrap'),
        ]);
    }

    /**
     * Форма обратной связи.
     *
     * @return string|\CodeIgniter\HTTP\RedirectResponse
     */
    public function contact()
    {
        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'name'    => 'required|trim|max_length[255]',
                'email'   => 'required|trim|valid_email|max_length[255]',
                'subject' => 'required|trim|max_length[255]',
                'message' => 'required|trim',
            ];

            if (! $this->validate($rules)) {
                return $this->renderPage('main/contact', [
                    'title'      => 'Контакты',
                    'validation' => $this->validator,
                ]);
            }

            $name    = (string) $this->request->getPost('name');
            $email   = (string) $this->request->getPost('email');
            $subject = (string) $this->request->getPost('subject');
            $message = (string) $this->request->getPost('message');

            // Сообщение сохраняется в БД в любом случае: в CI3-версии при сбое
            // почты отзыв просто терялся.
            (new FeedbackModel())->add($name, $email, $message);

            $mail = service('email');
            $mail->setFrom((string) env('email.fromEmail', 'noreply@kinomonster.local'), 'КиноМонстр');
            $mail->setReplyTo($email, $name);
            $mail->setTo((string) env('email.adminEmail', 'info@wh-db.com'));
            $mail->setSubject($subject);
            $mail->setMessage($message);

            if ($mail->send(false)) {
                return redirect()->to('/contact')
                    ->with('msg', '<div class="alert alert-success text-center">Ваше сообщение успешно отправлено!</div>');
            }

            log_message('error', 'Не удалось отправить письмо обратной связи: {err}', [
                'err' => $mail->printDebugger(['headers']),
            ]);

            return redirect()->to('/contact')
                ->with('msg', '<div class="alert alert-warning text-center">Сообщение сохранено, но письмо отправить не удалось.</div>');
        }

        return $this->renderPage('main/contact', ['title' => 'Контакты']);
    }
}
