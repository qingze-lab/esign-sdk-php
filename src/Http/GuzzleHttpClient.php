<?php
declare(strict_types = 1);

namespace QingzeLab\OpenIM\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use QingzeLab\OpenIM\Config\OpenImConfig;
use QingzeLab\OpenIM\Exception\HttpException;

/**
 * Class GuzzleHttpClient
 *
 * 基于 Guzzle 的高性能 HTTP 客户端实现，支持连接复用与超时控制。
 */
final class GuzzleHttpClient implements HttpClientInterface
{
    /**
     * @var Client
     */
    private Client $client;

    /**
     * @var OpenImConfig
     */
    private OpenImConfig $config;

    /**
     * GuzzleHttpClient constructor.
     *
     * @param OpenImConfig $config OpenIM 配置对象
     */
    public function __construct(OpenImConfig $config)
    {
        $this->config = $config;
        $this->client = new Client([
            'base_uri'        => $config->getBaseUrl(),
            'timeout'         => $config->getTimeout(),
            'connect_timeout' => $config->getConnectTimeout(),
            'http_errors'     => false,
            'headers'         => [
                'Accept'       => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    /**
     * @param string $url
     * @param array  $body
     * @param array  $headers
     * @return array
     * @throws JsonException
     */
    public function postJson(string $url, array $body = [], array $headers = []): array
    {
        $attempts = 0;
        $max      = max(1, $this->config->getRetryMaxAttempts());
        $delay    = max(0, $this->config->getRetryDelayMs());
        do {
            $attempts++;
            try {
                $response = $this->client->post($url, [
                    'json'    => $body,
                    'headers' => $headers,
                ]);
                $status   = $response->getStatusCode();
                $decoded  = $this->decodeResponse((string) $response->getBody(), $status, $url);
                if ($status >= 500 && $attempts < $max) {
                    usleep($delay * 1000);
                    continue;
                }
                return $decoded;
            } catch (GuzzleException $e) {
                if ($attempts < $max) {
                    usleep($delay * 1000);
                    continue;
                }
                throw new HttpException('HTTP POST 请求失败: ' . $e->getMessage(), $e->getCode(), $e);
            }
        } while ($attempts < $max);
        throw new HttpException('HTTP POST 重试失败: ' . $url);
    }

    /**
     * @param string $url
     * @param array  $query
     * @param array  $headers
     * @return array
     * @throws JsonException
     */
    public function getJson(string $url, array $query = [], array $headers = []): array
    {
        $attempts = 0;
        $max      = max(1, $this->config->getRetryMaxAttempts());
        $delay    = max(0, $this->config->getRetryDelayMs());
        do {
            $attempts++;
            try {
                $response = $this->client->get($url, [
                    'query'   => $query,
                    'headers' => $headers,
                ]);
                $status   = $response->getStatusCode();
                $decoded  = $this->decodeResponse((string) $response->getBody(), $status, $url);
                if ($status >= 500 && $attempts < $max) {
                    usleep($delay * 1000);
                    continue;
                }
                return $decoded;
            } catch (GuzzleException $e) {
                if ($attempts < $max) {
                    usleep($delay * 1000);
                    continue;
                }
                throw new HttpException('HTTP GET 请求失败: ' . $e->getMessage(), $e->getCode(), $e);
            }
        } while ($attempts < $max);
        throw new HttpException('HTTP GET 重试失败: ' . $url);
    }

    /**
     * 解码 HTTP 响应体为数组，并根据状态码抛出异常。
     *
     * @param string $body       原始响应体
     * @param int    $statusCode HTTP 状态码
     * @param string $url        请求 URL，仅用于错误信息
     *
     * @return array<string, mixed>
     * @throws JsonException
     */
    private function decodeResponse(string $body, int $statusCode, string $url): array
    {
        $decoded = json_decode($body, true, 512, JSON_THROW_ON_ERROR);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new HttpException(
                sprintf(
                    '解码 OpenIM 响应失败: %s | 状态码: %d | URL: %s',
                    json_last_error_msg(),
                    $statusCode,
                    $url
                )
            );
        }

        if ($statusCode >= 400) {
            throw new HttpException(
                sprintf(
                    'OpenIM 返回错误状态码: %d | URL: %s | 响应: %s',
                    $statusCode,
                    $url,
                    $body
                ),
                $statusCode
            );
        }

        return $decoded ?? [];
    }
}
