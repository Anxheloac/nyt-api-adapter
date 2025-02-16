<?php

namespace App\Services\NYTApi;

use Exception;
use App\Exceptions\NytApiException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ApiClient
{
    protected string $apiKey;
    protected int $apiVersion;
    protected string $baseUri;
    protected ?string $errorMessage = null;

    public function __construct()
    {
        $this->apiVersion = config('nyt_api.version');
        $this->baseUri = config('nyt_api.base_uri');
        $this->apiKey = config('nyt_api.api_key');
    }

    /**
     * @return void
     */
    public function initApiConfigs(): void
    {
        $this->apiVersion = config('nyt_api.version');
        $this->baseUri = config('nyt_api.base_uri');
        $this->apiKey = config('nyt_api.api_key');
    }

    /**
     * @param string $path
     * @param array $queryParams
     * @return array
     * @throws ConnectionException
     * @throws NytApiException
     */
    protected function sendRequest(string $path, array $queryParams = []): array
    {
        $url = $this->buildUrl($path);
        $queryParams = array_merge($queryParams, [
            'api-key' => $this->apiKey
        ]);

        try {
            $apiResponse = Http::retry(2, 100, function (Exception $exception) use ($url) {
                    $statusCode = $exception->response->status();
                    if ($statusCode == Response::HTTP_TOO_MANY_REQUESTS) {
                        \Log::info("Retrying request to $url. Status: $statusCode");
                        return true;
                    }
                    return false;
                })
                ->withQueryParameters($queryParams)
                ->get($url);
        } catch (RequestException $requestException) {
            $apiResponse = $requestException->response;
            $status = $apiResponse->status();
            $body = $apiResponse->body();

            // Log the failed response
            Log::error(__('errors.api.nyt_api_request'), [
                'url' => $url,
                'status' => $status,
                'response' => $body
            ]);

            throw new NYTApiException(
                message: $body ?? __('errors.api.generic_error'),
                statusCode: $status
            );
        }

        return $apiResponse->json();
    }

    /**
     * Build the full URL for a specific API endpoint with versioning and path.
     *
     * @param string $path The specific path for the API endpoint.
     *
     * @return string The full URL for the API request.
     */
    private function buildUrl(string $path): string
    {
        return "$this->baseUri/v$this->apiVersion/$path";
    }
}
