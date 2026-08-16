<?php

use CodeIgniter\Boot;
use Config\Paths;

/*
 *---------------------------------------------------------------
 * CHECK PHP VERSION
 *---------------------------------------------------------------
 */

$minPhpVersion = '8.2'; // If you update this, don't forget to update `spark`.
if (version_compare(PHP_VERSION, $minPhpVersion, '<')) {
    $message = sprintf(
        'Your PHP version must be %s or higher to run CodeIgniter. Current version: %s',
        $minPhpVersion,
        PHP_VERSION,
    );

    header('HTTP/1.1 503 Service Unavailable.', true, 503);
    echo $message;

    exit(1);
}

/*
 *---------------------------------------------------------------
 * ОКРУЖЕНИЕ ПО УМОЛЧАНИЮ
 *---------------------------------------------------------------
 * Без файла .env CodeIgniter считает окружение боевым (production) и при
 * любой ошибке показывает пустую страницу без объяснений. Для локального
 * запуска это неудобно, поэтому по умолчанию включаем development —
 * тогда причина ошибки видна прямо в браузере.
 *
 * На боевом сервере создайте .env со строкой:
 *   CI_ENVIRONMENT = production
 * Значение из .env имеет приоритет над этой строкой.
 */
if (! isset($_SERVER['CI_ENVIRONMENT']) && ! isset($_ENV['CI_ENVIRONMENT']) && getenv('CI_ENVIRONMENT') === false) {
    $_SERVER['CI_ENVIRONMENT'] = 'development';
}

/*
 *---------------------------------------------------------------
 * АДРЕС САЙТА ПО УМОЛЧАНИЮ
 *---------------------------------------------------------------
 * Если адрес не задан явно в .env, подставляем текущий — тот, по которому
 * открыта страница. Благодаря этому проект одинаково работает и как
 * http://kinomonster.test, и как http://localhost/kinomonster/public,
 * без правки конфигов.
 *
 * Значение app.baseURL из .env всегда имеет приоритет.
 */
if (getenv('app.baseURL') === false && ! isset($_ENV['app.baseURL']) && ! isset($_SERVER['app.baseURL'])
    && isset($_SERVER['HTTP_HOST'], $_SERVER['SCRIPT_NAME'])) {
    $isHttps = (! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https')
        || ((int) ($_SERVER['SERVER_PORT'] ?? 80) === 443);

    $basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');

    $_SERVER['app.baseURL'] = ($isHttps ? 'https://' : 'http://')
        . $_SERVER['HTTP_HOST'] . $basePath . '/';
}

/*
 *---------------------------------------------------------------
 * SET THE CURRENT DIRECTORY
 *---------------------------------------------------------------
 */

// Path to the front controller (this file)
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

// Ensure the current directory is pointing to the front controller's directory
if (getcwd() . DIRECTORY_SEPARATOR !== FCPATH) {
    chdir(FCPATH);
}

/*
 *---------------------------------------------------------------
 * BOOTSTRAP THE APPLICATION
 *---------------------------------------------------------------
 * This process sets up the path constants, loads and registers
 * our autoloader, along with Composer's, loads our constants
 * and fires up an environment-specific bootstrapping.
 */

// LOAD OUR PATHS CONFIG FILE
// This is the line that might need to be changed, depending on your folder structure.
require FCPATH . '../app/Config/Paths.php';
// ^^^ Change this line if you move your application folder

$paths = new Paths();

// LOAD THE FRAMEWORK BOOTSTRAP FILE
require $paths->systemDirectory . '/Boot.php';

exit(Boot::bootWeb($paths));
