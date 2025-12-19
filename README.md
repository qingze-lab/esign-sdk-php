# OpenIM PHP SDK

一个干净易用的 OpenIM REST API PHP SDK，提供 Token 管理、可靠的 HTTP 调用与常用领域服务封装（用户、群组、消息等）。

**特性**
- 轻量封装 OpenIM REST API，开箱即用
- 自动注入 `token` 与 `operationID` 请求头
- 支持可配置的 HTTP 重试与超时
- 与 PSR-16 缓存接口兼容，便于替换 Redis/Memcached
- 纯 PHP，无侵入，适合后端服务或脚本场景

**环境要求**
- PHP 8.2+
- Composer
- 可访问的 OpenIM Server（默认 `http://localhost:10002`）

**安装**
- `composer require qingze-lab/openim-sdk-php`

**快速开始（使用 OpenIMClient 简化初始化）**

```php
<?php
use QingzeLab\OpenIM\Client\OpenIMClient;
use QingzeLab\OpenIM\Config\OpenIMConfig;
use QingzeLab\OpenIM\Message\Content\TextContent;
use QingzeLab\OpenIM\Message\ContentType;
use QingzeLab\OpenIM\Tests\Support\ArrayCache;

require __DIR__ . '/vendor/autoload.php';

$client = new OpenIMClient(
    new OpenIMConfig('http://localhost:10002', 'imAdmin', 'openIM123'),
    new ArrayCache()
);

$client->users()->register('u_001', '用户001', '');
$resp = $client->messages()->sendSingle(
    'u_001',
    'u_002',
    ContentType::TEXT,
    (new TextContent())->setContent('hi')->toArray(),
    ['senderPlatformID' => 1]
);
```

**常用用法**
- 获取用户 Token
```php
$client->users()->getUserToken('u_001', 1);
```
- 更新用户信息
```php
$client->users()->updateUserInfoEx(['userID' => 'u_001', 'ex' => 'hello']);
```
- 发送消息
```php
$client->messages()->sendSingle(
    'u_001',
    'u_002',
    ContentType::TEXT,
    (new TextContent())->setContent('hi')->toArray(),
    ['senderPlatformID' => 1]
);
```
- 内容类与便捷方法发送消息
```php
use QingzeLab\OpenIM\Message\Content\TextContent;
use QingzeLab\OpenIM\Message\ContentType;
$client->messages()->sendTextSingle('u_001', 'u_002', (new TextContent())->setContent('hi'), [
    'senderPlatformID' => 1,
    'senderNickname' => '用户001',
]);
```
- 创建群组
```php
$client->groups()->createGroup('u_owner', ['u_001','u_002'], [
    'groupID' => 'g_001',
    'groupName' => '示例群',
    'groupType' => 2,
], []);
```
**Redis 缓存示例**
```php
use QingzeLab\OpenIM\Support\RedisCache;
$redisClass = '\\Redis';
$redis = new $redisClass();
$redis->connect('127.0.0.1', 6379);
$client = new OpenIMClient(new OpenIMConfig('http://localhost:10002', 'imAdmin', 'openIM123'), new RedisCache($redis, 'openim_token_'));
```

**配置说明**
- `OpenIMConfig` 构造参数：
  - `baseUrl` OpenIM API 地址
  - `adminUserId` 管理员用户 ID（如 `imAdmin`）
  - `adminSecret` 管理员鉴权凭证
  - `timeout` 请求超时（秒），默认 5.0
  - `connectTimeout` 连接超时（秒），默认 2.0
  - `retryMaxAttempts` 最大重试次数，默认 3
  - `retryDelayMs` 重试间隔毫秒，默认 200

**测试**
- `composer install`
- `vendor/bin/phpunit`

**示例**
- 参见 `examples/basic_usage.php` 与 `examples/redis_cache.php`

**许可证**
- MIT
