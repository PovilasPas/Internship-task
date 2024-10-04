<?php

declare(strict_types=1);

namespace App\Cache;

class SimpleCache implements CacheInterface
{
    private const int DEFAULT_TTL = 300;

    public function __construct(
        private readonly \Memcached $cache
    ) {

    }

    public function get(string $key, mixed $default = null): mixed
    {
        $value = $this->cache->get($key);
        $code = $this->cache->getResultCode();
        if ($code !== \Memcached::RES_SUCCESS) {
            return $default;
        }

        return $value;
    }

    public function set(string $key, mixed $value, null|int|\DateInterval $ttl = null): bool
    {
        $expiration = $this->ttlToSecs($ttl);

        return $this->cache->set($key, $value, $expiration);
    }

    public function delete(string $key): bool
    {
        return $this->cache->delete($key);
    }

    public function clear(): bool
    {
        return $this->cache->flush();
    }

    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        $keyArr = [];
        foreach ($keys as $key) {
            $keyArr[] = $key;
        }
        $result = $this->cache->getMulti($keyArr);

        if ($result === false) {
            $result = [];
        }

        foreach ($keys as $key) {
            if(!array_key_exists($key, $result)) {
                $result[$key] = $default;
            }
        }

        return $result;
    }

    public function setMultiple(iterable $values, null|int|\DateInterval $ttl = null): bool
    {
            $valArr = [];
            $expiration = $this->ttlToSecs($ttl);
            foreach ($values as  $key => $value) {
                $valArr[$key] = $value;
            }

            return $this->cache->setMulti($valArr, $expiration);
    }

    public function deleteMultiple(iterable $keys): bool
    {
        $keyArr = [];
        foreach ($keys as $key) {
            $keyArr[] = $key;
        }

        $result = $this->cache->deleteMulti($keyArr);
        foreach ($result as $value) {
            if ($value !== true) {
                return false;
            }
        }

        return true;
    }

    public function has(string $key): bool
    {
        $this->cache->get($key);
        $code = $this->cache->getResultCode();

        return  $code === \Memcached::RES_SUCCESS;
    }

    private function ttlToSecs(null|int|\DateInterval $ttl): int
    {
        if ($ttl instanceof \DateInterval) {
            return $ttl->days * 86400 + $ttl->h * 3600 + $ttl->i * 60 + $ttl->s;
        }

        if($ttl !== null) {
            return $ttl;
        }

        return self::DEFAULT_TTL;
    }
}
