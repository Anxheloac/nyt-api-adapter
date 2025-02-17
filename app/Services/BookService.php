<?php

namespace App\Services;

use App\Exceptions\NytApiException;
use App\Services\NYTApi\BookApi as NytBookApi;
use App\Tools\ICacheService;
use Illuminate\Http\Client\ConnectionException;

class BookService
{
    /**
     * @param NytBookApi $nytBookApi
     */
    public function __construct(
        protected NytBookApi $nytBookApi,
        protected ICacheService $cacheService
    ) {}

    /**
     *
     * @param array $queryParams
     * @throws NytApiException
     * @throws ConnectionException
     */
    public function getBestSellersHistory(array $queryParams = []): array
    {
        if (config('nyt_api.api_cache_enabled')) {
            $cacheKey = $this->generateBestSellersDataCacheKey($queryParams);

            $apiData = $this->cacheService->cacheApiResponse(
                key: $cacheKey,
                callback: function () use ($queryParams) {
                    return $this->nytBookApi->getBestSellersHistory(
                        searchParams: $queryParams
                    );
                },
                ttl: config('nyt_api.cache_expiry')
            );
        } else {
            $apiData = $this->nytBookApi->getBestSellersHistory(
                searchParams: $queryParams
            );
        }

        return [
            'results' => $apiData['results'],
            'total' => $apiData['num_results']
        ];
    }

    /**
     * @param array $queryParams
     * @return string
     */
    private function generateBestSellersDataCacheKey(array $queryParams): string
    {
        if (!empty($queryParams)) {
            // Serialize the queryParams to make them suitable for use in a cache key
            return config('nyt_api.cache_prefix.best_seller') . md5(json_encode($queryParams));
        } else {
            return config('nyt_api.cache_prefix.best_seller');
        }
    }
}
