<?php
declare(strict_types = 1);

namespace QingzeLab\OpenIM\Http;

/**
 * Interface HttpClientInterface
 *
 * 抽象 HTTP 客户端接口，便于替换具体实现（Guzzle / cURL 等）。
 */
interface HttpClientInterface
{
    /**
     * 发送 JSON POST 请求。
     *
     * @param string               $url     请求相对或绝对 URL
     * @param array<string, mixed> $body    请求体数组，会被编码为 JSON
     * @param array<string, mixed> $headers 额外请求头
     *
     * @return array<string, mixed> 响应数据（已解码为数组）
     */
    public function postJson(string $url, array $body = [], array $headers = []): array;

    /**
     * 发送 JSON GET 请求。
     *
     * @param string               $url     请求相对或绝对 URL
     * @param array<string, mixed> $query   查询参数
     * @param array<string, mixed> $headers 额外请求头
     *
     * @return array<string, mixed> 响应数据（已解码为数组）
     */
    public function getJson(string $url, array $query = [], array $headers = []): array;
}
