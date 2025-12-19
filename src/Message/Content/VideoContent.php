<?php
declare(strict_types = 1);

namespace QingzeLab\OpenIM\Message\Content;

use InvalidArgumentException;

/**
 * 视频消息内容
 *
 * 对应 OpenIM VideoElem，包含视频的 URL/类型/大小/时长以及封面信息。
 * 参考 OpenIM 官方文档：消息类型 - 视频消息。
 */
class VideoContent implements MessageContentInterface
{
    /**
     * 视频本地路径 对应字段：`videoPath`
     * @var string|null
     */
    private ?string $videoPath = null;

    /**
     * /**
     * 视频唯一 ID 对应字段：`videoUUID`
     * @var string|null
     */
    private ?string $videoUUID = null;

    /**
     * /**
     * 视频下载地址 对应字段：`videoUrl`
     * @var string|null
     */
    private ?string $videoUrl = null;

    /**
     * /**
     * 视频类型 对应字段：`videoType`
     * @var string|null
     */
    private ?string $videoType = null;

    /**
     * /**
     * 视频大小 对应字段：`videoSize`
     * @var int|null
     */
    private ?int $videoSize = null;

    /**
     * /**
     * 视频时长 对应字段：`duration`
     * @var int|null
     */
    private ?int $duration = null;

    /**
     * /**
     * 封面本地路径 对应字段：`snapshotPath`
     * @var string|null
     */
    private ?string $snapshotPath = null;

    /**
     * /**
     * 封面唯一 ID 对应字段：`snapshotUUID`
     * @var string|null
     */
    private ?string $snapshotUUID = null;

    /**
     * /**
     * 封面大小 对应字段：`snapshotSize`
     * @var int|null
     */
    private ?int $snapshotSize = null;

    /**
     * /**
     * 封面下载地址 对应字段：`snapshotUrl`
     * @var string|null
     */
    private ?string $snapshotUrl = null;

    /**
     * /**
     * 封面宽度 对应字段：`snapshotWidth`
     * @var int|null
     */
    private ?int $snapshotWidth = null;

    /**
     * /**
     * 封面高度 对应字段：`snapshotHeight`
     * @var int|null
     */
    private ?int $snapshotHeight = null;

    /**
     * 设置视频本地路径
     *
     * @param string|null $v 本地路径
     * @return self
     */
    public function setVideoPath(?string $v): self
    {
        $this->videoPath = $v;
        return $this;
    }

    /**
     * 设置视频唯一 ID
     *
     * @param string|null $v 唯一 ID
     * @return self
     */
    public function setVideoUUID(?string $v): self
    {
        $this->videoUUID = $v;
        return $this;
    }

    /**
     * 设置视频下载地址
     *
     * @param string $v 下载地址（必填）
     * @return self
     */
    public function setVideoUrl(string $v): self
    {
        $this->videoUrl = $v;
        return $this;
    }

    /**
     * 设置视频类型
     *
     * @param string $v 类型（如 mp4）
     * @return self
     */
    public function setVideoType(string $v): self
    {
        $this->videoType = $v;
        return $this;
    }

    /**
     * 设置视频大小
     *
     * @param int $v 字节大小（必填）
     * @return self
     */
    public function setVideoSize(int $v): self
    {
        $this->videoSize = $v;
        return $this;
    }

    /**
     * 设置视频时长
     *
     * @param int $v 秒（必填，正整数）
     * @return self
     */
    public function setDuration(int $v): self
    {
        $this->duration = $v;
        return $this;
    }

    /**
     * 设置封面本地路径
     *
     * @param string|null $v 本地路径
     * @return self
     */
    public function setSnapshotPath(?string $v): self
    {
        $this->snapshotPath = $v;
        return $this;
    }

    /**
     * 设置封面唯一 ID
     *
     * @param string|null $v 唯一 ID
     * @return self
     */
    public function setSnapshotUUID(?string $v): self
    {
        $this->snapshotUUID = $v;
        return $this;
    }

    /**
     * 设置封面大小
     *
     * @param int|null $v 字节大小
     * @return self
     */
    public function setSnapshotSize(?int $v): self
    {
        $this->snapshotSize = $v;
        return $this;
    }

    /**
     * 设置封面下载地址
     *
     * @param string $v 下载地址（必填）
     * @return self
     */
    public function setSnapshotUrl(string $v): self
    {
        $this->snapshotUrl = $v;
        return $this;
    }

    /**
     * 设置封面宽度
     *
     * @param int $v 像素宽度（必填）
     * @return self
     */
    public function setSnapshotWidth(int $v): self
    {
        $this->snapshotWidth = $v;
        return $this;
    }

    /**
     * 设置封面高度
     *
     * @param int $v 像素高度（必填）
     * @return self
     */
    public function setSnapshotHeight(int $v): self
    {
        $this->snapshotHeight = $v;
        return $this;
    }

    /**
     * 转换为 OpenIM 所需的 `content` 结构
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        if ($this->videoUrl === null || $this->videoUrl === '') {
            throw new InvalidArgumentException('VideoContent.videoUrl 不能为空');
        }
        if ($this->videoType === null || $this->videoType === '') {
            throw new InvalidArgumentException('VideoContent.videoType 不能为空');
        }
        if ($this->videoSize === null || $this->videoSize <= 0) {
            throw new InvalidArgumentException('VideoContent.videoSize 必须为正整数');
        }
        if ($this->duration === null || $this->duration <= 0) {
            throw new InvalidArgumentException('VideoContent.duration 必须为正整数');
        }
        if ($this->snapshotUrl === null || $this->snapshotUrl === '') {
            throw new InvalidArgumentException('VideoContent.snapshotUrl 不能为空');
        }
        if ($this->snapshotWidth === null || $this->snapshotHeight === null) {
            throw new InvalidArgumentException('VideoContent.snapshotWidth/snapshotHeight 为必填');
        }

        return [
            'videoPath'      => $this->videoPath ?? '',
            'videoUUID'      => $this->videoUUID ?? '',
            'videoUrl'       => $this->videoUrl,
            'videoType'      => $this->videoType,
            'videoSize'      => $this->videoSize,
            'duration'       => $this->duration,
            'snapshotPath'   => $this->snapshotPath ?? '',
            'snapshotUUID'   => $this->snapshotUUID ?? '',
            'snapshotSize'   => $this->snapshotSize ?? 0,
            'snapshotUrl'    => $this->snapshotUrl,
            'snapshotWidth'  => $this->snapshotWidth,
            'snapshotHeight' => $this->snapshotHeight,
        ];
    }
}
