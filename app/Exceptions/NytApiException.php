<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class NytApiException extends Exception
{
    /**
     * NYTApiException constructor.
     *
     * @param string $message
     * @param int $statusCode
     */
    public function __construct(
        string $message = "NYT API Error",
        protected int $statusCode = 500
    ) {
        parent::__construct($message);
    }

    /**
     * Report the exception
     */
    public function report(): void
    {
        Log::error("NYT API Error: {$this->getMessage()}", [
            'status' => $this->statusCode
        ]);

        // Optionally, send error to an external monitoring tool
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @return JsonResponse
     */
    public function render(): JsonResponse
    {
        return response()->json([
            'error' => true,
            'message' => $this->getMessage(),
            'data' => []
        ], ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
    }
}
