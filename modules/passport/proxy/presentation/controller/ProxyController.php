<?php

declare(strict_types=1);

namespace modules\passport\proxy\presentation\controller;

use core\presentation\controller\BaseController;
use Yii;
use yii\base\InvalidConfigException;
use yii\httpclient\Client;
use yii\httpclient\Exception;
use yii\web\BadRequestHttpException;
use yii\web\Response;
use OpenApi\Attributes as OA;
use yii\web\UploadedFile;

/**
 * Прокси-контроллер для запросов к центральному сервису Passport.
 *
 * Маршрут: GET|POST|PUT|DELETE /passport/proxy/{url}
 * Подставляет токен доступа из куки taskflow_access_token в Bearer-заголовок.
 * Работает только по разрешённой матрице (см. config/proxy.php).
 */
final class ProxyController extends BaseController
{
    #[OA\Get(
        path: '/passport/proxy/{url}',
        description: 'Проксирует GET-запрос на центральный сервис Passport. Query-параметры пробрасываются в Passport.',
        summary: 'Проксировать GET-запрос в Passport',
        security: [
            ['bearerAuth' => []],
            ['accessTokenCookie' => []],
        ],
        tags: ['passport-proxy'],
        parameters: [
            new OA\Parameter(
                name: 'url',
                description: 'Целевой путь в Passport, например `users/123`',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string'),
                example: 'users/123',
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Ответ Passport. Статус и тело ответа проксируются как есть.',
            ),
            new OA\Response(
                response: 400,
                description: 'Проксирование запрещено или отсутствует access token',
                content: new OA\JsonContent(ref: '#/components/schemas/Error'),
            ),
            new OA\Response(
                response: 401,
                description: 'Passport вернул 401 Unauthorized',
                content: new OA\JsonContent(ref: '#/components/schemas/Error'),
            ),
            new OA\Response(
                response: 502,
                description: 'Не удалось выполнить прокси-запрос',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Proxy request failed'),
                        new OA\Property(property: 'message', type: 'string'),
                    ],
                    type: 'object',
                ),
            ),
        ],
    )]
    #[OA\Post(
        path: '/passport/proxy/{url}',
        description: 'Проксирует POST-запрос на центральный сервис Passport. Query-параметры и тело запроса пробрасываются как есть. Для endpoint\'ов из `multipart_proxy` поддерживается multipart/form-data с файлами.',
        summary: 'Проксировать POST-запрос в Passport',
        security: [
            ['bearerAuth' => []],
            ['accessTokenCookie' => []],
        ],
        requestBody: new OA\RequestBody(
            description: 'Тело запроса пробрасывается в Passport как есть. Для разрешённых endpoint\'ов поддерживается multipart/form-data.',
            required: false,
            content: [
                new OA\MediaType(
                    mediaType: 'application/json',
                    schema: new OA\Schema(type: 'object'),
                ),
                new OA\MediaType(
                    mediaType: 'multipart/form-data',
                    schema: new OA\Schema(
                        type: 'object',
                        additionalProperties: true,
                    ),
                ),
            ],
        ),
        tags: ['passport-proxy'],
        parameters: [
            new OA\Parameter(
                name: 'url',
                description: 'Целевой путь в Passport, например `users/123`',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string'),
                example: 'users/123',
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Ответ Passport. Статус и тело ответа проксируются как есть.',
            ),
            new OA\Response(
                response: 400,
                description: 'Проксирование запрещено или отсутствует access token',
                content: new OA\JsonContent(ref: '#/components/schemas/Error'),
            ),
            new OA\Response(
                response: 401,
                description: 'Passport вернул 401 Unauthorized',
                content: new OA\JsonContent(ref: '#/components/schemas/Error'),
            ),
            new OA\Response(
                response: 502,
                description: 'Не удалось выполнить прокси-запрос',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Proxy request failed'),
                        new OA\Property(property: 'message', type: 'string'),
                    ],
                    type: 'object',
                ),
            ),
        ],
    )]
    #[OA\Put(
        path: '/passport/proxy/{url}',
        description: 'Проксирует PUT-запрос на центральный сервис Passport. Query-параметры и тело запроса пробрасываются как есть.',
        summary: 'Проксировать PUT-запрос в Passport',
        security: [
            ['bearerAuth' => []],
            ['accessTokenCookie' => []],
        ],
        requestBody: new OA\RequestBody(
            description: 'Тело запроса пробрасывается в Passport как есть',
            required: false,
            content: [
                new OA\MediaType(
                    mediaType: 'application/json',
                    schema: new OA\Schema(type: 'object'),
                ),
            ],
        ),
        tags: ['passport-proxy'],
        parameters: [
            new OA\Parameter(
                name: 'url',
                description: 'Целевой путь в Passport, например `users/123`',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string'),
                example: 'users/123',
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Ответ Passport. Статус и тело ответа проксируются как есть.',
            ),
            new OA\Response(
                response: 400,
                description: 'Проксирование запрещено или отсутствует access token',
                content: new OA\JsonContent(ref: '#/components/schemas/Error'),
            ),
            new OA\Response(
                response: 401,
                description: 'Passport вернул 401 Unauthorized',
                content: new OA\JsonContent(ref: '#/components/schemas/Error'),
            ),
            new OA\Response(
                response: 502,
                description: 'Не удалось выполнить прокси-запрос',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Proxy request failed'),
                        new OA\Property(property: 'message', type: 'string'),
                    ],
                    type: 'object',
                ),
            ),
        ],
    )]
    #[OA\Delete(
        path: '/passport/proxy/{url}',
        description: 'Проксирует DELETE-запрос на центральный сервис Passport. Query-параметры пробрасываются в Passport.',
        summary: 'Проксировать DELETE-запрос в Passport',
        security: [
            ['bearerAuth' => []],
            ['accessTokenCookie' => []],
        ],
        tags: ['passport-proxy'],
        parameters: [
            new OA\Parameter(
                name: 'url',
                description: 'Целевой путь в Passport, например `users/123`',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string'),
                example: 'users/123',
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Ответ Passport. Статус и тело ответа проксируются как есть.',
            ),
            new OA\Response(
                response: 400,
                description: 'Проксирование запрещено или отсутствует access token',
                content: new OA\JsonContent(ref: '#/components/schemas/Error'),
            ),
            new OA\Response(
                response: 401,
                description: 'Passport вернул 401 Unauthorized',
                content: new OA\JsonContent(ref: '#/components/schemas/Error'),
            ),
            new OA\Response(
                response: 502,
                description: 'Не удалось выполнить прокси-запрос',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Proxy request failed'),
                        new OA\Property(property: 'message', type: 'string'),
                    ],
                    type: 'object',
                ),
            ),
        ],
    )]
    /**
     * Единственный action, принимающий любой HTTP-метод.
     *
     * @throws BadRequestHttpException
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function actionIndex(string $url): Response
    {
        $method = strtoupper(Yii::$app->request->getMethod());

        $params = require dirname(__DIR__, 2) . '/config/params.php';

        // 1. Проверяем, разрешён ли целевой путь
        if (!$this->isAllowed($method, $url, $params["proxy"])) {
            throw new BadRequestHttpException('Proxying to this endpoint is not allowed');
        }

        // 2. Извлекаем access-токен из куки
        $accessToken = Yii::$app->request->cookies->getValue($params['access_token_cookie_name']);
        if (!is_string($accessToken) || $accessToken === '') {
            throw new BadRequestHttpException('Access token is missing');
        }

        // 3. Строим целевой URL и пробрасываем query-параметры (кроме служебного `url`)
        $targetUrl = rtrim($params['passport_url'], '/') . '/' . ltrim($url, '/');

        $queryParams = Yii::$app->request->getQueryParams();
        unset($queryParams['url']);
        if ($queryParams !== []) {
            $separator = str_contains($targetUrl, '?') ? '&' : '?';
            $targetUrl .= $separator . http_build_query($queryParams);
        }

        // 4. Создаём прокси-запрос
        /** @var Client $client */
        $client = Yii::$container->get(Client::class);
        $proxyRequest = $client->createRequest()
            ->setMethod($method)
            ->setUrl($targetUrl)
            ->addHeaders(['Authorization' => 'Bearer ' . $accessToken])
            ->setOptions(['timeout' => 30]);

        $contentType = (string)Yii::$app->request->getHeaders()->get('Content-Type', '');
        $isMultipart = stripos($contentType, 'multipart/form-data') === 0;

        if ($isMultipart) {
            // 5a. Multipart разрешён только для whitelisted endpoint'ов
            $multipartRoutes = $params['multipart_proxy'][$method] ?? [];
            if (!$this->matchesAny($url, $multipartRoutes)) {
                throw new BadRequestHttpException('Multipart proxying is not allowed for this endpoint');
            }

            // 5b. Добавляем текстовые поля.
            //     Yii автоматически «помечает» запрос как multipart при вызове addFile().
            $bodyParams = Yii::$app->request->getBodyParams();
            if ($bodyParams !== []) {
                $proxyRequest->setData($bodyParams);
            }

            // 5c. Добавляем файлы.
            //     Для массивов (`files[]`) используем имена `files[0]`, `files[1]`, ...
            //     чтобы избежать перезаписи, как рекомендовано в документации.
            foreach (array_keys($_FILES) as $fieldName) {
                $isArrayInput = is_array($_FILES[$fieldName]['name'] ?? null);

                foreach (UploadedFile::getInstancesByName($fieldName) as $index => $uploadedFile) {
                    if ($uploadedFile->getHasError()) {
                        continue;
                    }

                    $targetFieldName = $isArrayInput ? "{$fieldName}[{$index}]" : $fieldName;

                    $proxyRequest->addFile($targetFieldName, $uploadedFile->tempName, [
                        'fileName'    => $uploadedFile->name,
                        'contentType' => $uploadedFile->type !== '' ? $uploadedFile->type : 'application/octet-stream',
                    ]);
                }
            }

            // ВАЖНО: Content-Type для multipart НЕ добавляем вручную —
            // Yii сгенерирует его сам с корректным boundary.
        } else {
            // 5d. Не-multipart: пробрасываем тело как есть
            $body = Yii::$app->request->getRawBody();
            if ($body !== '' && $body !== null) {
                $proxyRequest->setContent($body);
                if ($contentType !== '') {
                    $proxyRequest->addHeaders(['Content-Type' => $contentType]);
                }
            }
        }

        // 6. Отправляем запрос
        try {
            $response = $proxyRequest->send();
        } catch (Exception $e) {
            Yii::$app->response->statusCode = 502;
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = [
                'error'   => 'Proxy request failed',
                'message' => $e->getMessage(),
            ];

            return Yii::$app->response;
        }

        // 7. Возвращаем ответ в JSON
        Yii::$app->response->statusCode = $response->getStatusCode();
        Yii::$app->response->format = Response::FORMAT_JSON;
        Yii::$app->response->data = $response->getData();

        return Yii::$app->response;
    }

    /**
     * Проверяет, разрешён ли метод + путь по матрице.
     *
     * @param array<string, string[]> $allowedRoutes
     */
    private function isAllowed(string $method, string $url, array $allowedRoutes): bool
    {
        return $this->matchesAny($url, $allowedRoutes[$method] ?? []);
    }

    /**
     * @param string[] $patterns
     */
    private function matchesAny(string $url, array $patterns): bool
    {
        $url = '/' . ltrim($url, '/');

        foreach ($patterns as $pattern) {
            if ($this->matchRoute($pattern, $url)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Сопоставляет URL с паттерном.
     *
     * Поддерживает:
     * - точное совпадение,
     * - wildcard «*» в конце,
     * - параметры {param}.
     */
    private function matchRoute(string $pattern, string $url): bool
    {
        // Точное совпадение
        if ($pattern === $url) {
            return true;
        }

        // Wildcard в конце: /users/*
        if (str_ends_with($pattern, '*') && str_starts_with($url, rtrim($pattern, '*'))) {
            return true;
        }

        // Параметры вида {id}
        if (preg_match('/\{[^}]+\}/', $pattern) === 1) {
            $regex = '#^' . preg_replace('/\{[^}]+\}/', '[^/]+', $pattern) . '$#';

            return preg_match($regex, $url) === 1;
        }

        return false;
    }
}
