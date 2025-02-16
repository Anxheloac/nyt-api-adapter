<?php

namespace Tests\Unit;

use App\Exceptions\NytApiException;
use Dflydev\DotAccessData\Data;
use GuzzleHttp\Psr7\Uri;
use Illuminate\Support\Facades\Http;
use App\Services\NYTApi\BookApi as NytBookApi;
use Symfony\Component\HttpFoundation\Response;
use Tests\HelperTrait;
use Tests\TestCase;

class NYTApiTest extends TestCase
{
    use HelperTrait;

    public function test_best_seller_history_successful_response()
    {
        // Load the fixture data
        $bestSellerHistoryJson = $this->load_fixture_data('best_sellers_history.json');
        $bestSellerHistoryWithResult = json_decode($bestSellerHistoryJson, true);

        $nytBookApi = app(NytBookApi::class);

        Http::fake([
            'api.nytimes.com/*' => Http::response($bestSellerHistoryJson),
        ]);

        $response = $nytBookApi->getBestSellersHistory();

        self::assertSame($bestSellerHistoryWithResult['num_results'], $response['num_results']);
        self::assertSame($bestSellerHistoryWithResult['results'], $response['results']);
    }

    public function test_best_seller_history_key_error_response()
    {
        // Load the fixture data
        $apiKeyError = $this->load_fixture_data('api_key_error.json');

        $nytBookApi = app(NytBookApi::class);

        Http::fake([
            'api.nytimes.com/*' => Http::response($apiKeyError, Response::HTTP_UNAUTHORIZED),
        ]);

        $this->expectException(NytApiException::class);
        $this->expectExceptionMessage(message: $apiKeyError);

        $response = $nytBookApi->getBestSellersHistory();

        self::assertEmpty($response);
    }

    public function test_best_seller_history_quota_limit_error_response()
    {
        // Load the fixture data
        $limitQuotaError = $this->load_fixture_data('quota_limit_error.json');
        $bestSellerHistoryJson = $this->load_fixture_data('best_sellers_history.json');

        $nytBookApi = app(NytBookApi::class);

        Http::fake([
            'api.nytimes.com/*' =>
                Http::sequence()
                    ->push($limitQuotaError, Response::HTTP_TOO_MANY_REQUESTS)
                    ->push($bestSellerHistoryJson, Response::HTTP_OK)
        ]);

        $response = $nytBookApi->getBestSellersHistory();

        Http::assertSentCount(2);
    }

    public function test_best_seller_history_api_with_search()
    {
        // Load the fixture data
        $filteredResponseJson = $this->load_fixture_data('best_sellers_history_filtered.json');
        $filteredResponseResult = json_decode($filteredResponseJson, true);

        $nytBookApi = app(NytBookApi::class);

        Http::fake([
            'api.nytimes.com/*' => Http::response($filteredResponseJson),
        ]);

        $searchParameters = [
            'author' => 'diana',
            'title' => 'GIVE YOU',
            'isbn' => ['0399178570'],
            'offset' => 0
        ];

        $response = $nytBookApi->getBestSellersHistory($searchParameters);

        self::assertSame($filteredResponseResult['num_results'], $response['num_results']);
        self::assertSame($filteredResponseResult['results'], $response['results']);
    }

    public function test_best_seller_history_api_search_arguments()
    {
        // Load the fixture data
        $filteredResponseJson = $this->load_fixture_data('best_sellers_history_filtered.json');

        Http::fake([
            'api.nytimes.com/*' => Http::response($filteredResponseJson),
        ]);

        $nytBookApi = app(NytBookApi::class);
        $nytBookApi->initApiConfigs();

        $searchParameters = [
            'isbn' => ['1234', '2345'],
            'author' => 'diana',
            'title' => 'GIVE YOU',
            'offset' => 0
        ];

        $response = $nytBookApi->getBestSellersHistory($searchParameters);

        Http::assertSent(function ($request) use ($searchParameters) {
            $url = parse_url($request->url());
            parse_str($url['query'], $queryParams);

            return $queryParams['author'] == $searchParameters['author'] &&
                $queryParams['title'] == $searchParameters['title'] &&
                $queryParams['isbn'] = '1234;2345';
        });
    }
}
