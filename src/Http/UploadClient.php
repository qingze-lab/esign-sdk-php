<?php

declare(strict_types = 1);

namespace QingzeLab\ESignBao\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use QingzeLab\ESignBao\Exceptions\ESignBaoException;

/**
 * 文件上传客户端
 * 专门用于处理文件流上传（Step 2），不包含业务API签名逻辑
 */
class UploadClient
{
    private Client $client;

    public function __construct()
    {
        // 创建一个干净的 Guzzle 客户端，不带任何中间件或默认配置
        $this->client = new Client([
            'http_errors' => false,
            'timeout'     => 120,
        ]);
    }

    /**
     * 执行 PUT 上传
     *
     * @param string $url     上传地址
     * @param mixed  $stream  文件内容或资源句柄
     * @param array  $headers 请求头
     * @return void
     * @throws ESignBaoException
     */
    public function put(string $url, mixed $stream, array $headers): void
    {
        try {
            $options = [
                'headers' => $headers,
                'body'    => $stream,
            ];

            $response   = $this->client->request('PUT', $url, $options);
            $statusCode = $response->getStatusCode();

            if ($statusCode < 200 || $statusCode >= 300) {
                $responseBody = $response->getBody()->getContents();
                // 尝试解析错误信息
                $errorMsg = "文件流上传失败，状态码: {$statusCode}";

                $json = json_decode($responseBody, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    if (isset($json['msg'])) {
                        $errorMsg .= ", 错误信息: " . $json['msg'];
                    }
                    $errorData = $json;
                }
                else {
                    $errorData = ['raw_body' => $responseBody];
                }

                throw new ESignBaoException($errorMsg, $statusCode, $errorData);
            }

        } catch (GuzzleException $e) {
            throw new ESignBaoException(
                '文件流上传网络请求失败: ' . $e->getMessage(),
                $e->getCode(),
                null,
                $e
            );
        }
    }
}
