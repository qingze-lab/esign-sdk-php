<?php
declare(strict_types=1);

namespace QingzeLab\OpenIM\Tests\Integration;

use PHPUnit\Framework\TestCase;
use QingzeLab\OpenIM\Client\OpenImClient;
use QingzeLab\OpenIM\Config\OpenImConfig;
use QingzeLab\OpenIM\Tests\Support\RedisCache;

final class TokenCacheRedisTest extends TestCase
{
    private function ensureServerReachable(): void
    {
        $url = (string) (getenv('OPEN_IM_API_URL') ?: 'http://localhost:10002');
        $ch = curl_init($url);
        if ($ch === false) {
            $this->markTestSkipped('无法初始化 cURL。');
        }
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        $errno = curl_errno($ch);
        curl_close($ch);
        if ($errno !== 0) {
            $this->markTestSkipped('OpenIM API 地址不可访问，跳过 Redis 缓存集成测试。');
        }
    }

    private function ensureRedisReachable()
    {
        if (!extension_loaded('redis')) {
            $this->markTestSkipped('未安装 redis 扩展，跳过 Redis 缓存测试。');
        }
        $host = (string) (getenv('OPEN_IM_REDIS_HOST') ?: '127.0.0.1');
        $port = (int) (getenv('OPEN_IM_REDIS_PORT') ?: 6379);
        $redisClass = '\\Redis';
        $redis = new $redisClass();
        try {
            $redis->connect($host, $port, 1.0);
        } catch (\Throwable) {
            $this->markTestSkipped('Redis 无法连接，跳过 Redis 缓存测试。');
        }
        return $redis;
    }

    public function testGetTokenWithRedisCache(): void
    {
        $this->ensureServerReachable();
        $redis = $this->ensureRedisReachable();
        $redis->select((int) (getenv('OPEN_IM_REDIS_DB') ?: 0));
        $redis->flushDB();

        $config     = new OpenImConfig(
            (string) (getenv('OPEN_IM_API_URL') ?: 'http://localhost:10002'),
            (string) (getenv('OPEN_IM_APP_ID') ?: 'imAdmin'),
            (string) (getenv('OPEN_IM_APP_SECRET') ?: 'openIM123')
        );
        $client     = new OpenImClient($config, new RedisCache($redis, 'openim_token_'));

        $client->users()->userRegister();

        $this->assertTrue($redis->dbSize() > 0);
    }
}
