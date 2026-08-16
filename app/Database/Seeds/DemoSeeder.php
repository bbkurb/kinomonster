<?php

declare(strict_types=1);

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Демонстрационные данные для локального запуска.
 *
 * Запуск:  php spark db:seed DemoSeeder
 *
 * Создаёт администратора и наполняет сайт примерами, чтобы после установки
 * было что открыть. Повторный запуск безопасен: существующие записи
 * пропускаются, ничего не дублируется и не перезаписывается.
 *
 * На боевом сервере запускать не нужно.
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedUsers();
        $this->seedMovies();
        $this->seedNews();
        $this->seedPosts();
        $this->seedComments();

        echo "\nГотово. Учётные записи для входа:\n";
        echo "  админ:        admin / admin12345\n";
        echo "  пользователь: vasya / vasya12345\n\n";
        echo "Обязательно смените пароли, если сайт будет доступен извне.\n";
    }

    private function seedUsers(): void
    {
        $users = [
            [
                'username' => 'admin',
                'email'    => 'admin@kinomonster.local',
                'password' => password_hash('admin12345', PASSWORD_DEFAULT),
                'role'     => 'admin',
            ],
            [
                'username' => 'vasya',
                'email'    => 'vasya@kinomonster.local',
                'password' => password_hash('vasya12345', PASSWORD_DEFAULT),
                'role'     => 'user',
            ],
        ];

        foreach ($users as $user) {
            $exists = $this->db->table('users')
                ->where('username', $user['username'])
                ->countAllResults() > 0;

            if ($exists) {
                continue;
            }

            $user['banned']     = 0;
            $user['activated']  = 1;
            $user['created_at'] = date('Y-m-d H:i:s');

            $this->db->table('users')->insert($user);
        }
    }

    private function seedMovies(): void
    {
        $movies = [
            ['matrix', 'Матрица', 'Хакер Нео узнаёт, что привычный мир — симуляция, и присоединяется к сопротивлению машинам.', 1999, 8.5, '/assets/img/matrix.png', 'https://www.youtube.com/embed/m8e-FF8MsqU', 'Вачовски', '2024-01-10', 1],
            ['inception', 'Начало', 'Профессиональный вор проникает в чужие сны, чтобы внедрить идею.', 2010, 8.7, '/assets/img/cloud.png', 'https://www.youtube.com/embed/YoHD9XEInc0', 'Кристофер Нолан', '2024-02-11', 1],
            ['interstellar', 'Интерстеллар', 'Экипаж отправляется сквозь червоточину на поиски нового дома для человечества.', 2014, 8.6, '/assets/img/inter.png', 'https://www.youtube.com/embed/zSWdZVtXT7E', 'Кристофер Нолан', '2024-03-12', 1],
            ['arrival', 'Прибытие', 'Лингвист пытается понять язык инопланетных гостей, пока мир на грани войны.', 2016, 7.9, '/assets/img/max.png', 'https://www.youtube.com/embed/tFMo3UJ4B4g', 'Дени Вильнёв', '2024-04-13', 1],
            ['dune', 'Дюна', 'Наследник дома Атрейдесов оказывается в центре борьбы за пустынную планету Арракис.', 2021, 8.0, '/assets/img/dead.png', 'https://www.youtube.com/embed/n9xhJrPXop4', 'Дени Вильнёв', '2024-05-14', 1],
            ['breaking-bad', 'Во все тяжкие', 'Школьный учитель химии узнаёт о смертельном диагнозе и начинает варить метамфетамин.', 2008, 9.4, '/assets/img/breakingbad.png', 'https://www.youtube.com/embed/HhesaQXLuRY', 'Винс Гиллиган', '2024-01-20', 2],
            ['dark', 'Тьма', 'Исчезновение ребёнка в немецком городке вскрывает тайну путешествий во времени.', 2017, 8.7, '/assets/img/xfiles.png', 'https://www.youtube.com/embed/rrwycJ08PSA', 'Баран бо Одар', '2024-02-21', 2],
            ['chernobyl', 'Чернобыль', 'Хроника аварии на ЧАЭС и работы ликвидаторов.', 2019, 9.3, '/assets/img/silicon.png', 'https://www.youtube.com/embed/s9APLXM9Ei8', 'Йохан Ренк', '2024-03-22', 2],
        ];

        foreach ($movies as $m) {
            if ($this->db->table('movie')->where('slug', $m[0])->countAllResults() > 0) {
                continue;
            }

            $this->db->table('movie')->insert([
                'slug'         => $m[0],
                'name'         => $m[1],
                'descriptions' => $m[2],
                'year'         => $m[3],
                'rating'       => $m[4],
                'poster'       => $m[5],
                'player_code'  => $m[6],
                'director'     => $m[7],
                'add_date'     => $m[8],
                'category_id'  => $m[9],
            ]);
        }
    }

    private function seedNews(): void
    {
        $news = [
            ['oscar-2026', 'Названы номинанты на Оскар 2026', 'Академия объявила список номинантов. Лидером стал новый фильм Дени Вильнёва с девятью номинациями.'],
            ['new-dune', 'Третья часть Дюны запущена в производство', 'Съёмки начнутся весной, релиз запланирован на конец следующего года.'],
            ['cannes', 'Каннский фестиваль объявил программу', 'В основном конкурсе 21 фильм, включая три дебютные работы.'],
        ];

        foreach ($news as $n) {
            if ($this->db->table('news')->where('slug', $n[0])->countAllResults() > 0) {
                continue;
            }

            $this->db->table('news')->insert(['slug' => $n[0], 'title' => $n[1], 'text' => $n[2]]);
        }
    }

    private function seedPosts(): void
    {
        $posts = [
            ['top-sci-fi', 'Топ-10 фантастики десятилетия', 'Собрали десять фантастических фильмов, которые определили жанр за последние десять лет, и разобрали, чем каждый из них важен для кинематографа.'],
            ['nolan-guide', 'Как смотреть Нолана', 'Нелинейное повествование, время как полноценный персонаж и практические эффекты вместо компьютерной графики — краткий путеводитель по фильмографии режиссёра.'],
        ];

        foreach ($posts as $p) {
            if ($this->db->table('posts')->where('slug', $p[0])->countAllResults() > 0) {
                continue;
            }

            $this->db->table('posts')->insert(['slug' => $p[0], 'title' => $p[1], 'text' => $p[2]]);
        }
    }

    private function seedComments(): void
    {
        if ($this->db->table('comments')->countAllResults() > 0) {
            return;
        }

        $admin = $this->db->table('users')->where('username', 'admin')->get()->getRowArray();
        $vasya = $this->db->table('users')->where('username', 'vasya')->get()->getRowArray();
        $movie = $this->db->table('movie')->where('slug', 'matrix')->get()->getRowArray();

        if ($admin === null || $vasya === null || $movie === null) {
            return;
        }

        $this->db->table('comments')->insertBatch([
            ['user_id' => $vasya['id'], 'movie_id' => $movie['id'], 'comment_text' => 'Отличный фильм, пересматриваю каждый год!'],
            ['user_id' => $admin['id'], 'movie_id' => $movie['id'], 'comment_text' => 'Классика жанра, ничего лучше с тех пор не сняли.'],
        ]);
    }
}
