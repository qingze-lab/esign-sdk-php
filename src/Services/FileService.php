<?php

declare(strict_types = 1);

namespace QingzeLab\ESignBao\Services;

use QingzeLab\ESignBao\Exceptions\ESignBaoException;
use QingzeLab\ESignBao\Http\HttpClient;

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
     * @param HttpClient $httpClient
     */
    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * 通过上传方式创建文件
     * 接口文档: https://open.esign.cn/doc/opendoc/pdf-sign3/xagpot
     *
     * @param string      $contentMd5   文件内容的MD5值（32位小写）
     * @param string      $contentType  文件MIME类型，如 application/pdf
     * @param string      $fileName     文件名称
     * @param int         $fileSize     文件大小（字节）
     * @param string|null $convertToPDF 是否转换为PDF，可选值：false-不转换，true-转换
     * @return array 包含fileId和uploadUrl的文件信息
     * @throws ESignBaoException
     */
    public function getUploadUrl(
        string  $contentMd5,
        string  $contentType,
        string  $fileName,
        int     $fileSize,
        ?string $convertToPDF = null
    ): array
    {
        $data = [
            'contentMd5'  => $contentMd5,
            'contentType' => $contentType,
            'fileName'    => $fileName,
            'fileSize'    => $fileSize,
        ];

        if ($convertToPDF !== null) {
            $data['convertToPDF'] = $convertToPDF;
        }

        return $this->httpClient->post('/v3/files/file-upload-url', $data);
    }

    /**
     * 通过文件路径上传（简化方法）
     *
     * @param string $filePath 本地文件路径
     * @return array 包含fileId的文件信息
     * @throws ESignBaoException
     */
    public function uploadFileByPath(string $filePath): array
    {
        if (!file_exists($filePath)) {
            throw new ESignBaoException("文件不存在: {$filePath}");
        }

        $fileContent = file_get_contents($filePath);
        if ($fileContent === false) {
            throw new ESignBaoException("读取文件失败: {$filePath}");
        }

        $fileName   = basename($filePath);
        $fileSize   = filesize($filePath);
        $contentMd5 = md5($fileContent);

        $finfo       = finfo_open(FILEINFO_MIME_TYPE);
        $contentType = finfo_file($finfo, $filePath);
        finfo_close($finfo);

        // 获取上传URL
        $result = $this->getUploadUrl($contentMd5, $contentType, $fileName, $fileSize);

        if (!isset($result['data']['fileId']) || !isset($result['data']['uploadUrl'])) {
            throw new ESignBaoException('获取上传URL失败');
        }

        $uploadUrl = $result['data']['uploadUrl'];
        $fileId    = $result['data']['fileId'];

        // 上传文件到OSS
        $ch = curl_init($uploadUrl);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fileContent);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: ' . $contentType,
            'Content-MD5: ' . base64_encode(md5($fileContent, true)),
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new ESignBaoException("文件上传失败，HTTP状态码: {$httpCode}");
        }

        return ['data' => ['fileId' => $fileId]];
    }

    /**
     * 通过模板创建文件
     * 接口文档: https://open.esign.cn/doc/opendoc/pdf-sign3/ub4ncy
     * 接口路径: POST /v3/files/createByTemplate
     *
     * @param string $templateId       模板编号
     * @param string $name             文件名称
     * @param array  $simpleFormFields 输入项填充内容，格式：['key' => 'value']
     * @return array 包含fileId、fileName、downloadUrl的文件信息
     * @throws ESignBaoException
     */
    public function createFileByTemplate(
        string $templateId,
        string $name,
        array  $simpleFormFields = []
    ): array
    {
        $data = [
            'templateId'       => $templateId,
            'name'             => $name,
            'simpleFormFields' => $simpleFormFields,
        ];

        return $this->httpClient->post('/v3/files/createByTemplate', $data);
    }

    /**
     * 查询文件详情
     *
     * @param string $fileId 文件ID
     * @return array 文件详情
     * @throws ESignBaoException
     */
    public function getFileInfo(string $fileId): array
    {
        return $this->httpClient->get("/v3/files/{$fileId}");
    }

    /**
     * 下载文件
     *
     * @param string $fileId 文件ID
     * @return array 包含下载链接
     * @throws ESignBaoException
     */
    public function downloadFile(string $fileId): array
    {
        return $this->httpClient->get("/v3/files/{$fileId}/download-url");
    }
}