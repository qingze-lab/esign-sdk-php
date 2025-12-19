<?php
declare(strict_types = 1);

namespace QingzeLab\OpenIM\Message\Content;

use InvalidArgumentException;

/**
 * 图片元素信息
 *
 * 对应 OpenIM ImageElem 的图片对象，包含原图/大图/缩略图等尺寸信息。
 * 参考 OpenIM 官方文档：消息类型 - 图片消息。
 */
class Picture
{
    /**
     * 图片唯一标识 对应字段：`uuid`
     * @var string|null
     */
    private ?string $uuid = null;

    /**
     * 图片类型（如 jpeg/png） 对应字段：`type`
     * @var string|null
     */
    private ?string $type = null;

    /**
     * 图片大小（字节） 对应字段：`size`
     * @var int|null
     */
    private ?int $size = null;

    /**
     * 图片宽度（像素） 对应字段：`width`
     * @var int|null
     */
    private ?int $width = null;

    /**
     * 图片高度（像素） 对应字段：`height`
     * @var int|null
     */
    private ?int $height = null;

    /**
     * 图片下载地址 对应字段：`url`
     * @var string|null
     */
    private ?string $url = null;

    /**
     * 设置图片唯一标识
     *
     * @param string|null $uuid 图片唯一标识
     * @return self
     */
    public function setUuid(?string $uuid): self
    {
        $this->uuid = $uuid;
        return $this;
    }

    /**
     * 设置图片类型
     *
     * @param string $type 图片类型（如 jpeg/png）
     * @return self
     */
    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * 设置图片大小
     *
     * @param int|null $size 字节大小
     * @return self
     */
    public function setSize(?int $size): self
    {
        $this->size = $size;
        return $this;
    }

    /**
     * 设置图片宽度
     *
     * @param int $width 宽度像素
     * @return self
     */
    public function setWidth(int $width): self
    {
        $this->width = $width;
        return $this;
    }

    /**
     * 设置图片高度
     *
     * @param int $height 高度像素
     * @return self
     */
    public function setHeight(int $height): self
    {
        $this->height = $height;
        return $this;
    }

    /**
     * 设置图片下载地址
     *
     * @param string $url 下载地址
     * @return self
     */
    public function setUrl(string $url): self
    {
        $this->url = $url;
        return $this;
    }

    /**
     * 转为数组
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        if ($this->type === null || $this->type === '') {
            throw new InvalidArgumentException('Picture.type 不能为空');
        }
        if ($this->width === null || $this->height === null || $this->url === null || $this->url === '') {
            throw new InvalidArgumentException('Picture.width/height/url 为必填');
        }

        return [
            'uuid'   => $this->uuid ?? '',
            'type'   => $this->type,
            'size'   => $this->size ?? 0,
            'width'  => $this->width,
            'height' => $this->height,
            'url'    => $this->url,
        ];
    }
}
