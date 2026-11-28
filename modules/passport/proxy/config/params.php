<?php

declare(strict_types=1);

return [
    'passport_url' => rtrim((string)($_ENV['PASSPORT_URL'] ?? 'https://passport.thescript.agency'), '/'),
    'access_token_cookie_name' => 'taskflow_access_token',

    /**
     * Разрешённая матрица проксирования запросов на центральный сервис (Passport).
     *
     * Ключ — HTTP-метод, значение — массив разрешённых путей.
     * Пути поддерживают wildcard «*» в конце и параметры в виде <param>.
     */
    'proxy' => [
        'GET' => [
            // Должности
            '/posts',
            '/posts/{id}',
            // Роли центрального сервера
            '/roles',
            '/roles/{id}',
            // Пользовательские контакты
            '/user-contacts',
            // Пользователськие образования
            '/user-educations',
            // Роли пользователя
            '/user-roles',
            // Пользователь
            '/users/{id}',
            '/users',
            // Получить свои согласия
            '/users/{id}/accepts',
            // Получить аватарку/пользовательский файл
            '/storage/files/{id}/download',
        ],
        'POST' => [
            // Пользователь
            '/users/{id}/update',
            // Пользовательские контакты
            '/user-contacts',
            // Пользователськие образования
            '/user-educations',
        ],
        'PUT' => [
            // Пользовательские контакты
            '/user-contacts/{id}',
            // Пользователськие образования
            '/user-educations/{id}',
        ],
        'DELETE' => [
            // Пользовательские контакты
            '/user-contacts/{id}',
            // Пользователськие образования
            '/user-educations/{id}',
        ],
    ],
    /**
     * Endpoints, для которых разрешена передача файлов (multipart/form-data).
     *
     * Проверяется В ДОПОЛНЕНИЕ к `proxy`: endpoint должен присутствовать
     * и в `proxy`, и в `multipart_proxy`.
     */
    'multipart_proxy' => [
        'POST' => [
            '/users/{id}/update',
        ],
        'PUT' => [],
    ],
];
