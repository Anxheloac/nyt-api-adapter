<?php

namespace Tests;

use App\Http\Responses\ApiResponse;

trait HelperTrait
{
    /**
     * @param string $subPath
     * @return false|string
     */
    private function load_fixture_data(string $subPath, bool $toArray = false): string|array
    {
        $jsonResource = file_get_contents('tests/Fixtures/NYTApi/'. $subPath);

        return $toArray ? json_decode($jsonResource, true) : $jsonResource;
    }

    /**
     * @param array $data
     * @return array
     */
    private function get_api_response_wrapper(array $data): array
    {
        return [
            'status' => 'success',
            'message' => 'Success',
            'data' => $data,
        ];
    }

}
