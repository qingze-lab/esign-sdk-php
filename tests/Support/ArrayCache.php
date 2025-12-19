<?php
declare(strict_types=1);

namespace QingzeLab\OpenIM\Tests\Support;

use Psr\SimpleCache\CacheInterface;

/**
 * Class ArrayCache
 *
 * 一个简单的基于数组的内存缓存实现，仅用于测试环境。
 */
final class ArrayCache implements CacheInterface
{
    /**
     * @var array<string, mixed>
     */
    private array $store = [];

    /**
     * 获取缓存项。
     *
     * @param string $key     缓存键
     * @param mixed  $default 默认值
     *
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->store[$key] ?? $default;
    }

    /**
     * 设置缓存项。
     *
     * @param string                $key   缓存键
     * @param mixed                 $value 缓存值
     * @param null|int|\DateInterval $ttl  过期时间
     *
     * @return bool
     */
    public function set(string $key, mixed $value, null|int|\DateInterval $ttl = null): bool
    {
        $this->store[$key] = $value;

        return true;
    }

    /**
     * 删除缓存项。
     *
     * @param string $key 缓存键
     *
     * @return bool
     */
    public function delete(string $key): bool
    {
        unset($this->store[$key]);

        return true;
    }

    /**
     * 清空所有缓存。
     *
     * @return bool
     */
    public function clear(): bool
    {
        $this->store = [];

        return true;
    }

    /**
     * 批量获取缓存项。
     *
     * @param iterable<string> $keys    缓存键集合
     * @param mixed            $default 默认值
     *
     * @return iterable<string, mixed>
     */
    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        $results = [];

        foreach ($keys as $key) {
            $results[$key] = $this->get($key, $default);
        }

        return $results;
    }

    /**
     * 批量设置缓存项。
     *
     * @param iterable<string, mixed>   $values 键值对集合
     * @param null|int|\DateInterval $ttl    过期时间
     *
     * @return bool
     */
    public function setMultiple(iterable $values, null|int|\DateInterval $ttl = null): bool
    {
        foreach ($values as $key => $value) {
            $this->set((string) $key, $value, $ttl);
        }

        return true;
    }

    /**
     * 批量删除缓存项。
     *
     * @param iterable<string> $keys 缓存键集合
     *
     * @return bool
     */
    public function deleteMultiple(iterable $keys): bool
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }

        return true;
    }

    /**
     * 判断缓存键是否存在。
     *
     * @param string $key 缓存键
     *
     * @return bool
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->store);
    }
}
