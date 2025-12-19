<?php
declare(strict_types = 1);

namespace QingzeLab\OpenIM\Message\Content;

use InvalidArgumentException;

/**
 * 文件消息内容
 *
 * 对应 OpenIM FileElem，包含文件的 URL/名称/大小等信息。
 * 参考 OpenIM 官方文档：消息类型 - 文件消息。
 */
class FileContent implements MessageContentInterface
{
    /**
     * 本地文件路径 对应字段：`filePath`
     * @var string|null
     */
    private ?string $filePath = null;

    /**
     * 文件唯一 ID 对应字段：`uuid`
     * @var string|null
     */
    private ?string $uuid = null;

    /**
     * 文件下载地址 对应字段：`sourceUrl`
     * @var string|null
     */
    private ?string $sourceUrl = null;

    /**
     * 文件名称 对应字段：`fileName`
     * @var string|null
     */
    private ?string $fileName = null;

    /**
     * 文件大小（字节） 对应字段：`fileSize`
     * @var int|null
     */
    private ?int $fileSize = null;

    /**
     * 文件类型（如 pdf/docx） 对应字段：`fileType`
     * @var string|null
     */
    private ?string $fileType = null;

    /**
     * 设置本地文件路径
     *
     * @param string|null $v 本地路径
     * @return self
     */
    public function setFilePath(?string $v): self
    {
        $this->filePath = $v;
        return $this;
    }

    /**
     * 设置文件唯一 ID
     *
     * @param string|null $v 唯一 ID
     * @return self
     */
    public function setUuid(?string $v): self
    {
        $this->uuid = $v;
        return $this;
    }

    /**
     * 设置文件下载地址
     *
     * @param string $v 下载地址（必填）
     * @return self
     */
    public function setSourceUrl(string $v): self
    {
        $this->sourceUrl = $v;
        return $this;
    }

    /**
     * 设置文件名称
     *
     * @param string $v 名称（必填）
     * @return self
     */
    public function setFileName(string $v): self
    {
        $this->fileName = $v;
        return $this;
    }

    /**
     * 设置文件大小
     *
     * @param int $v 字节大小（必填）
     * @return self
     */
    public function setFileSize(int $v): self
    {
        $this->fileSize = $v;
        return $this;
    }

    /**
     * 设置文件类型
     *
     * @param string|null $v 类型（如 pdf/docx）
     * @return self
     */
    public function setFileType(?string $v): self
    {
        $this->fileType = $v;
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
            throw new InvalidArgumentException('FileContent.sourceUrl 不能为空');
        }
        if ($this->fileName === null || $this->fileName === '') {
            throw new InvalidArgumentException('FileContent.fileName 不能为空');
        }
        if ($this->fileSize === null || $this->fileSize <= 0) {
            throw new InvalidArgumentException('FileContent.fileSize 必须为正整数');
        }

        return [
            'filePath'  => $this->filePath ?? '',
            'uuid'      => $this->uuid ?? '',
            'sourceUrl' => $this->sourceUrl,
            'fileName'  => $this->fileName,
            'fileSize'  => $this->fileSize,
            'fileType'  => $this->fileType ?? '',
        ];
    }
}
