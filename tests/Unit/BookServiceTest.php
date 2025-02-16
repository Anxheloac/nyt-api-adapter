<?php

namespace Tests\Unit;

use App\Services\BookService;
use App\Tools\CacheService;
use Tests\NytApiMockTrait;
use Tests\TestCase;
use Tests\HelperTrait;
use function PHPUnit\Framework\assertSame;

class BookServiceTest extends TestCase
{
    use HelperTrait, NytApiMockTrait;

    /**
     * A basic unit test example.
     */
    public function test_get_best_sellers_history_data(): void
    {
        $bestSellerHistoryWithResult = $this->load_fixture_data('best_sellers_history.json', true);

        $this->initNytBookApiMock(
            withReturn: $bestSellerHistoryWithResult
        );

        $bookService = app(BookService::class);
        $response = $bookService->getBestSellersHistory();

        assertSame($response['results'], $bestSellerHistoryWithResult['results']);
        assertSame($response['total'], $bestSellerHistoryWithResult['num_results']);
    }

    /**
     * A basic unit test example.
     */
    public function test_get_best_sellers_history_data_with_cache(): void
    {
        $bestSellerHistoryWithResult = $this->load_fixture_data('best_sellers_history.json', true);

        $cacheServiceMock = $this->getMockBuilder(CacheService::class)
                                    ->getMock();

        $cacheServiceMock->method('cacheApiResponse')
                            ->willReturn($bestSellerHistoryWithResult);

        $bookService = app(BookService::class);
        $response = $bookService->getBestSellersHistory();

        assertSame($response['results'], $bestSellerHistoryWithResult['results']);
        assertSame($response['total'], $bestSellerHistoryWithResult['num_results']);
    }
}
