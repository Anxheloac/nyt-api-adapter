<?php

namespace Tests\Feature;

use App\Exceptions\NytApiException;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;
use Tests\HelperTrait;
use Tests\NytApiMockTrait;
use Tests\TestCase;

class BookControllerTest extends TestCase
{
    use HelperTrait, NytApiMockTrait;

    public function setUp(): void
    {
        parent::setUp();
        Config::set('nyt_api.api_cache_enabled', false);
    }

    /**
     *
     * @return void
     */
    public function test_get_best_seller_history_api_successful(): void
    {
        $bestSellerHistoryWithResult = $this->load_fixture_data('best_sellers_history.json', true);
        $this->initNytBookApiMock(
            withReturn: $bestSellerHistoryWithResult
        );

        $response = $this->getJson('/api/v1/books/best-sellers/history');

        $expectedJsonResponse = $this->get_api_response_wrapper(
            data: [
                'results' => $bestSellerHistoryWithResult['results'],
                'total' => $bestSellerHistoryWithResult['num_results']
            ]
        );

        $response->assertStatus(Response::HTTP_OK)
                    ->assertJson($expectedJsonResponse);
    }

    /**
     * @return void
     */
    public function test_get_best_seller_history_api_error(): void
    {
        $this->initNytBookApiMock(
            exception : new NytApiException()
        );

        $response = $this->getJson('/api/v1/books/best-sellers/history');
        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * A basic feature test example.
     */
    public function test_get_best_seller_history_api_not_found_error(): void
    {
        $response = $this->getJson('/api/v2/books/best-sellers/history');
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * A basic feature test example.
     */
    public function test_get_best_seller_history_api_validation_fails(): void
    {
        $queryParameters = [
            'offset' => 30,
            'isbn' => '12345'
        ];

        $urlQuery = http_build_query($queryParameters);

        $response = $this->getJson('/api/v1/books/best-sellers/history?'.$urlQuery);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['offset', 'isbn']);
    }
}
