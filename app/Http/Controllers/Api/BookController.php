<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\BestSellerSearchRequest;
use App\Http\Responses\ApiResponse;
use App\Services\BookService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class BookController extends Controller
{
    /**
     * @param BookService $bookService
     */
    public function __construct(
        protected BookService $bookService
    ) {}

    /**
     * @param BestSellerSearchRequest $request
     * @return JsonResponse
     */
    public function getBestSellersHistory(BestSellerSearchRequest $request): JsonResponse
    {
        try {
            $searchParams = $request->filledFields();
            $bestSellersData = $this->bookService->getBestSellersHistory($searchParams);

            return ApiResponse::success(
                data: $bestSellersData
            );
        } catch (\Exception $exception) {

            \Log::error($exception);

            return ApiResponse::error(
                message: __('errors.api.generic_error'),
                statusCode: ResponseAlias::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
