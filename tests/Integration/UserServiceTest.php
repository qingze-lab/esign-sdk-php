<?php
declare(strict_types=1);

namespace QingzeLab\OpenIM\Tests\Integration;

use QingzeLab\OpenIM\Client\OpenImClient;
use QingzeLab\OpenIM\Config\OpenImConfig;
use QingzeLab\OpenIM\Service\UserService;
use QingzeLab\OpenIM\Tests\Support\ArrayCache;
use PHPUnit\Framework\TestCase;
use Random\RandomException;

/**
 * Class UserServiceTest
 *
 * 使用真实 OpenIM 实例验证 UserService 的基本行为。
 */
final class UserServiceTest extends TestCase
{
    /**
     * 构建用于测试的 UserService 实例。
     *
     * @return UserService
     */
    private function createUserService(): UserService
    {
        $baseUrl   = (string) (getenv('OPEN_IM_API_URL') ?: 'http://localhost:10002');
        $adminUser = (string) (getenv('OPEN_IM_APP_ID') ?: 'imAdmin');
        $secret    = (string) (getenv('OPEN_IM_APP_SECRET') ?: 'openIM123');
        $client    = new OpenImClient(new OpenImConfig($baseUrl, $adminUser, $secret), new ArrayCache());
        return $client->users();
    }

    /**
     * 检查 OpenIM API 地址是否可访问。
     *
     * 不可达时跳过测试，避免误报失败。
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
     * 测试创建用户接口调用成功，并返回数组结构。
     *
     * @return void
     * @throws RandomException
     */
    public function testCreateUserReturnsArray(): void
    {
        $this->ensureServerReachable();

        $service = $this->createUserService();

        $userId = 'sdk_test_' . bin2hex(random_bytes(4));

        $response = $service->userRegister([
            [
                'userID'   => $userId,
                'nickname' => 'SDK 测试用户',
                'faceURL'  => '',
            ],
        ]);

        $this->assertIsArray($response);

        $updateResp = $service->updateUserInfoEx([
            'userID' => $userId,
            'ex'     => 'updated',
        ]);
        $this->assertIsArray($updateResp);
        $this->assertArrayHasKey('errCode', $updateResp);
        $this->assertContains((int) $updateResp['errCode'], [0, 1001]);

        $infosResp = $service->getUsersInfo([$userId]);
        $this->assertIsArray($infosResp);
        $this->assertArrayHasKey('errCode', $infosResp);
        $this->assertContains((int) $infosResp['errCode'], [0, 1001]);
    }

    /**
     * 获取用户 token 成功返回数据结构，且 token 为非空字符串（errCode=0 时）。
     *
     * @return void
     * @throws RandomException
     */
    public function testGetUserTokenReturnsData(): void
    {
        $this->ensureServerReachable();

        $service = $this->createUserService();

        $userId = 'sdk_ut_' . bin2hex(random_bytes(4));

        $service->userRegister([
            [
                'userID'   => $userId,
                'nickname' => '获取Token用户',
                'faceURL'  => '',
            ],
        ]);

        $resp = $service->getUserToken($userId, 1);

        $this->assertIsArray($resp);
        $this->assertArrayHasKey('errCode', $resp);
        $this->assertContains((int) $resp['errCode'], [0, 1001]);

        if ((int) $resp['errCode'] === 0) {
            $this->assertArrayHasKey('data', $resp);
            $this->assertIsArray($resp['data']);
            $this->assertArrayHasKey('token', $resp['data']);
            $this->assertIsString($resp['data']['token']);
            $this->assertNotSame('', $resp['data']['token']);
        }
    }
}
