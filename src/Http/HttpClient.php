<?php

declare(strict_types = 1);

namespace QingzeLab\ESignBao\Http;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\HandlerStack;
use Psr\Http\Message\ResponseInterface;
use QingzeLab\ESignBao\Config\Configuration;
use QingzeLab\ESignBao\Exceptions\ESignBaoException;
use QingzeLab\ESignBao\Utils\SignatureUtil;

/**
 * HTTP客户端封装类
 * 参考OpenIM SDK的HTTP客户端设计，支持重试机制
 */
class HttpClient
{
    /**
     * @var Client
     */
    private Client $client;

    /**
     * @var Configuration
     */
    private Configuration $config;


    /**
     * @param Configuration $config
     */
    public function __construct(Configuration $config)
    {
        $this->config = $config;
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
            'X-Tsign-Open-Ca-Timestamp' => (string)round(microtime(true) * 1000),
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

    /**
     * 解析响应
     * @param ResponseInterface $response
     * @param string            $operationId
     * @return array
     * @throws ESignBaoException
     */
    private function parseResponse(ResponseInterface $response, string $operationId): array
    {
        $statusCode = $response->getStatusCode();
        $body       = $response->getBody()->getContents();

        $this->log('Response', [
            'operation_id' => $operationId,
            'status_code'  => $statusCode,
            'body'         => $body,
        ]);

        $data = json_decode($body, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ESignBaoException(
                '响应解析失败: ' . json_last_error_msg(),
                $statusCode,
                ['raw_body' => $body]
            );
        }

        if ($statusCode < 200 || $statusCode >= 300) {
            throw new ESignBaoException(
                $data['message'] ?? '请求失败',
                $data['code'] ?? $statusCode,
                $data
            );
        }

        if (isset($data['code']) && $data['code'] !== 0) {
            throw new ESignBaoException(
                $data['message'] ?? '业务处理失败',
                $data['code'],
                $data
            );
        }

        return $data;
    }

    /**
     * 脱敏请求头（隐藏敏感信息）
     * @param array $headers
     * @return array
     */
    private function sanitizeHeaders(array $headers): array
    {
        $sanitized = $headers;
        if (isset($sanitized['X-Tsign-Open-Ca-Signature'])) {
            $sanitized['X-Tsign-Open-Ca-Signature'] = '***';
        }
        return $sanitized;
    }

    /**
     * 生成操作ID用于日志关联
     * @return string
     */
    private function generateOperationId(): string
    {
        try {
            return bin2hex(random_bytes(8));
        } catch (Exception) {
            return uniqid('', true);
        }
    }

    /**
     * 记录日志
     * @param string $type
     * @param array  $data
     */
    private function log(string $type, array $data): void
    {
        if ($this->config->getLogger() !== null) {
            switch ($type) {
                case 'Error':
                    $this->config->getLogger()->error('HTTP Error', $data);
                    break;
                case 'Request':
                    $this->config->getLogger()->info('HTTP Request', $data);
                    break;
                case 'Response':
                default:
                    $this->config->getLogger()->info('HTTP Response', $data);
                    break;
            }
        }
    }
}
