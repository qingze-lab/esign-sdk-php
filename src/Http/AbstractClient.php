<?php

declare(strict_types = 1);

namespace QingzeLab\ESignBao\Http;

use Exception;
use Psr\Http\Message\ResponseInterface;
use QingzeLab\ESignBao\Config\Configuration;
use QingzeLab\ESignBao\Exceptions\ESignBaoException;

abstract class AbstractClient
{
    /**
     * @var Configuration
     */
    protected Configuration $config;


    /**
     * @param Configuration $config
     */
    public function __construct(Configuration $config)
    {
        $this->config = $config;
    }

    /**
     * 解析响应
     * @param ResponseInterface $response
     * @param string            $operationId
     * @return array
     * @throws ESignBaoException
     */
    protected function parseResponse(ResponseInterface $response, string $operationId): array
    {
        $statusCode = $response->getStatusCode();
        $body       = $response->getBody()->getContents();

        $this->log('Response', [
            'operation_id' => $operationId,
            'status_code'  => $statusCode,
            'body'         => $body,
        ]);

        if ($statusCode < 200 || $statusCode >= 300) {
            throw new ESignBaoException(
                '请求失败',
                $statusCode,
            );
        }

        $data = json_decode($body, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ESignBaoException(
                '响应解析失败: ' . json_last_error_msg(),
                $statusCode,
                ['raw_body' => $body]
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
    protected function sanitizeHeaders(array $headers): array
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
    protected function generateOperationId(): string
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
    protected function log(string $type, array $data): void
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