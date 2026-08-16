<?php

declare(strict_types=1);

namespace App\Validation;

/**
 * Правила валидации ссылок на медиа.
 */
class AssetRules
{
    /**
     * Ссылка на постер или плеер.
     *
     * Допускаются относительные пути (/assets/img/matrix.png) и абсолютные
     * http/https-адреса. Схемы javascript:, data:, vbscript: и им подобные
     * отклоняются — иначе значение попадёт в атрибут src и выполнится в
     * браузере посетителя.
     */
    public function safe_media_url(?string $str, ?string &$error = null): bool
    {
        $str = trim((string) $str);

        if ($str === '') {
            return true; // пустое значение проверяет правило required
        }

        if (str_starts_with($str, '/') && ! str_starts_with($str, '//')) {
            return true;
        }

        $scheme = strtolower((string) parse_url($str, PHP_URL_SCHEME));

        if (in_array($scheme, ['http', 'https'], true)) {
            return true;
        }

        $error = 'Ссылка должна начинаться с / либо с http:// или https://';

        return false;
    }
}
