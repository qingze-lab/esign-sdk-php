<?php
declare(strict_types = 1);

namespace QingzeLab\OpenIM\Message\Content;

use InvalidArgumentException;

/**
 * 文本消息内容
 *
 * 对应 OpenIM 消息体中的 TextElem，包含纯文本内容。
 * 参考 OpenIM 官方文档：消息类型 - 文本消息。
 */
class TextContent implements MessageContentInterface
{
    /**
     * 文本内容 对应字段：`content`
     * @var string|null
     */
    private ?string $content = null;

    /**
     * 设置文本内容
     *
     * @param string $content 文本内容
     * @return self
     */
    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    /**
     * 转换为 OpenIM 所需的 `content` 结构
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        if ($this->content === null || $this->content === '') {
            throw new InvalidArgumentException('TextContent.content 不能为空');
        }

        return [
            'content' => $this->content,
        ];
    }
}
