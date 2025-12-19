<?php
declare(strict_types = 1);

use QingzeLab\OpenIM\Client\OpenImClient;
use QingzeLab\OpenIM\Config\OpenImConfig;
use QingzeLab\OpenIM\Message\Content\TextContent;
use QingzeLab\OpenIM\Message\ContentType;
use QingzeLab\OpenIM\Tests\Support\RedisCache;

require __DIR__ . '/../vendor/autoload.php';

$baseUrl   = (string) (getenv('OPEN_IM_API_URL') ?: 'http://localhost:10002');
$adminUser = (string) (getenv('OPEN_IM_APP_ID') ?: 'imAdmin');
$secret    = (string) (getenv('OPEN_IM_APP_SECRET') ?: 'openIM123');

$config = new OpenImConfig($baseUrl, $adminUser, $secret);

$redisClass = '\\Redis';
$redis = new $redisClass();
$redis->connect((string) (getenv('OPEN_IM_REDIS_HOST') ?: '127.0.0.1'), (int) (getenv('OPEN_IM_REDIS_PORT') ?: 6379));
$redis->select((int) (getenv('OPEN_IM_REDIS_DB') ?: 0));

$client = new OpenImClient($config, new RedisCache($redis, 'openim_token_'));

$sender = 'sdk_sender_' . bin2hex(random_bytes(4));
$recv   = 'sdk_recv_' . bin2hex(random_bytes(4));

// 调用 users() 注册用户，然后通过 messages() 发送消息
$client->users()->userRegister([
    ['userID' => $sender, 'nickname' => '发送者', 'faceURL' => ''],
    ['userID' => $recv,   'nickname' => '接收者', 'faceURL' => ''],
]);

$resp = $client->messages()->sendSingle(
    $sender,
    $recv,
    ContentType::TEXT,
    (new TextContent())->setContent('hello openim with redis')->toArray(),
    ['senderPlatformID' => 1]
);

print_r($resp);
