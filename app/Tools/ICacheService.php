<?php

namespace App\Tools;

interface ICacheService
{
    public function cacheApiResponse(string $key, callable $callback, int $ttl = 60);

    public function getCachedApiResponse(string $key);

    public function clearCache(string $key);
}
