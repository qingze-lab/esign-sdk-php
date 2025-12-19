<?php
declare(strict_types = 1);

namespace QingzeLab\OpenIM\Tests\Support;

use DateInterval;
use Psr\SimpleCache\CacheInterface;

final class RedisCache implements CacheInterface
{
    private $redis;

    /**
     * @var string
     */
    private string $prefix;

    public function __construct($redis, string $prefix = 'openim:')
    {
        $this->redis  = $redis;
        $this->prefix = $prefix;
    }

    private function key(string $key): string
    {
        return $this->prefix . $key;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $val = $this->redis->get($this->key($key));
        return $val === false ? $default : $val;
    }

    public function set(string $key, mixed $value, null|int|DateInterval $ttl = null): bool
    {
        $fullKey = $this->key($key);
        if ($ttl instanceof DateInterval) {
            $ttlSeconds = (int) $ttl->s + $ttl->i * 60 + $ttl->h * 3600 + $ttl->d * 86400;
        } else {
            $ttlSeconds = is_int($ttl) ? $ttl : 0;
        }
        if ($ttlSeconds > 0) {
            return $this->redis->setex($fullKey, $ttlSeconds, (string) $value);
        }
        return (bool) $this->redis->set($fullKey, (string) $value);
    }

    public function delete(string $key): bool
    {
        return $this->redis->del($this->key($key)) > 0;
    }

    public function clear(): bool
    {
        $cursor = 0;
        do {
            $result = $this->redis->scan($cursor, $this->prefix . '*', 100);
            if ($result !== false && $result !== []) {
                $this->redis->del(...$result);
            }
        } while ($cursor !== 0);
        return true;
    }

    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        $fullKeys = [];
        foreach ($keys as $k) {
            $fullKeys[] = $this->key((string) $k);
        }
        $vals = $this->redis->mGet($fullKeys);
        $out  = [];
        $i    = 0;
        foreach ($keys as $k) {
            $val = $vals[$i++] ?? null;
            $out[(string) $k] = ($val === false || $val === null) ? $default : $val;
        }
        return $out;
    }

    public function setMultiple(iterable $values, null|int|DateInterval $ttl = null): bool
    {
        foreach ($values as $k => $v) {
            $this->set((string) $k, $v, $ttl);
        }
        return true;
    }

    public function deleteMultiple(iterable $keys): bool
    {
        $fullKeys = [];
        foreach ($keys as $k) {
            $fullKeys[] = $this->key((string) $k);
        }
        if ($fullKeys === []) {
            return true;
        }
        $this->redis->del(...$fullKeys);
        return true;
    }

    public function has(string $key): bool
    {
        return $this->redis->exists($this->key($key)) > 0;
    }
}
