# 消息构建器使用指南

## 文本消息（单聊）
```php
use QingzeLab\OpenIM\Service\MessageService;
use QingzeLab\OpenIM\Message\Content\TextContentBuilder;
$msg = new MessageService($http, $token);
$content = (new TextContentBuilder())->content('hello openim')->build();
$resp = $msg->sendTextSingle('u_001', 'u_002', $content, [
    'senderPlatformID' => 1,
    'senderNickname' => '用户001',
    'isOnlineOnly' => false,
    'notOfflinePush' => false,
]);
```

## 图片消息（群聊）
```php
use QingzeLab\OpenIM\Message\Content\ImageContentBuilder;
use QingzeLab\OpenIM\Message\Content\PictureBuilder;
$img = (new ImageContentBuilder())
    ->sourcePicture((new PictureBuilder())->type('jpeg')->size(102400)->width(800)->height(600)->url('https://example.com/origin.jpg')->build())
    ->bigPicture((new PictureBuilder())->type('jpeg')->size(51200)->width(400)->height(300)->url('https://example.com/big.jpg')->build())
    ->snapshotPicture((new PictureBuilder())->type('jpeg')->size(10240)->width(160)->height(120)->url('https://example.com/snapshot.jpg')->build())
    ->build();
$resp = $msg->sendImageGroup('u_001', 'g_001', $img, [
    'senderPlatformID' => 1,
]);
```

## 语音消息（单聊）
```php
use QingzeLab\OpenIM\Message\Content\SoundContentBuilder;
$sound = (new SoundContentBuilder())
    ->sourceUrl('https://example.com/sound.amr')
    ->duration(5)
    ->dataSize(20480)
    ->soundType('amr')
    ->build();
$resp = $msg->sendSoundSingle('u_001', 'u_002', $sound, []);
```

## 视频消息（单聊）
```php
use QingzeLab\OpenIM\Message\Content\VideoContentBuilder;
$video = (new VideoContentBuilder())
    ->videoUrl('https://example.com/video.mp4')
    ->videoType('mp4')
    ->videoSize(10485760)
    ->duration(10)
    ->snapshotUrl('https://example.com/snapshot.jpg')
    ->snapshotWidth(320)
    ->snapshotHeight(240)
    ->build();
$resp = $msg->sendVideoSingle('u_001', 'u_002', $video, []);
```

## 文件消息（单聊）
```php
use QingzeLab\OpenIM\Message\Content\FileContentBuilder;
$file = (new FileContentBuilder())
    ->sourceUrl('https://example.com/file.pdf')
    ->fileName('文档.pdf')
    ->fileSize(4096)
    ->fileType('pdf')
    ->build();
$resp = $msg->sendFileSingle('u_001', 'u_002', $file, []);
```

## 位置消息（单聊）
```php
use QingzeLab\OpenIM\Message\Content\LocationContentBuilder;
$loc = (new LocationContentBuilder())
    ->description('公司总部')
    ->longitude(116.397128)
    ->latitude(39.916527)
    ->build();
$resp = $msg->sendLocationSingle('u_001', 'u_002', $loc, []);
```

## @消息（群聊）
```php
use QingzeLab\OpenIM\Message\Content\AtContentBuilder;
$at = (new AtContentBuilder())
    ->text('请查看最新公告')
    ->atUserList(['u_002', 'u_003'])
    ->isAtSelf(false)
    ->build();
$resp = $msg->sendAtGroup('u_001', 'g_001', $at, []);
```

## 自定义消息（单聊）
```php
use QingzeLab\OpenIM\Message\Content\CustomContentBuilder;
$custom = (new CustomContentBuilder())
    ->data(json_encode(['type' => 'ping', 'ts' => time()], JSON_UNESCAPED_UNICODE))
    ->build();
$resp = $msg->sendCustomSingle('u_001', 'u_002', $custom, []);
```

## 系统通知（群聊）
```php
use QingzeLab\OpenIM\Message\Content\SystemNotificationContentBuilder;
use QingzeLab\OpenIM\Message\Content\ImageContentBuilder;
use QingzeLab\OpenIM\Message\Content\PictureBuilder;
$pic = (new ImageContentBuilder())
    ->sourcePicture((new PictureBuilder())->type('jpeg')->size(10240)->width(160)->height(120)->url('https://example.com/pic.jpg')->build())
    ->build();
$sys = (new SystemNotificationContentBuilder())
    ->notificationName('notification')
    ->notificationFaceURL('https://example.com/face.jpg')
    ->notificationType(1)
    ->text('hello!')
    ->mixType(0)
    ->pictureElem($pic)
    ->build();
$resp = $msg->sendSystemGroup('u_001', 'g_001', $sys, []);
```
