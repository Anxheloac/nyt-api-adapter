<?php

namespace App\Services\NYTApi;

use App\Exceptions\NytApiException;
use Illuminate\Http\Client\ConnectionException;

class BookApi extends ApiClient
{
    /**
     * @param array $searchParams
     * @return array
     * @throws ConnectionException
     * @throws NytApiException
     */
    public function getBestSellersHistory(array $searchParams = []): array
    {
        $path = config('nyt_api.paths.books.best_seller_history');

        if(isset($searchParams['isbn'])) {
            $searchParams['isbn'] = implode(';', $searchParams['isbn']);
        }

        return parent::sendRequest($path, $searchParams);
    }
}
