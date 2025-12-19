<?php
declare(strict_types = 1);

namespace QingzeLab\OpenIM\Message\Content;

/**
 * 图片消息内容
 *
 * 对应 OpenIM ImageElem，包含源路径和三种尺寸图片信息。
 * 参考 OpenIM 官方文档：消息类型 - 图片消息。
 */
class ImageContent implements MessageContentInterface
{
    /**
     * 本地源路径 对应字段：`sourcePath`
     * @var string|null
     */
    private ?string $sourcePath = null;

    /**
     * 原图信息 对应字段：`sourcePicture`
     * @var Picture|null
     */
    private ?Picture $sourcePicture = null;

    /**
     * 大图信息 对应字段：`bigPicture`
     * @var Picture|null
     */
    private ?Picture $bigPicture = null;

    /**
     * 缩略图信息 对应字段：`snapshotPicture`
     * @var Picture|null
     */
    private ?Picture $snapshotPicture = null;

    /**
     * 设置本地源路径
     *
     * @param string|null $sourcePath 本地源路径
     * @return self
     */
    public function setSourcePath(?string $sourcePath): self
    {
        $this->sourcePath = $sourcePath;
        return $this;
    }

    /**
     * 设置原图信息
     *
     * @param Picture|null $sourcePicture 原图
     * @return self
     */
    public function setSourcePicture(?Picture $sourcePicture): self
    {
        $this->sourcePicture = $sourcePicture;
        return $this;
    }

    /**
     * 设置大图信息
     *
     * @param Picture|null $bigPicture 大图
     * @return self
     */
    public function setBigPicture(?Picture $bigPicture): self
    {
        $this->bigPicture = $bigPicture;
        return $this;
    }

    /**
     * 设置缩略图信息
     *
     * @param Picture|null $snapshotPicture 缩略图
     * @return self
     */
    public function setSnapshotPicture(?Picture $snapshotPicture): self
    {
        $this->snapshotPicture = $snapshotPicture;
        return $this;
    }

    /**
     * 转换为 OpenIM 所需的 `content` 结构
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $content = [];
        if ($this->sourcePath !== null) {
            $content['sourcePath'] = $this->sourcePath;
        }
        if ($this->sourcePicture !== null) {
            $content['sourcePicture'] = $this->sourcePicture->toArray();
        }
        if ($this->bigPicture !== null) {
            $content['bigPicture'] = $this->bigPicture->toArray();
        }
        if ($this->snapshotPicture !== null) {
            $content['snapshotPicture'] = $this->snapshotPicture->toArray();
        }

        return $content;
    }
}
