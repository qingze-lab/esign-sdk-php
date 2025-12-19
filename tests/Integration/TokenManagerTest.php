<?php
declare(strict_types=1);

namespace QingzeLab\OpenIM\Tests\Integration;

use QingzeLab\OpenIM\Client\OpenImClient;
use QingzeLab\OpenIM\Config\OpenImConfig;
use QingzeLab\OpenIM\Exception\OpenIMException;
use QingzeLab\OpenIM\Tests\Support\ArrayCache;
use PHPUnit\Framework\TestCase;

/**
 * Class TokenManagerTest
 *
 * 使用实际 OpenIM 后端验证 TokenManager 的获取 Token 能力。
 */
final class TokenManagerTest extends TestCase
{
    /**
     * 构建用于测试的 OpenImConfig 实例。
     *
     * @return OpenImConfig
     */
    private function createConfig(): OpenImConfig
    {
        $baseUrl   = (string) (getenv('OPEN_IM_API_URL') ?: 'http://localhost:10002');
        $adminUser = (string) (getenv('OPEN_IM_APP_ID') ?: 'imAdmin');
        $secret    = (string) (getenv('OPEN_IM_APP_SECRET') ?: 'openIM123');

        return new OpenImConfig($baseUrl, $adminUser, $secret);
    }

    /**
     * 检查 OpenIM API 地址是否可访问。
     *
     * 无法访问时跳过集成测试，避免误报失败。
     *
     * @return void
     */
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
            $this->markTestSkipped('OpenIM API 地址不可访问，跳过集成测试。');
        }
    }

    /**
     * 测试通过 TokenManager 获取管理员 Token。
     *
     * @return void
     */
    public function testAdminTokenCachedViaClient(): void
    {
        $this->ensureServerReachable();

        $config   = $this->createConfig();
        $cache    = new ArrayCache();
        $client   = new OpenImClient($config, $cache);

        $client->users()->userRegister();

        $token = $cache->get('openim_token_' . $config->getAdminUserId());
        $this->assertIsString($token);
        $this->assertNotSame('', (string) $token);
    }

    /**
     * 测试强制刷新 Token 能够返回新的非空 Token。
     *
     * @return void
     */
    public function testInvalidSecretThrowsException(): void
    {
        $this->ensureServerReachable();

        $config = new OpenImConfig(
            (string) (getenv('OPEN_IM_API_URL') ?: 'http://localhost:10002'),
            (string) (getenv('OPEN_IM_APP_ID') ?: 'imAdmin'),
            'invalid_secret'
        );

        $client = new OpenImClient($config, new ArrayCache());

        $this->expectException(OpenImException::class);

        $client->users()->userRegister();
    }

    
}
