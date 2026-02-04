<?php

declare(strict_types = 1);

namespace QingzeLab\ESignBao\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use QingzeLab\ESignBao\Config\Configuration;
use QingzeLab\ESignBao\Exceptions\ESignBaoException;

/**
 * 文件上传客户端
 * 专门用于处理文件流上传（Step 2），不包含业务API签名逻辑
 */
class UploadClient extends AbstractClient
{
    /**
     * @var Client
     */
    private Client $client;

    /**
     * @param Configuration $config
     */
    public function __construct(Configuration $config)
    {
        parent::__construct($config);
        // 创建一个干净的 Guzzle 客户端，不带任何中间件或默认配置
        $this->client = new Client([
            'http_errors'     => false,
            // 使用配置中的超时时间，或者针对上传适当延长
            'timeout'         => max($this->config->getTimeout(), 120),
            'connect_timeout' => $this->config->getConnectTimeout(),
        ]);
    }

    /**
     * 执行 PUT 上传
     *
     * @param string          $url     上传地址
     * @param string|resource $body    文件内容或资源句柄
     * @param array           $headers 请求头
     * @return array 包含状态码和响应体
     * @throws ESignBaoException
     */
    public function put(string $url, $body, array $headers): array
    {
        $operationId = $this->generateOperationId();

        try {
            $options = [
                'headers' => $headers,
                'body'    => $body,
            ];

            // 记录请求日志
            $this->log('Request', [
                'operation_id' => $operationId,
                'method'       => 'PUT',
                'uri'          => $url,
                'headers'      => $this->sanitizeHeaders($headers),
                'body'         => $body,
            ]);

            $response = $this->client->request('PUT', $url, $options);

            return $this->parseResponse($response, $operationId);
        } catch (GuzzleException $e) {
            // 记录异常日志
            $this->log('Error', [
                'operation_id' => $operationId,
                'message'      => $e->getMessage(),
                'code'         => $e->getCode(),
            ]);

            throw new ESignBaoException(
                '文件流上传网络请求失败: ' . $e->getMessage(),
                $e->getCode(),
                null,
                $e
            );
        }
    }
}
