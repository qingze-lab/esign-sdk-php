<?php

declare(strict_types = 1);

namespace QingzeLab\ESignBao\Services;

use QingzeLab\ESignBao\Exceptions\ESignBaoException;
use QingzeLab\ESignBao\Http\HttpClient;
use QingzeLab\ESignBao\Http\UploadClient;
use QingzeLab\ESignBao\Utils\SignatureUtil;

/**
 * 文件领域服务
 * 基于易签宝官方文档 V3 API
 */
class FileService
{
    /**
     * @var HttpClient
     */
    private HttpClient $httpClient;

    /**
     * @var UploadClient
     */
    private UploadClient $uploadClient;

    /**
     * @param HttpClient $httpClient
     */
    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient   = $httpClient;
        $this->uploadClient = new UploadClient($httpClient->getConfig());
    }

    /**
     * 获取文件上传地址 (步骤一)
     * 接口文档: https://open.esign.cn/doc/opendoc/pdf-sign3/rlh256
     *
     * @param string      $contentMd5       文件的Content-MD5值
     * @param string      $contentType      目标文件的MIME类型
     * @param string      $fileName         文件名称
     * @param int         $fileSize         文件大小(字节)
     * @param bool        $convertToPDF     是否需要转换成PDF文档
     * @param bool        $convertToHTML    是否需要转换成HTML文档
     * @param bool        $convertToOFD     是否需要转换为OFD文档
     * @param string|null $dedicatedCloudId 专属云项目ID
     * @return array 包含fileId和fileUploadUrl
     * @throws ESignBaoException
     */
    public function getFileUploadUrl(
        string  $contentMd5,
        string  $contentType,
        string  $fileName,
        int     $fileSize,
        bool    $convertToPDF = false,
        bool    $convertToHTML = false,
        bool    $convertToOFD = false,
        ?string $dedicatedCloudId = null
    ): array
    {
        $data = [
            'contentMd5'    => $contentMd5,
            'contentType'   => $contentType,
            'fileName'      => $fileName,
            'fileSize'      => $fileSize,
            'convertToPDF'  => $convertToPDF,
            'convertToHTML' => $convertToHTML,
            'convertToOFD'  => $convertToOFD,
        ];

        if ($dedicatedCloudId !== null) {
            $data['dedicatedCloudId'] = $dedicatedCloudId;
        }

        return $this->httpClient->post('/v3/files/file-upload-url', $data);
    }

    /**
     * 上传文件流 (步骤二)
     * 将本地文件上传到步骤一获取的 fileUploadUrl
     * 建议使用 fopen 资源句柄以节省内存
     *
     * @param string      $fileUploadUrl 步骤一获取的上传地址
     * @param string      $filePath      本地文件路径
     * @param string      $contentType   文件MIME类型
     * @param string|null $contentMd5    文件Content-MD5，如果不传则自动计算（建议传入以确保与Step 1一致）
     * @return array 上传结果
     * @throws ESignBaoException
     */
    public function uploadFileStream(string $fileUploadUrl, string $filePath, string $contentType = 'application/pdf', ?string $contentMd5 = null): array
    {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            throw new ESignBaoException("文件不存在或不可读: {$filePath}", 0);
        }

        // 计算MD5用于校验（如果没有传入）
        if ($contentMd5 === null) {
            $contentMd5 = SignatureUtil::generateFileMD5($filePath);
        }

        // 使用文件流上传，避免读取整个文件到内存
        $stream = fopen($filePath, 'r');
        if ($stream === false) {
            throw new ESignBaoException("打开文件流失败: {$filePath}", 0);
        }

        try {
            // 使用新 UploadClient 上传
            $headers = [
                'Content-Type' => $contentType,
                'Content-MD5'  => $contentMd5,
            ];

            return $this->uploadClient->put($fileUploadUrl, $stream, $headers);
        } finally {
            if (is_resource($stream)) {
                fclose($stream);
            }
        }
    }

    /**
     * 查询文件上传状态
     * 接口文档: https://open.esign.cn/doc/opendoc/pdf-sign3/gws76s
     *
     * @param string $fileId 文件ID
     * @return array 包含文件状态 status 等信息
     *                       status: 0-文件未上传；1-文件上传中；2-文件上传已完成；3-文件上传失败；
     *                       4-文件等待转pdf；5-文件已转换pdf；6-加水印中；7-加水印完毕；8-文件转换失败；
     * @throws ESignBaoException
     */
    public function getFileStatus(string $fileId): array
    {
        return $this->httpClient->get("/v3/files/{$fileId}");
    }
}
