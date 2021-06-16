<?php

namespace App\Service\Retail;

use App\Exceptions\ResponseNotSuccessRetailApiException;
use RetailCrm\ApiClient;
use RetailCrm\Response\ApiResponse;

/**
 * Сервис для получения данных от RetailCRM посредством API
 */
class RetailAPI
{
    public const MAX_LIMIT = 100;

    public ApiClient $client;

    public function __construct(
        string $domain,
        string $apiToken
    ) {
        $this->client = new ApiClient($domain, $apiToken);
    }

    /**
     * Получение списка магазинов
     *
     * @return array
     */
    public function getSites(): array
    {
        $response = $this->client->request->sitesList();
        $this->throwExceptionIfNotSuccess($response);

        return $response->getResponse()['sites'];
    }

    /**
     * Получение списка групп товаров
     *
     * @param array $filter Фильтр
     *
     * @return array
     */
    public function getProductGroups(array $filter = []): array
    {
        $request = function (int $page = null) use ($filter) {
            return $this->client->request->storeProductsGroups($filter, $page, self::MAX_LIMIT);
        };

        return $this->handlerRequestWithPagination($request, 'productGroup');
    }

    /**
     * Получение списка товаров с торговыми предложениями
     *
     * @param array $filter Фильтр
     *
     * @return array
     */
    public function getProducts(array $filter = []): array
    {
        $request = function (int $page = null) use ($filter) {
            return $this->client->request->storeProducts($filter, $page, self::MAX_LIMIT);
        };

        return $this->handlerRequestWithPagination($request, 'products');
    }

    /**
     * обработать запрос с пагинацией
     *
     * @param callable $request
     * @param string   $responseDataKey
     *
     * @return array
     */
    protected function handlerRequestWithPagination(callable $request, string $responseDataKey): array
    {
        /** @var ApiResponse $response */
        $response = $request();
        $this->throwExceptionIfNotSuccess($response);
        $data[] = $response->getResponse()[$responseDataKey];

        // Если страниц больше - получаем остальные
        for ($i = $response['pagination']['currentPage']; $i < $response['pagination']['totalPageCount']; ++$i) {
            $response = $request($i + 1);
            $this->throwExceptionIfNotSuccess($response);
            $data[] = $response->getResponse()[$responseDataKey];
        }
        $data = array_merge([], ...$data);

        return $data;
    }

    /**
     * просить исключение если ответ не успешный
     *
     * @param ApiResponse $response
     */
    protected function throwExceptionIfNotSuccess(ApiResponse $response)
    {
        if (!$response->isSuccessful() || !$response['success']) {
            throw new ResponseNotSuccessRetailApiException($response['errorMsg'] ?? 'ответ не успешный');
        }
    }
}
