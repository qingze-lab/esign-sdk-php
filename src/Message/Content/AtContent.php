<?php
declare(strict_types = 1);

namespace QingzeLab\OpenIM\Message\Content;

use InvalidArgumentException;

/**
 * @消息内容
 *
 * 对应 OpenIM AtElem，包含被 @ 的用户列表、文本内容等。
 * 参考 OpenIM 官方文档：消息类型 - @消息。
 */
class AtContent implements MessageContentInterface
{
    /**
     * 文本内容 对应字段：`text`
     * @var string|null
     */
    private ?string $text = null;

    /**
     * 被 @ 的用户列表 对应字段：`atUserList`
     * @var array<int, string>|null
     */
    private ?array $atUserList = null;

    /**
     * 是否 @ 自己 对应字段：`isAtSelf`
     * @var bool
     */
    private bool $isAtSelf = false;

    /**
     * 设置文本内容
     *
     * @param string $text 文本内容
     * @return self
     */
    public function setText(string $text): self
    {
        $this->text = $text;
        return $this;
    }

    /**
     * 设置被 @ 的用户列表
     *
     * @param array<int, string> $atUserList 用户 ID 列表
     * @return self
     */
    public function setAtUserList(array $atUserList): self
    {
        $this->atUserList = $atUserList;
        return $this;
    }

    /**
     * 设置是否 @ 自己
     *
     * @param bool $isAtSelf 是否 @ 自己
     * @return self
     */
    public function setIsAtSelf(bool $isAtSelf): self
    {
        $this->isAtSelf = $isAtSelf;
        return $this;
    }

    /**
     * 转换为 OpenIM 所需的 `content` 结构
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        if ($this->text === null) {
            throw new InvalidArgumentException('AtContent.text 不能为空');
        }
        if ($this->atUserList === null) {
            throw new InvalidArgumentException('AtContent.atUserList 不能为空');
        }

        return [
            'text'       => $this->text,
            'atUserList' => $this->atUserList,
            'isAtSelf'   => $this->isAtSelf,
        ];
    }
}
