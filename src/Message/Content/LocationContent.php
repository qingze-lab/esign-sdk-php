<?php
declare(strict_types = 1);

namespace QingzeLab\OpenIM\Message\Content;

use InvalidArgumentException;

/**
 * 位置消息内容
 *
 * 对应 OpenIM LocationElem，包含经纬度与位置描述。
 * 参考 OpenIM 官方文档：消息类型 - 位置消息。
 */
class LocationContent implements MessageContentInterface
{
    /**
     * 位置描述 对应字段：`description`
     * @var string|null
     */
    private ?string $description = null;

    /**
     * 经度 对应字段：`longitude`
     * @var float|null
     */
    private ?float $longitude = null;

    /**
     * 纬度 对应字段：`latitude`
     * @var float|null
     */
    private ?float $latitude = null;

    /**
     * 设置位置描述
     *
     * @param string|null $description 位置描述
     * @return self
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    /**
     * 设置经度
     *
     * @param float $longitude 经度（必填）
     * @return self
     */
    public function setLongitude(float $longitude): self
    {
        $this->longitude = $longitude;
        return $this;
    }

    /**
     * 设置纬度
     *
     * @param float $latitude 纬度（必填）
     * @return self
     */
    public function setLatitude(float $latitude): self
    {
        $this->latitude = $latitude;
        return $this;
    }

    /**
     * 转换为 OpenIM 所需的 `content` 结构
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        if ($this->longitude === null || $this->latitude === null) {
            throw new InvalidArgumentException('LocationContent.longitude/latitude 为必填');
        }
        return [
            'description' => $this->description ?? '',
            'longitude'   => $this->longitude,
            'latitude'    => $this->latitude,
        ];
    }
}
