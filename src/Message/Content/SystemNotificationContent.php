<?php
declare(strict_types = 1);

namespace QingzeLab\OpenIM\Message\Content;

use InvalidArgumentException;

/**
 * 系统通知消息内容
 *
 * 对应 OpenIM SystemNotificationElem，包含系统通知的名称、头像、类型、文本等。
 * 参考 OpenIM 官方文档：消息类型 - 系统通知。
 */
class SystemNotificationContent implements MessageContentInterface
{
    /**
     * 通知名称 对应字段：`notificationName`
     * @var string|null
     */
    private ?string $notificationName = null;

    /**
     * 通知头像 URL 对应字段：`notificationFaceURL`
     * @var string|null
     */
    private ?string $notificationFaceURL = null;

    /**
     * 通知类型 对应字段：`notificationType`
     * @var int|null
     */
    private ?int $notificationType = null;

    /**
     * 通知文本 对应字段：`text`
     * @var string|null
     */
    private ?string $text = null;

    /**
     * 外部链接 对应字段：`externalUrl`
     * @var string|null
     */
    private ?string $externalUrl = null;

    /**
     * 混合类型 对应字段：`mixType`
     * @var int|null
     */
    private ?int $mixType = null;

    /**
     * 图片元素 对应字段：`pictureElem`
     * @var ImageContent|null
     */
    private ?ImageContent $pictureElem = null;

    /**
     * 设置通知名称
     *
     * @param string $v 通知名称
     * @return self
     */
    public function setNotificationName(string $v): self
    {
        $this->notificationName = $v;
        return $this;
    }

    /**
     * 设置通知头像 URL
     *
     * @param string $v 头像 URL
     * @return self
     */
    public function setNotificationFaceURL(string $v): self
    {
        $this->notificationFaceURL = $v;
        return $this;
    }

    /**
     * 设置通知类型
     *
     * @param int $v 类型
     * @return self
     */
    public function setNotificationType(int $v): self
    {
        $this->notificationType = $v;
        return $this;
    }

    /**
     * 设置通知文本
     *
     * @param string $v 文本
     * @return self
     */
    public function setText(string $v): self
    {
        $this->text = $v;
        return $this;
    }

    /**
     * 设置外部链接
     *
     * @param string|null $v 链接
     * @return self
     */
    public function setExternalUrl(?string $v): self
    {
        $this->externalUrl = $v;
        return $this;
    }

    /**
     * 设置混合类型
     *
     * @param int $v 混合类型
     * @return self
     */
    public function setMixType(int $v): self
    {
        $this->mixType = $v;
        return $this;
    }

    /**
     * 设置图片元素
     *
     * @param ImageContent|null $v 图片元素
     * @return self
     */
    public function setPictureElem(?ImageContent $v): self
    {
        $this->pictureElem = $v;
        return $this;
    }

    /**
     * 转换为 OpenIM 所需的 `content` 结构
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        if ($this->notificationName === null || $this->notificationName === '') {
            throw new InvalidArgumentException('SystemNotificationContent.notificationName 不能为空');
        }
        if ($this->notificationFaceURL === null || $this->notificationFaceURL === '') {
            throw new InvalidArgumentException('SystemNotificationContent.notificationFaceURL 不能为空');
        }
        if ($this->notificationType === null) {
            throw new InvalidArgumentException('SystemNotificationContent.notificationType 不能为空');
        }
        if ($this->text === null) {
            throw new InvalidArgumentException('SystemNotificationContent.text 不能为空');
        }
        if ($this->mixType === null) {
            throw new InvalidArgumentException('SystemNotificationContent.mixType 不能为空');
        }

        $content = [
            'notificationName'    => $this->notificationName,
            'notificationFaceURL' => $this->notificationFaceURL,
            'notificationType'    => $this->notificationType,
            'text'                => $this->text,
            'externalUrl'         => $this->externalUrl ?? '',
            'mixType'             => $this->mixType,
        ];

        if ($this->pictureElem !== null) {
            $content['pictureElem'] = $this->pictureElem->toArray();
        }

        return $content;
    }
}
