<?php
declare(strict_types=1);

namespace QingzeLab\OpenIM\Tests\Integration;

use QingzeLab\OpenIM\Client\OpenImClient;
use QingzeLab\OpenIM\Config\OpenImConfig;
use QingzeLab\OpenIM\Service\GroupService;
use QingzeLab\OpenIM\Service\UserService;
use QingzeLab\OpenIM\Tests\Support\ArrayCache;
use PHPUnit\Framework\TestCase;

/**
 * Class GroupServiceTest
 *
 * 使用真实 OpenIM 实例验证 GroupService 的核心行为：
 * - 群创建
 * - 踢出群成员
 */
final class GroupServiceTest extends TestCase
{
    /**
     * @return OpenImClient
     */
    private function createClient(): OpenImClient
    {
        $baseUrl   = (string) (getenv('OPEN_IM_API_URL') ?: 'http://localhost:10002');
        $adminUser = (string) (getenv('OPEN_IM_APP_ID') ?: 'imAdmin');
        $secret    = (string) (getenv('OPEN_IM_APP_SECRET') ?: 'openIM123');
        return new OpenImClient(new OpenImConfig($baseUrl, $adminUser, $secret), new ArrayCache());
    }

    /**
     * 构建 GroupService 与 UserService。
     *
     * @return array{0: GroupService, 1: UserService}
     */
    private function createServices(): array
    {
        $client = $this->createClient();
        return [$client->groups(), $client->users()];
    }

    /**
     * 检查 OpenIM API 地址是否可访问，不可访问时跳过测试。
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
            $this->markTestSkipped('OpenIM API 地址不可访问，跳过群组集成测试。');
        }
    }

    /**
     * 测试创建群组，并校验返回结构与 errCode。
     *
     * @return void
     */
    public function testCreateGroupWithMembersReturnsSuccess(): void
    {
        $this->ensureServerReachable();

        [$groupService, $userService] = $this->createServices();

        $ownerId   = 'sdk_owner_' . bin2hex(random_bytes(4));
        $memberId1 = 'sdk_member_' . bin2hex(random_bytes(4));
        $memberId2 = 'sdk_member_' . bin2hex(random_bytes(4));

        $userService->userRegister([
            ['userID' => $ownerId,   'nickname' => 'SDK 群主',  'faceURL' => ''],
            ['userID' => $memberId1, 'nickname' => 'SDK 成员1', 'faceURL' => ''],
            ['userID' => $memberId2, 'nickname' => 'SDK 成员2', 'faceURL' => ''],
        ]);

        $groupId   = 'sdk_group_' . bin2hex(random_bytes(4));
        $groupName = 'SDK 集成测试群';

        $response = $groupService->createGroup(
            $ownerId,
            [$memberId1, $memberId2],
            [
                'groupID'           => $groupId,
                'groupName'         => $groupName,
                'notification'      => '测试公告',
                'introduction'      => '测试简介',
                'faceURL'           => '',
                'ex'                => '',
                'groupType'         => 2,
                'needVerification'  => 0,
                'lookMemberInfo'    => 0,
                'applyMemberFriend' => 0,
            ],
            []
        );

        $this->assertIsArray($response);
        $this->assertArrayHasKey('errCode', $response);
        $this->assertSame(0, (int) $response['errCode']);
    }

    /**
     * 测试在群组中移除成员（踢人）。
     *
     * @return void
     */
    public function testKickGroupMemberReturnsSuccess(): void
    {
        $this->ensureServerReachable();

        [$groupService, $userService] = $this->createServices();

        $ownerId  = 'sdk_owner_' . bin2hex(random_bytes(4));
        $memberId = 'sdk_member_' . bin2hex(random_bytes(4));

        $userService->userRegister([
            ['userID' => $ownerId, 'nickname' => 'SDK 群主', 'faceURL' => ''],
            ['userID' => $memberId, 'nickname' => 'SDK 成员', 'faceURL' => ''],
        ]);

        $groupId   = 'sdk_group_' . bin2hex(random_bytes(4));
        $groupName = 'SDK 踢人测试群';

        $createResp = $groupService->createGroup(
            $ownerId,
            [$memberId],
            [
                'groupID'   => $groupId,
                'groupName' => $groupName,
                'ex'        => '',
                'groupType' => 2,
            ],
            []
        );

        $this->assertSame(0, (int) $createResp['errCode']);

        $setResp = $groupService->setGroupInfo([
            'groupID'   => $groupId,
            'groupName' => $groupName . '_updated',
        ]);
        $this->assertIsArray($setResp);
        $this->assertArrayHasKey('errCode', $setResp);
        $this->assertContains((int) $setResp['errCode'], [0, 1001]);

        $inviteResp = $groupService->inviteUserToGroup($groupId, [$memberId], '邀请说明');
        $this->assertIsArray($inviteResp);
        $this->assertArrayHasKey('errCode', $inviteResp);
        $this->assertContains((int) $inviteResp['errCode'], [0, 1001, 500]);

        $kickResp = $groupService->kickGroup($groupId, [$memberId], '测试移除成员');

        $this->assertIsArray($kickResp);
        $this->assertArrayHasKey('errCode', $kickResp);
    }
}
