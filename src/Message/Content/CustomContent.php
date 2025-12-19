<?php
declare(strict_types = 1);

namespace QingzeLab\OpenIM\Message\Content;

use InvalidArgumentException;

/**
 * 自定义消息内容
 *
 * 对应 OpenIM CustomElem，自定义结构由业务定义。
 * 参考 OpenIM 官方文档：消息类型 - 自定义消息。
 */
class CustomContent implements MessageContentInterface
{
    /**
     * 自定义数据 对应字段：`data`
     * @var string|null
     */
    private ?string $data = null;

    /**
     * 扩展描述信息 对应字段：`description`
     * @var string|null
     */
    private ?string $description = null;

    /**
     * 扩展字段 对应字段：`extension`
     * @var string|null
     */
    private ?string $extension = null;

    /**
     * 设置自定义数据
     *
     * @param string $data 自定义数据（必填）
     * @return self
     */
    public function setData(string $data): self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * 设置扩展描述信息
     *
     * @param string|null $description 描述
     * @return self
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    /**
     * 设置扩展字段
     *
     * @param string|null $extension 扩展
     * @return self
     */
    public function setExtension(?string $extension): self
    {
        $this->extension = $extension;
        return $this;
    }

    /**
     * 转换为 OpenIM 所需的 `content` 结构
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        if ($this->data === null || $this->data === '') {
            throw new InvalidArgumentException('CustomContent.data 不能为空');
        }

        $content = [
            'data' => $this->data,
        ];
        if ($this->description !== null) {
            $content['description'] = $this->description;
        }
        if ($this->extension !== null) {
            $content['extension'] = $this->extension;
        }

        return $content;
    }
}
