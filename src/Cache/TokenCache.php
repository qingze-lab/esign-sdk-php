<?php
declare(strict_types = 1);

namespace QingzeLab\OpenIM\Cache;

use Psr\SimpleCache\CacheInterface;

/**
 * Class TokenCache
 *
 * 基于 PSR-16 的 Token 缓存封装，方便替换为 Redis / 文件 / 内存等实现。
 */
final class TokenCache
{
    /**
     * @var CacheInterface
     */
    private CacheInterface $cache;

    /**
     * @var string
     */
    private string $cacheKeyPrefix;

    /**
     * TokenCache constructor.
     *
     * @param CacheInterface $cache          PSR-16 缓存实现
     * @param string         $cacheKeyPrefix Token 缓存键前缀
     */
    public function __construct(CacheInterface $cache, string $cacheKeyPrefix = 'openim_token_')
    {
        $this->cache          = $cache;
        $this->cacheKeyPrefix = $cacheKeyPrefix;
    }

    /**
     * 从缓存中获取 Token。
     *
     * @param string $adminUserId APP 管理员 userID
     *
     * @return string|null 返回 Token 或 null（不存在 / 已过期）
     */
    public function getToken(string $adminUserId): ?string
    {
        $key = $this->buildKey($adminUserId);

        /** @var string|null $token */
        $token = $this->cache->get($key);

        return $token;
    }

    /**
     * 将 Token 写入缓存。
     *
     * @param string $adminUserId APP 管理员 userID
     * @param string $token       Token 字符串
     * @param int    $ttlSeconds  有效期秒数（建议略小于实际过期时间）
     *
     * @return void
     */
    public function setToken(string $adminUserId, string $token, int $ttlSeconds): void
    {
        $key = $this->buildKey($adminUserId);

        $this->cache->set($key, $token, $ttlSeconds);
    }

    /**
     * 构建缓存键。
     *
     * @param string $adminUserId APP 管理员 userID
     *
     * @return string
     */
    private function buildKey(string $adminUserId): string
    {
        return $this->cacheKeyPrefix . $adminUserId;
    }
}
