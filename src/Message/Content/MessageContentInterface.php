<?php
declare(strict_types = 1);

namespace QingzeLab\OpenIM\Message\Content;

/**
 * 消息内容模型统一接口。
 */
interface MessageContentInterface
{
    /**
     * 转换为 OpenIM REST API 所需的 content 数组结构。
     *
     * @return array<string, mixed>
     */
    public function toArray(): array;
}

