<?php
declare(strict_types = 1);

namespace QingzeLab\OpenIM\Service;

use QingzeLab\OpenIM\Message\Content\AtContent;
use QingzeLab\OpenIM\Message\Content\CustomContent;
use QingzeLab\OpenIM\Message\Content\FileContent;
use QingzeLab\OpenIM\Message\Content\ImageContent;
use QingzeLab\OpenIM\Message\Content\LocationContent;
use QingzeLab\OpenIM\Message\Content\SoundContent;
use QingzeLab\OpenIM\Message\Content\SystemNotificationContent;
use QingzeLab\OpenIM\Message\Content\TextContent;
use QingzeLab\OpenIM\Message\Content\VideoContent;
use QingzeLab\OpenIM\Message\ContentType;
use QingzeLab\OpenIM\Message\MessagePayload;
use Random\RandomException;

/**
 * Class MessageService
 *
 * 消息领域服务。
 */
final class MessageService extends AbstractService
{
    /**
     * 模拟身份向指定用户或群组发送消息，也可用于从其他平台导入历史记录。
     *
     * @param array<string, mixed> $fields
     * @return array<string, mixed>
     */
    public function sendMsg(array $fields): array
    {
        return $this->post('/msg/send_msg', $fields);
    }

    /**
     * 批量发送消息。
     *
     * @param array $fields
     * @return array
     */
    public function batchSendMsg(array $fields): array
    {
        return $this->post('/msg/batch_send_msg', $fields);
    }

    /**
     * 发送单聊消息（通用）。
     *
     * @param string               $sendID
     * @param string               $recvID
     * @param ContentType          $type
     * @param array<string, mixed> $content
     * @param array<string, mixed> $opts
     * @return array<string, mixed>
     * @throws RandomException
     */
    public function sendSingle(string $sendID, string $recvID, ContentType $type, array $content, array $opts = []): array
    {
        $payload = MessagePayload::single($sendID, $recvID, $type, $content, $opts)->toArray();
        return $this->sendMsg($payload);
    }

    /**
     * 发送群聊消息（通用）。
     *
     * @param string               $sendID
     * @param string               $groupID
     * @param ContentType          $type
     * @param array<string, mixed> $content
     * @param array<string, mixed> $opts
     * @return array<string, mixed>
     * @throws RandomException
     */
    public function sendGroup(string $sendID, string $groupID, ContentType $type, array $content, array $opts = []): array
    {
        $payload = MessagePayload::group($sendID, $groupID, $type, $content, $opts)->toArray();
        return $this->sendMsg($payload);
    }

    /**
     * 发送文本（单聊）。
     *
     * @param string      $sendID
     * @param string      $recvID
     * @param TextContent $content
     * @param array       $opts
     * @return array<string, mixed>
     * @throws RandomException
     */
    public function sendTextSingle(string $sendID, string $recvID, TextContent $content, array $opts = []): array
    {
        return $this->sendSingle($sendID, $recvID, ContentType::TEXT, $content->toArray(), $opts);
    }

    /**
     * 发送文本（群聊）。
     *
     * @param string      $sendID
     * @param string      $groupID
     * @param TextContent $content
     * @param array       $opts
     * @return array<string, mixed>
     * @throws RandomException
     */
    public function sendTextGroup(string $sendID, string $groupID, TextContent $content, array $opts = []): array
    {
        return $this->sendGroup($sendID, $groupID, ContentType::TEXT, $content->toArray(), $opts);
    }

    /**
     * 发送图片（单聊）。
     *
     * @param string       $sendID
     * @param string       $recvID
     * @param ImageContent $content
     * @param array        $opts
     * @return array<string, mixed>
     * @throws RandomException
     */
    public function sendImageSingle(string $sendID, string $recvID, ImageContent $content, array $opts = []): array
    {
        return $this->sendSingle($sendID, $recvID, ContentType::IMAGE, $content->toArray(), $opts);
    }

    /**
     * 发送图片（群聊）。
     *
     * @param string       $sendID
     * @param string       $groupID
     * @param ImageContent $content
     * @param array        $opts
     * @return array<string, mixed>
     * @throws RandomException
     */
    public function sendImageGroup(string $sendID, string $groupID, ImageContent $content, array $opts = []): array
    {
        return $this->sendGroup($sendID, $groupID, ContentType::IMAGE, $content->toArray(), $opts);
    }

    /**
     * 发送语音（单聊）。
     *
     * @param string       $sendID
     * @param string       $recvID
     * @param SoundContent $content
     * @param array        $opts
     * @return array<string, mixed>
     * @throws RandomException
     */
    public function sendSoundSingle(string $sendID, string $recvID, SoundContent $content, array $opts = []): array
    {
        return $this->sendSingle($sendID, $recvID, ContentType::SOUND, $content->toArray(), $opts);
    }

    /**
     * 发送视频（单聊）。
     *
     * @param string       $sendID
     * @param string       $recvID
     * @param VideoContent $content
     * @param array        $opts
     * @return array<string, mixed>
     * @throws RandomException
     */
    public function sendVideoSingle(string $sendID, string $recvID, VideoContent $content, array $opts = []): array
    {
        return $this->sendSingle($sendID, $recvID, ContentType::VIDEO, $content->toArray(), $opts);
    }

    /**
     * 发送文件（单聊）。
     *
     * @param string      $sendID
     * @param string      $recvID
     * @param FileContent $content
     * @param array       $opts
     * @return array<string, mixed>
     * @throws RandomException
     */
    public function sendFileSingle(string $sendID, string $recvID, FileContent $content, array $opts = []): array
    {
        return $this->sendSingle($sendID, $recvID, ContentType::FILE, $content->toArray(), $opts);
    }

    /**
     * 发送位置（单聊）。
     *
     * @param string          $sendID
     * @param string          $recvID
     * @param LocationContent $content
     * @param array           $opts
     * @return array<string, mixed>
     * @throws RandomException
     */
    public function sendLocationSingle(string $sendID, string $recvID, LocationContent $content, array $opts = []): array
    {
        return $this->sendSingle($sendID, $recvID, ContentType::LOCATION, $content->toArray(), $opts);
    }

    /**
     * 发送 @ 消息（群聊）。
     *
     * @param string    $sendID
     * @param string    $groupID
     * @param AtContent $content
     * @param array     $opts
     * @return array<string, mixed>
     * @throws RandomException
     */
    public function sendAtGroup(string $sendID, string $groupID, AtContent $content, array $opts = []): array
    {
        return $this->sendGroup($sendID, $groupID, ContentType::AT, $content->toArray(), $opts);
    }

    /**
     * 发送自定义（单聊）。
     *
     * @param string        $sendID
     * @param string        $recvID
     * @param CustomContent $content
     * @param array         $opts
     * @return array<string, mixed>
     * @throws RandomException
     */
    public function sendCustomSingle(string $sendID, string $recvID, CustomContent $content, array $opts = []): array
    {
        return $this->sendSingle($sendID, $recvID, ContentType::CUSTOM, $content->toArray(), $opts);
    }

    /**
     * 发送系统通知（群聊）。
     *
     * @param string                    $sendID
     * @param string                    $groupID
     * @param SystemNotificationContent $content
     * @param array                     $opts
     * @return array<string, mixed>
     * @throws RandomException
     */
    public function sendSystemGroup(string $sendID, string $groupID, SystemNotificationContent $content, array $opts = []): array
    {
        return $this->sendGroup($sendID, $groupID, ContentType::SYSTEM, $content->toArray(), $opts);
    }
}
