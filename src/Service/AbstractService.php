<?php
declare(strict_types = 1);

namespace QingzeLab\OpenIM\Service;

use QingzeLab\OpenIM\Auth\TokenManager;
use QingzeLab\OpenIM\Http\HttpClientInterface;

/**
 * Class AbstractService
 *
 * OpenIM 领域服务基类，封装公共的请求构建逻辑。
 */
abstract class AbstractService
{
    /**
     * @var HttpClientInterface
     */
    protected HttpClientInterface $httpClient;

    /**
     * @var TokenManager
     */
    protected TokenManager $tokenManager;

    /**
     * AbstractService constructor.
     *
     * @param HttpClientInterface $httpClient   HTTP 客户端
     * @param TokenManager        $tokenManager Token 管理器
     */
    public function __construct(HttpClientInterface $httpClient, TokenManager $tokenManager)
    {
        $this->httpClient   = $httpClient;
        $this->tokenManager = $tokenManager;
    }

    /**
     * 发送携带 Token 的 POST JSON 请求。
     *
     * @param string               $path API 相对路径（例如 /user/create）
     * @param array<string, mixed> $body 请求体
     *
     * @return array<string, mixed> 响应数据
     */
    protected function post(string $path, array $body = []): array
    {
        $token = $this->tokenManager->getToken();

        $headers = [
            'token'       => $token,
            'operationID' => (string) (int) floor(microtime(true) * 1000),
        ];

        return $this->httpClient->postJson($path, $body, $headers);
    }

    /**
     * 发送携带 Token 的 GET JSON 请求。
     *
     * @param string               $path  API 相对路径（例如 /user/get）
     * @param array<string, mixed> $query 查询参数
     *
     * @return array<string, mixed> 响应数据
     */
    protected function get(string $path, array $query = []): array
    {
        $token = $this->tokenManager->getToken();

        $headers = [
            'token'       => $token,
            'operationID' => (string) (int) floor(microtime(true) * 1000),
        ];

        return $this->httpClient->getJson($path, $query, $headers);
    }
}
