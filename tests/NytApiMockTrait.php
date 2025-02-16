<?php

namespace Tests;

use App\Services\NYTApi\BookApi as NytBookApi;

trait NytApiMockTrait
{
    /**
     * @param array $withReturn
     * @param \Exception|null $exception
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    protected function initNytBookApiMock(array $withReturn = [], \Exception $exception = null)
    {
        $nytBookApiMock = $this->getMockBuilder(NytBookApi::class)
                                ->onlyMethods(['getBestSellersHistory'])
                                ->getMock();

        if ($exception) {
            $nytBookApiMock->method('getBestSellersHistory')
                            ->willThrowException($exception);
        } else {
            $nytBookApiMock->method('getBestSellersHistory')
                            ->willReturn($withReturn);
        }

        // Bind the mock to the container
        app()->bind(NytBookApi::class, function() use ($nytBookApiMock) {
            return $nytBookApiMock;
        });

        return $nytBookApiMock;
    }
}
