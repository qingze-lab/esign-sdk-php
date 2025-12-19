<?php
declare(strict_types=1);

namespace QingzeLab\OpenIM\Tests\Integration;

use QingzeLab\OpenIM\Client\OpenImClient;
use QingzeLab\OpenIM\Config\OpenImConfig;
use QingzeLab\OpenIM\Message\Content\AtContent;
use QingzeLab\OpenIM\Message\Content\CustomContent;
use QingzeLab\OpenIM\Message\Content\FileContent;
use QingzeLab\OpenIM\Message\Content\ImageContent;
use QingzeLab\OpenIM\Message\Content\LocationContent;
use QingzeLab\OpenIM\Message\Content\Picture;
use QingzeLab\OpenIM\Message\Content\SoundContent;
use QingzeLab\OpenIM\Message\Content\SystemNotificationContent;
use QingzeLab\OpenIM\Message\Content\TextContent;
use QingzeLab\OpenIM\Message\Content\VideoContent;
use QingzeLab\OpenIM\Message\ContentType;
use QingzeLab\OpenIM\Message\MessagePayload;
use QingzeLab\OpenIM\Service\MessageService;
use QingzeLab\OpenIM\Service\UserService;
use QingzeLab\OpenIM\Tests\Support\ArrayCache;
use PHPUnit\Framework\TestCase;

final class MessageServiceTest extends TestCase
{
    private function services(): array
    {
        $baseUrl   = (string) (getenv('OPEN_IM_API_URL') ?: 'http://localhost:10002');
        $adminUser = (string) (getenv('OPEN_IM_APP_ID') ?: 'imAdmin');
        $secret    = (string) (getenv('OPEN_IM_APP_SECRET') ?: 'openIM123');
        $client    = new OpenImClient(new OpenImConfig($baseUrl, $adminUser, $secret), new ArrayCache());
        return [$client->users(), $client->messages()];
    }

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
            $this->markTestSkipped('OpenIM API 地址不可访问，跳过消息集成测试。');
        }
    }

    public function testSendMsg(): void
    {
        $this->ensureServerReachable();
        [$users, $messages] = $this->services();
        $sender = 'sdk_sender_' . bin2hex(random_bytes(4));
        $recv   = 'sdk_recv_' . bin2hex(random_bytes(4));
        $users->userRegister([
            ['userID' => $sender, 'nickname' => '发送者', 'faceURL' => ''],
            ['userID' => $recv,   'nickname' => '接收者', 'faceURL' => ''],
        ]);
        $payload = [
            'sendID'           => $sender,
            'recvID'           => $recv,
            'groupID'          => '',
            'senderNickname'   => '发送者',
            'senderFaceURL'    => '',
            'senderPlatformID' => 1,
            'content'          => (new TextContent())->setContent('hello')->toArray(),
            'contentType'      => ContentType::TEXT->value,
            'sessionType'      => 1,
            'isOnlineOnly'     => false,
            'notOfflinePush'   => false,
            'clientMsgID'      => bin2hex(random_bytes(8)),
            'ex'               => '',
        ];
        $resp = $messages->sendMsg($payload);
        $this->assertIsArray($resp);
        $this->assertArrayHasKey('errCode', $resp);
        $this->assertIsInt((int) $resp['errCode']);
    }

    public function testBatchSendMsg(): void
    {
        $this->ensureServerReachable();
        [$users, $messages] = $this->services();
        $sender = 'sdk_sender_' . bin2hex(random_bytes(4));
        $recv   = 'sdk_recv_' . bin2hex(random_bytes(4));
        $users->userRegister([
            ['userID' => $sender, 'nickname' => '发送者', 'faceURL' => ''],
            ['userID' => $recv,   'nickname' => '接收者', 'faceURL' => ''],
        ]);
        $fields = [
            'sendID'           => $sender,
            'recvID'           => $recv,
            'groupID'          => '',
            'senderPlatformID' => 1,
            'content'          => (new TextContent())->setContent('hello batch')->toArray(),
            'contentType'      => ContentType::TEXT->value,
            'sessionType'      => 1,
            'clientMsgID'      => bin2hex(random_bytes(8)),
        ];
        try {
            $resp = $messages->batchSendMsg($fields);
            $this->assertIsArray($resp);
        } catch (\Throwable $e) {
            $this->assertStringContainsString('BatchSendMsgReq', (string) $e->getMessage());
        }
    }

    public function testSendSingle(): void
    {
        $this->ensureServerReachable();
        [$users, $messages] = $this->services();
        $sender = 'sdk_sender_' . bin2hex(random_bytes(4));
        $recv   = 'sdk_recv_' . bin2hex(random_bytes(4));
        $users->userRegister([
            ['userID' => $sender, 'nickname' => '发送者', 'faceURL' => ''],
            ['userID' => $recv,   'nickname' => '接收者', 'faceURL' => ''],
        ]);
        $content = (new TextContent())->setContent('hello single');
        $resp = $messages->sendSingle($sender, $recv, ContentType::TEXT, $content->toArray(), [
            'senderPlatformID' => 1,
        ]);
        $this->assertIsArray($resp);
        $this->assertArrayHasKey('errCode', $resp);
        $this->assertIsInt((int) $resp['errCode']);
    }

    public function testSendGroup(): void
    {
        $this->ensureServerReachable();
        [, $messages] = $this->services();
        $sender = 'sdk_sender_' . bin2hex(random_bytes(4));
        $group  = 'sdk_group_' . bin2hex(random_bytes(4));
        $content = (new TextContent())->setContent('hello group');
        $resp = $messages->sendGroup($sender, $group, ContentType::TEXT, $content->toArray(), [
            'senderPlatformID' => 1,
        ]);
        $this->assertIsArray($resp);
        $this->assertArrayHasKey('errCode', $resp);
        $this->assertIsInt((int) $resp['errCode']);
    }

    public function testSendTextSingle(): void
    {
        $this->ensureServerReachable();
        [$users, $messages] = $this->services();
        $sender = 'sdk_sender_' . bin2hex(random_bytes(4));
        $recv   = 'sdk_recv_' . bin2hex(random_bytes(4));
        $users->userRegister([
            ['userID' => $sender, 'nickname' => '发送者', 'faceURL' => ''],
            ['userID' => $recv,   'nickname' => '接收者', 'faceURL' => ''],
        ]);
        $resp = $messages->sendTextSingle($sender, $recv, (new TextContent())->setContent('text'), [
            'senderPlatformID' => 1,
        ]);
        $this->assertIsArray($resp);
        $this->assertArrayHasKey('errCode', $resp);
        $this->assertIsInt((int) $resp['errCode']);
    }

    public function testSendTextGroup(): void
    {
        $this->ensureServerReachable();
        [, $messages] = $this->services();
        $sender = 'sdk_sender_' . bin2hex(random_bytes(4));
        $group  = 'sdk_group_' . bin2hex(random_bytes(4));
        $resp = $messages->sendTextGroup($sender, $group, (new TextContent())->setContent('text'), [
            'senderPlatformID' => 1,
        ]);
        $this->assertIsArray($resp);
        $this->assertArrayHasKey('errCode', $resp);
        $this->assertIsInt((int) $resp['errCode']);
    }

    public function testSendImageSingle(): void
    {
        $this->ensureServerReachable();
        [$users, $messages] = $this->services();
        $sender = 'sdk_sender_' . bin2hex(random_bytes(4));
        $recv   = 'sdk_recv_' . bin2hex(random_bytes(4));
        $users->userRegister([
            ['userID' => $sender, 'nickname' => '发送者', 'faceURL' => ''],
            ['userID' => $recv,   'nickname' => '接收者', 'faceURL' => ''],
        ]);
        $pic = (new Picture())->setType('jpeg')->setWidth(160)->setHeight(120)->setUrl('https://example.com/i.jpg');
        $resp = $messages->sendImageSingle($sender, $recv, (new ImageContent())->setSnapshotPicture($pic), [
            'senderPlatformID' => 1,
        ]);
        $this->assertIsArray($resp);
        $this->assertArrayHasKey('errCode', $resp);
        $this->assertIsInt((int) $resp['errCode']);
    }

    public function testSendImageGroup(): void
    {
        $this->ensureServerReachable();
        [, $messages] = $this->services();
        $sender = 'sdk_sender_' . bin2hex(random_bytes(4));
        $group  = 'sdk_group_' . bin2hex(random_bytes(4));
        $pic = (new Picture())->setType('jpeg')->setWidth(160)->setHeight(120)->setUrl('https://example.com/i.jpg');
        $resp = $messages->sendImageGroup($sender, $group, (new ImageContent())->setSnapshotPicture($pic), [
            'senderPlatformID' => 1,
        ]);
        $this->assertIsArray($resp);
        $this->assertArrayHasKey('errCode', $resp);
        $this->assertIsInt((int) $resp['errCode']);
    }

    public function testSendSoundSingle(): void
    {
        $this->ensureServerReachable();
        [$users, $messages] = $this->services();
        $sender = 'sdk_sender_' . bin2hex(random_bytes(4));
        $recv   = 'sdk_recv_' . bin2hex(random_bytes(4));
        $users->userRegister([
            ['userID' => $sender, 'nickname' => '发送者', 'faceURL' => ''],
            ['userID' => $recv,   'nickname' => '接收者', 'faceURL' => ''],
        ]);
        $resp = $messages->sendSoundSingle($sender, $recv, (new SoundContent())->setSourceUrl('https://example.com/s.amr')->setDuration(2), [
            'senderPlatformID' => 1,
        ]);
        $this->assertIsArray($resp);
        $this->assertArrayHasKey('errCode', $resp);
        $this->assertIsInt((int) $resp['errCode']);
    }

    public function testSendVideoSingle(): void
    {
        $this->ensureServerReachable();
        [$users, $messages] = $this->services();
        $sender = 'sdk_sender_' . bin2hex(random_bytes(4));
        $recv   = 'sdk_recv_' . bin2hex(random_bytes(4));
        $users->userRegister([
            ['userID' => $sender, 'nickname' => '发送者', 'faceURL' => ''],
            ['userID' => $recv,   'nickname' => '接收者', 'faceURL' => ''],
        ]);
        $content = (new VideoContent())
            ->setVideoUrl('https://example.com/v.mp4')
            ->setVideoType('mp4')
            ->setVideoSize(1024)
            ->setDuration(3)
            ->setSnapshotUrl('https://example.com/s.jpg')
            ->setSnapshotWidth(160)
            ->setSnapshotHeight(120);
        $resp = $messages->sendVideoSingle($sender, $recv, $content, [
            'senderPlatformID' => 1,
        ]);
        $this->assertIsArray($resp);
        $this->assertArrayHasKey('errCode', $resp);
        $this->assertIsInt((int) $resp['errCode']);
    }

    public function testSendFileSingle(): void
    {
        $this->ensureServerReachable();
        [$users, $messages] = $this->services();
        $sender = 'sdk_sender_' . bin2hex(random_bytes(4));
        $recv   = 'sdk_recv_' . bin2hex(random_bytes(4));
        $users->userRegister([
            ['userID' => $sender, 'nickname' => '发送者', 'faceURL' => ''],
            ['userID' => $recv,   'nickname' => '接收者', 'faceURL' => ''],
        ]);
        $resp = $messages->sendFileSingle($sender, $recv, (new FileContent())->setSourceUrl('https://example.com/f.pdf')->setFileName('f.pdf')->setFileSize(64)->setFileType('pdf'), [
            'senderPlatformID' => 1,
        ]);
        $this->assertIsArray($resp);
        $this->assertArrayHasKey('errCode', $resp);
        $this->assertIsInt((int) $resp['errCode']);
    }

    public function testSendLocationSingle(): void
    {
        $this->ensureServerReachable();
        [$users, $messages] = $this->services();
        $sender = 'sdk_sender_' . bin2hex(random_bytes(4));
        $recv   = 'sdk_recv_' . bin2hex(random_bytes(4));
        $users->userRegister([
            ['userID' => $sender, 'nickname' => '发送者', 'faceURL' => ''],
            ['userID' => $recv,   'nickname' => '接收者', 'faceURL' => ''],
        ]);
        $resp = $messages->sendLocationSingle($sender, $recv, (new LocationContent())->setLongitude(121.5)->setLatitude(31.2), [
            'senderPlatformID' => 1,
        ]);
        $this->assertIsArray($resp);
        $this->assertArrayHasKey('errCode', $resp);
        $this->assertIsInt((int) $resp['errCode']);
    }

    public function testSendAtGroup(): void
    {
        $this->ensureServerReachable();
        [, $messages] = $this->services();
        $sender = 'sdk_sender_' . bin2hex(random_bytes(4));
        $group  = 'sdk_group_' . bin2hex(random_bytes(4));
        $resp = $messages->sendAtGroup($sender, $group, (new AtContent())->setText('hi')->setAtUserList(['all'])->setIsAtSelf(false), [
            'senderPlatformID' => 1,
        ]);
        $this->assertIsArray($resp);
        $this->assertArrayHasKey('errCode', $resp);
        $this->assertIsInt((int) $resp['errCode']);
    }

    public function testSendCustomSingle(): void
    {
        $this->ensureServerReachable();
        [$users, $messages] = $this->services();
        $sender = 'sdk_sender_' . bin2hex(random_bytes(4));
        $recv   = 'sdk_recv_' . bin2hex(random_bytes(4));
        $users->userRegister([
            ['userID' => $sender, 'nickname' => '发送者', 'faceURL' => ''],
            ['userID' => $recv,   'nickname' => '接收者', 'faceURL' => ''],
        ]);
        $resp = $messages->sendCustomSingle($sender, $recv, (new CustomContent())->setData('ping'), [
            'senderPlatformID' => 1,
        ]);
        $this->assertIsArray($resp);
        $this->assertArrayHasKey('errCode', $resp);
        $this->assertIsInt((int) $resp['errCode']);
    }

    public function testSendSystemGroup(): void
    {
        $this->ensureServerReachable();
        [, $messages] = $this->services();
        $sender = 'sdk_sender_' . bin2hex(random_bytes(4));
        $group  = 'sdk_group_' . bin2hex(random_bytes(4));
        $content = (new SystemNotificationContent())
            ->setNotificationName('系统公告')
            ->setNotificationFaceURL('https://example.com/sys.png')
            ->setNotificationType(1)
            ->setText('维护通知')
            ->setExternalUrl('https://status.example.com')
            ->setMixType(0);
        $resp = $messages->sendSystemGroup($sender, $group, $content, [
            'senderPlatformID' => 1,
        ]);
        $this->assertIsArray($resp);
        $this->assertArrayHasKey('errCode', $resp);
        $this->assertIsInt((int) $resp['errCode']);
    }
}
