<?php

declare(strict_types = 1);

namespace QingzeLab\ESignBao\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\HandlerStack;
use QingzeLab\ESignBao\Config\Configuration;
use QingzeLab\ESignBao\Exceptions\ESignBaoException;
use QingzeLab\ESignBao\Utils\SignatureUtil;

/**
 * HTTP客户端封装类
 * 参考OpenIM SDK的HTTP客户端设计，支持重试机制
 */
class HttpClient extends AbstractClient
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
        $this->client = $this->createClient();
    }

    /**
     * 创建Guzzle客户端（带重试中间件）
     * @return Client
     */
    private function createClient(): Client
    {
        $stack = HandlerStack::create();

        // 添加重试中间件
        $retryMiddleware = new RetryMiddleware(
            $this->config->getMaxRetries(),
            $this->config->getRetryStatusCodes(),
            $this->config->getRetryDelayMs()
        );
        $stack->push($retryMiddleware);

        return new Client([
            'base_uri'        => $this->config->getApiBaseUrl(),
            'timeout'         => $this->config->getTimeout(),
            'connect_timeout' => $this->config->getConnectTimeout(),
            'handler'         => $stack,
            'http_errors'     => false,
        ]);
    }

    /**
     * 发送GET请求
     * @param string $uri
     * @param array  $params
     * @param array  $headers
     * @return array
     * @throws ESignBaoException
     */
    public function get(string $uri, array $params = [], array $headers = []): array
    {
        $queryString = !empty($params) ? '?' . http_build_query($params) : '';
        $fullUri     = $uri . $queryString;

        return $this->request('GET', $fullUri, '', $headers);
    }

    /**
     * 发送POST请求
     * @param string $uri
     * @param array  $data
     * @param array  $headers
     * @return array
     * @throws ESignBaoException
     */
    public function post(string $uri, array $data = [], array $headers = []): array
    {
        $body = json_encode($data, JSON_UNESCAPED_UNICODE);
        return $this->request('POST', $uri, $body, $headers);
    }

    /**
     * 发送PUT请求
     * @param string $uri
     * @param array  $data
     * @param array  $headers
     * @return array
     * @throws ESignBaoException
     */
    public function put(string $uri, array $data = [], array $headers = []): array
    {
        $body = json_encode($data, JSON_UNESCAPED_UNICODE);
        return $this->request('PUT', $uri, $body, $headers);
    }

    /**
     * 发送DELETE请求
     * @param string $uri
     * @param array  $headers
     * @return array
     * @throws ESignBaoException
     */
    public function delete(string $uri, array $headers = []): array
    {
        return $this->request('DELETE', $uri, '', $headers);
    }

    /**
     * 获取配置对象
     * @return Configuration
     */
    public function getConfig(): Configuration
    {
        return $this->config;
    }

    /**
     * 发送HTTP请求
     * @param string $method
     * @param string $uri
     * @param string $body
     * @param array  $customHeaders
     * @return array
     * @throws ESignBaoException
     */
    private function request(string $method, string $uri, string $body = '', array $customHeaders = []): array
    {
        $operationId = $this->generateOperationId();

        try {
            $headers = $this->buildHeaders($method, $uri, $body, $customHeaders);

            $options = [
                'headers' => $headers,
            ];

            if (!empty($body)) {
                $options['body'] = $body;
            }

            $this->log('Request', [
                'operation_id' => $operationId,
                'method'       => $method,
                'uri'          => $uri,
                'headers'      => $this->sanitizeHeaders($headers),
                'body'         => $body,
            ]);

            $response = $this->client->request($method, $uri, $options);

            return $this->parseResponse($response, $operationId);

        } catch (GuzzleException $e) {
            $this->log('Error', [
                'operation_id' => $operationId,
                'method'       => $method,
                'uri'          => $uri,
                'message'      => $e->getMessage(),
                'code'         => $e->getCode(),
            ]);

            throw new ESignBaoException(
                'HTTP请求失败: ' . $e->getMessage(),
                $e->getCode(),
                null,
                $e
            );
        }
    }

    /**
     * 构建请求头
     * @param string $method
     * @param string $uri
     * @param string $body
     * @param array  $customHeaders
     * @return array
     */
    private function buildHeaders(string $method, string $uri, string $body, array $customHeaders): array
    {
        $accept      = 'application/json';
        $contentType = 'application/json; charset=UTF-8';
        $date        = SignatureUtil::generateGMTDate();
        $contentMD5  = SignatureUtil::generateContentMD5($body);

        $pathAndParameters = SignatureUtil::buildPathAndParameters($uri);
        $formattedHeaders  = SignatureUtil::formatCustomHeaders($customHeaders);

        $signature = SignatureUtil::generateSignature(
            $this->config->getAppSecret(),
            $method,
            $accept,
            $contentMD5,
            $contentType,
            $date,
            $formattedHeaders,
            $pathAndParameters
        );

        $headers = [
            'Accept'                    => $accept,
            'Content-Type'              => $contentType,
            'Date'                      => $date,
            'X-Tsign-Open-App-Id'       => $this->config->getAppId(),
            'X-Tsign-Open-Auth-Mode'    => 'Signature',
            'X-Tsign-Open-Ca-Timestamp' => (string) round(microtime(true) * 1000),
            'X-Tsign-Open-Ca-Signature' => $signature,
        ];

        if (!empty($contentMD5)) {
            $headers['Content-MD5'] = $contentMD5;
        }

        foreach ($customHeaders as $key => $value) {
            if (str_starts_with(strtolower($key), 'x-tsign-open-')) {
                $headers[$key] = $value;
            }
        }

        return $headers;
    }
}
