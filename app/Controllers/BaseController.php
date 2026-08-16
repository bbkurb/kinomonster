<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\FilmsModel;
use App\Models\NewsModel;
use App\Services\Auth;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Базовый контроллер — аналог MY_Controller из CI3.
 *
 * В CI3 MY_Controller при каждом запросе безусловно дёргал БД за новостями и
 * топом фильмов для сайдбара. Здесь данные сайдбара считаются лениво, только
 * когда шаблон действительно рендерится (см. renderPage()).
 */
abstract class BaseController extends Controller
{
    /**
     * Хелперы, доступные во всех контроллерах.
     *
     * @var list<string>
     */
    protected $helpers = ['url', 'form', 'menu'];

    protected Auth $auth;

    /**
     * Данные, передаваемые во вьюхи.
     *
     * @var array<string, mixed>
     */
    protected array $data = [];

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $this->auth = service('auth');

        $this->data = [
            'title'    => 'КиноМонстр - сайт о кино',
            'category' => '',
        ];
    }

    /**
     * Рендер страницы в общем макете.
     *
     * Заменяет три подряд идущих вызова load->view(header/страница/footer),
     * которые были в каждом методе CI3-контроллеров.
     *
     * @param array<string, mixed> $data
     */
    protected function renderPage(string $view, array $data = []): string
    {
        $data = array_merge($this->data, $data);

        // Данные сайдбара нужны только при полном рендере страницы.
        $data['sidebarNews']  ??= (new NewsModel())->getAll();
        $data['sidebarFilms'] ??= (new FilmsModel())->getTopRated(10);
        $data['auth']         ??= $this->auth;

        return view('templates/header', $data)
            . view($view, $data)
            . view('templates/footer', $data);
    }
}
