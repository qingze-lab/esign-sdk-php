<?php
declare(strict_types=1);

use Psr\SimpleCache\CacheInterface;
use QingzeLab\OpenIM\Client\OpenIMClient;
use QingzeLab\OpenIM\Config\OpenIMConfig;
use QingzeLab\OpenIM\Message\Content\TextContent;
use QingzeLab\OpenIM\Message\ContentType;

require __DIR__ . '/../vendor/autoload.php';

final class ArrayCache implements CacheInterface
{
    private array $store = [];
    public function get(string $key, mixed $default = null): mixed { return $this->store[$key] ?? $default; }
    public function set(string $key, mixed $value, null|int|\DateInterval $ttl = null): bool { $this->store[$key] = $value; return true; }
    public function delete(string $key): bool { unset($this->store[$key]); return true; }
    public function clear(): bool { $this->store = []; return true; }
    public function getMultiple(iterable $keys, mixed $default = null): iterable { $r = []; foreach ($keys as $k) { $r[$k] = $this->get((string) $k, $default); } return $r; }
    public function setMultiple(iterable $values, null|int|\DateInterval $ttl = null): bool { foreach ($values as $k => $v) { $this->set((string) $k, $v, $ttl); } return true; }
    public function deleteMultiple(iterable $keys): bool { foreach ($keys as $k) { $this->delete((string) $k); } return true; }
    public function has(string $key): bool { return array_key_exists($key, $this->store); }
}

$client = new OpenIMClient(
    new OpenIMConfig('http://127.0.0.1:10002', 'imAdmin', 'your_admin_secret'),
    new ArrayCache()
);

$client->users()->userRegister([
    ['userID' => 'demo_user_001', 'nickname' => '示例用户', 'faceURL' => ''],
    ['userID' => 'demo_user_002', 'nickname' => '示例用户2', 'faceURL' => ''],
]);
$resp = $client->messages()->sendSingle(
    'demo_user_001',
    'demo_user_002',
    ContentType::TEXT,
    (new TextContent())->setContent('hello')->toArray(),
    ['senderPlatformID' => 1]
);

var_dump($resp);
