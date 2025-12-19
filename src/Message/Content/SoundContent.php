<?php
declare(strict_types = 1);

namespace QingzeLab\OpenIM\Message\Content;

use InvalidArgumentException;

/**
 * 语音消息内容
 *
 * 对应 OpenIM SoundElem，包含语音的地址、时长等信息。
 * 参考 OpenIM 官方文档：消息类型 - 语音消息。
 */
class SoundContent implements MessageContentInterface
{
    /**
     * 语音文件唯一 ID 对应字段：`uuid`
     * @var string|null
     */
    private ?string $uuid = null;

    /**
     * 本地语音路径 对应字段：`soundPath`
     * @var string|null
     */
    private ?string $soundPath = null;

    /**
     * 语音下载地址 对应字段：`sourceUrl`
     * @var string|null
     */
    private ?string $sourceUrl = null;

    /**
     * 语音文件大小（字节） 对应字段：`dataSize`
     * @var int|null
     */
    private ?int $dataSize = null;

    /**
     * 语音时长（秒） 对应字段：`duration`
     * @var int|null
     */
    private ?int $duration = null;

    /**
     * 语音类型（如 amr/mp3） 对应字段：`soundType`
     * @var string|null
     */
    private ?string $soundType = null;

    /**
     * 设置语音文件唯一 ID
     *
     * @param string|null $uuid 唯一 ID
     * @return self
     */
    public function setUuid(?string $uuid): self
    {
        $this->uuid = $uuid;
        return $this;
    }

    /**
     * 设置本地语音路径
     *
     * @param string|null $soundPath 路径
     * @return self
     */
    public function setSoundPath(?string $soundPath): self
    {
        $this->soundPath = $soundPath;
        return $this;
    }

    /**
     * 设置语音下载地址
     *
     * @param string $sourceUrl 下载地址（必填）
     * @return self
     */
    public function setSourceUrl(string $sourceUrl): self
    {
        $this->sourceUrl = $sourceUrl;
        return $this;
    }

    /**
     * 设置语音文件大小
     *
     * @param int|null $dataSize 字节大小
     * @return self
     */
    public function setDataSize(?int $dataSize): self
    {
        $this->dataSize = $dataSize;
        return $this;
    }

    /**
     * 设置语音时长
     *
     * @param int $duration 秒（必填，正整数）
     * @return self
     */
    public function setDuration(int $duration): self
    {
        $this->duration = $duration;
        return $this;
    }

    /**
     * 设置语音类型
     *
     * @param string|null $soundType 类型（如 amr/mp3）
     * @return self
     */
    public function setSoundType(?string $soundType): self
    {
        $this->soundType = $soundType;
        return $this;
    }

    /**
     * 转换为 OpenIM 所需的 `content` 结构
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        if ($this->sourceUrl === null || $this->sourceUrl === '') {
            throw new InvalidArgumentException('SoundContent.sourceUrl 不能为空');
        }
        if ($this->duration === null || $this->duration <= 0) {
            throw new InvalidArgumentException('SoundContent.duration 必须为正整数');
        }

        return [
            'uuid'      => $this->uuid ?? '',
            'soundPath' => $this->soundPath ?? '',
            'sourceUrl' => $this->sourceUrl,
            'dataSize'  => $this->dataSize ?? 0,
            'duration'  => $this->duration,
            'soundType' => $this->soundType ?? '',
        ];
    }
}
