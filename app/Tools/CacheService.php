<?php

namespace App\Tools;

use Illuminate\Support\Facades\Cache;

class CacheService implements ICacheService
{
    /**
     * Cache an API response.
     *
     * @param string $key
     * @param callable $callback
     * @param int $ttl Time to live in minutes
     * @return mixed
     */
    public function cacheApiResponse(string $key, callable $callback, int $ttl = 60)
    {
        \Log::info('Looking in cache for: '. $key);

        // Check if the data is already cached
        if (Cache::has($key)) {
            // Return the cached data if available
            \Log::info('Get from cache: '. $key);
            return Cache::get($key);
        }

        // Otherwise, execute the callback and store the result in the cache
        $data = $callback();

        // Store the result in cache for a specified TTL (Time To Live)
        Cache::put($key, $data, $ttl);

        \Log::info($key . ' Not found! Fetch from Api and store in the cache');

        return $data;
    }

    /**
     * Get the cached response by key.
     *
     * @param string $key
     * @return mixed
     */
    public function getCachedApiResponse(string $key)
    {
        return Cache::get($key);
    }

    /**
     * Clear the cache by key.
     *
     * @param string $key
     * @return void
     */
    public function clearCache(string $key)
    {
        Cache::forget($key);
    }
}
