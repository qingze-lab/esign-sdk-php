<?php
declare(strict_types = 1);

namespace QingzeLab\OpenIM\Message;

use InvalidArgumentException;
use Random\RandomException;

/**
 * 消息载荷
 *
 * 对应 OpenIM 发送接口的载荷结构，包含会话、发送者、内容等字段。
 * 参考 OpenIM 官方文档：消息发送接口字段说明。
 */
class MessagePayload
{
    /**
     * 发送者用户 ID（必填） 对应字段：`sendID`
     * @var string|null
     */
    private ?string $sendID = null;

    /**
     * 接收者用户 ID（单聊时必填）对应字段：`recvID`
     * @var string|null
     */
    private ?string $recvID = null;

    /**
     * 群组 ID（群聊时必填） 对应字段：`groupID`
     * @var string|null
     */
    private ?string $groupID = null;

    /**
     * 发送者昵称 对应字段：`senderNickname`
     * @var string
     */
    private string $senderNickname = '';

    /**
     * 发送者头像 对应字段：`senderFaceURL`
     * @var string
     */
    private string $senderFaceURL = '';

    /**
     * 发送平台 ID 对应字段：`senderPlatformID`
     * @var int
     */
    private int $senderPlatformID = 1;

    /**
     * 内容类型（必填） 对应字段：`contentType`
     * @var ContentType|null
     */
    private ?ContentType $contentType = null;

    /**
     * 会话类型（必填） 对应字段：`sessionType`
     * @var SessionType|null
     */
    private ?SessionType $sessionType = null;

    /**
     * 仅在线发送 对应字段：`isOnlineOnly`
     * @var bool
     */
    private bool $isOnlineOnly = false;

    /**
     * 不推送离线消息 对应字段：`notOfflinePush`
     * @var bool
     */
    private bool $notOfflinePush = false;

    /**
     * 客户端消息 ID 对应字段：`clientMsgID`
     * @var string|null
     */
    private ?string $clientMsgID = null;

    /**
     * 扩展字段 对应字段：`ex`
     * @var string
     */
    private string $ex = '';

    /**
     * 内容结构（必填） 对应字段：`content`
     * @var array<string, mixed>|null
     */
    private ?array $content = null;

    /**
     * 设置发送者用户 ID
     *
     * @param string $sendID 发送者用户 ID（必填）
     * @return self
     */
    public function setSendID(string $sendID): self
    {
        $this->sendID = $sendID;
        return $this;
    }

    /**
     * 设置接收者用户 ID（单聊）
     *
     * @param string $recvID 接收者用户 ID（单聊必填）
     * @return self
     */
    public function setRecvID(string $recvID): self
    {
        $this->recvID = $recvID;
        return $this;
    }

    /**
     * 设置群组 ID（群聊）
     *
     * @param string $groupID 群组 ID（群聊必填）
     * @return self
     */
    public function setGroupID(string $groupID): self
    {
        $this->groupID = $groupID;
        return $this;
    }

    /**
     * 设置发送者昵称
     *
     * @param string $nickname 昵称
     * @return self
     */
    public function setSenderNickname(string $nickname): self
    {
        $this->senderNickname = $nickname;
        return $this;
    }

    /**
     * 设置发送者头像
     *
     * @param string $faceURL 头像 URL
     * @return self
     */
    public function setSenderFaceURL(string $faceURL): self
    {
        $this->senderFaceURL = $faceURL;
        return $this;
    }

    /**
     * 设置发送平台 ID
     *
     * @param int $platformID 平台 ID
     * @return self
     */
    public function setSenderPlatformID(int $platformID): self
    {
        $this->senderPlatformID = $platformID;
        return $this;
    }

    /**
     * 设置内容类型
     *
     * @param ContentType $type 内容类型（必填）
     * @return self
     */
    public function setContentType(ContentType $type): self
    {
        $this->contentType = $type;
        return $this;
    }

    /**
     * 设置会话类型
     *
     * @param SessionType $type 会话类型（必填）
     * @return self
     */
    public function setSessionType(SessionType $type): self
    {
        $this->sessionType = $type;
        return $this;
    }

    /**
     * 设置仅在线发送
     *
     * @param bool $flag 是否仅在线
     * @return self
     */
    public function setIsOnlineOnly(bool $flag): self
    {
        $this->isOnlineOnly = $flag;
        return $this;
    }

    /**
     * 设置不推送离线消息
     *
     * @param bool $flag 不推送离线
     * @return self
     */
    public function setNotOfflinePush(bool $flag): self
    {
        $this->notOfflinePush = $flag;
        return $this;
    }

    /**
     * 设置客户端消息 ID
     *
     * @param string|null $id 客户端消息 ID（为空时自动生成）
     * @return self
     */
    public function setClientMsgID(?string $id): self
    {
        $this->clientMsgID = $id;
        return $this;
    }

    /**
     * 设置扩展字段
     *
     * @param string $ex 扩展字段
     * @return self
     */
    public function setEx(string $ex): self
    {
        $this->ex = $ex;
        return $this;
    }

    /**
     * 设置内容结构
     *
     * @param array<string, mixed> $content 内容结构（必填）
     * @return self
     */
    public function setContent(array $content): self
    {
        $this->content = $content;
        return $this;
    }

    /**
     * 构建单聊载荷（通用）。
     *
     * @param string               $sendID
     * @param string               $recvID
     * @param ContentType          $type
     * @param array<string, mixed> $content
     * @param array<string, mixed> $opts
     * @return self
     */
    public static function single(string $sendID, string $recvID, ContentType $type, array $content, array $opts = []): self
    {
        if ($sendID === '' || $recvID === '') {
            throw new InvalidArgumentException('sendID 和 recvID 不能为空');
        }

        return (new self())
            ->setSendID($sendID)
            ->setRecvID($recvID)
            ->setGroupID('')
            ->setSenderNickname((string) ($opts['senderNickname'] ?? ''))
            ->setSenderFaceURL((string) ($opts['senderFaceURL'] ?? ''))
            ->setSenderPlatformID((int) ($opts['senderPlatformID'] ?? 1))
            ->setContentType($type)
            ->setSessionType(SessionType::SINGLE)
            ->setIsOnlineOnly((bool) ($opts['isOnlineOnly'] ?? false))
            ->setNotOfflinePush((bool) ($opts['notOfflinePush'] ?? false))
            ->setClientMsgID(isset($opts['clientMsgID']) ? (string) $opts['clientMsgID'] : null)
            ->setEx((string) ($opts['ex'] ?? ''))
            ->setContent($content);
    }

    /**
     * 构建群聊载荷（通用）。
     *
     * @param string               $sendID
     * @param string               $groupID
     * @param ContentType          $type
     * @param array<string, mixed> $content
     * @param array<string, mixed> $opts
     * @return self
     */
    public static function group(string $sendID, string $groupID, ContentType $type, array $content, array $opts = []): self
    {
        if ($sendID === '' || $groupID === '') {
            throw new InvalidArgumentException('sendID 和 groupID 不能为空');
        }
        return (new self())
            ->setSendID($sendID)
            ->setRecvID('')
            ->setGroupID($groupID)
            ->setSenderNickname((string) ($opts['senderNickname'] ?? ''))
            ->setSenderFaceURL((string) ($opts['senderFaceURL'] ?? ''))
            ->setSenderPlatformID((int) ($opts['senderPlatformID'] ?? 1))
            ->setContentType($type)
            ->setSessionType(SessionType::GROUP)
            ->setIsOnlineOnly((bool) ($opts['isOnlineOnly'] ?? false))
            ->setNotOfflinePush((bool) ($opts['notOfflinePush'] ?? false))
            ->setClientMsgID(isset($opts['clientMsgID']) ? (string) $opts['clientMsgID'] : null)
            ->setEx((string) ($opts['ex'] ?? ''))
            ->setContent($content);
    }

    /**
     * 转换为发送接口所需数组。
     *
     * @return array<string, mixed>
     * @throws RandomException
     */
    public function toArray(): array
    {
        if ($this->sendID === null || $this->sendID === '') {
            throw new InvalidArgumentException('MessagePayload.sendID 不能为空');
        }
        if ($this->contentType === null) {
            throw new InvalidArgumentException('MessagePayload.contentType 不能为空');
        }
        if ($this->sessionType === null) {
            throw new InvalidArgumentException('MessagePayload.sessionType 不能为空');
        }
        if ($this->content === null) {
            throw new InvalidArgumentException('MessagePayload.content 不能为空');
        }
        if ($this->clientMsgID === null || $this->clientMsgID === '') {
            $this->clientMsgID = bin2hex(random_bytes(8));
        }
        if ($this->sessionType === SessionType::SINGLE) {
            if ($this->recvID === null || $this->recvID === '') {
                throw new InvalidArgumentException('MessagePayload.recvID 不能为空（单聊）');
            }
            $group = '';
        }
        else {
            if ($this->groupID === null || $this->groupID === '') {
                throw new InvalidArgumentException('MessagePayload.groupID 不能为空（群聊）');
            }
            $group        = $this->groupID;
            $this->recvID = '';
        }

        return [
            'sendID'           => $this->sendID,
            'recvID'           => $this->recvID,
            'groupID'          => $group,
            'senderNickname'   => $this->senderNickname,
            'senderFaceURL'    => $this->senderFaceURL,
            'senderPlatformID' => $this->senderPlatformID,
            'content'          => $this->content,
            'contentType'      => $this->contentType->value,
            'sessionType'      => $this->sessionType->value,
            'isOnlineOnly'     => $this->isOnlineOnly,
            'notOfflinePush'   => $this->notOfflinePush,
            'clientMsgID'      => $this->clientMsgID,
            'ex'               => $this->ex,
        ];
    }
}
