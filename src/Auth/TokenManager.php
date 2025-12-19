<?php
declare(strict_types = 1);

namespace QingzeLab\OpenIM\Auth;

use QingzeLab\OpenIM\Cache\TokenCache;
use QingzeLab\OpenIM\Config\OpenImConfig;
use QingzeLab\OpenIM\Exception\OpenImException;
use QingzeLab\OpenIM\Http\HttpClientInterface;

/**
 * Class TokenManager
 *
 * Token 管理器，负责从缓存获取 / 刷新 OpenIM APP 管理员 Token。
 */
final class TokenManager
{
    /**
     * @var OpenImConfig
     */
    private OpenImConfig $config;

    /**
     * @var HttpClientInterface
     */
    private HttpClientInterface $httpClient;

    /**
     * @var TokenCache
     */
    private TokenCache $tokenCache;

    /**
     * TokenManager constructor.
     *
     * @param OpenImConfig        $config     OpenIM 配置
     * @param HttpClientInterface $httpClient HTTP 客户端
     * @param TokenCache          $tokenCache Token 缓存封装
     */
    public function __construct(OpenImConfig $config, HttpClientInterface $httpClient, TokenCache $tokenCache)
    {
        $this->config     = $config;
        $this->httpClient = $httpClient;
        $this->tokenCache = $tokenCache;
    }

    /**
     * 获取有效 Token（优先使用缓存，不存在时调用 OpenIM 接口获取）。
     *
     * @return string
     */
    public function getToken(): string
    {
        $adminUserId = $this->config->getAdminUserId();

        $cached = $this->tokenCache->getToken($adminUserId);
        if ($cached !== null && $cached !== '') {
            return $cached;
        }

        $tokenData = $this->requestNewToken();

        if (!isset($tokenData['token']) || !is_string($tokenData['token'])) {
            throw new OpenImException('OpenIM user_token 接口返回格式异常，缺少 token 字段');
        }

        $token = $tokenData['token'];
        $ttl   = isset($tokenData['expireTimeSeconds']) ? (int) $tokenData['expireTimeSeconds'] : 3600;

        $this->tokenCache->setToken($adminUserId, $token, (int) floor($ttl * 0.9));

        return $token;
    }

    /**
     * 强制刷新 Token（忽略缓存，直接调用 OpenIM）。
     *
     * @return string
     */
    public function refreshToken(): string
    {
        $adminUserId = $this->config->getAdminUserId();

        $tokenData = $this->requestNewToken();

        if (!isset($tokenData['token']) || !is_string($tokenData['token'])) {
            throw new OpenImException('OpenIM user_token 接口返回格式异常，缺少 token 字段');
        }

        $token = $tokenData['token'];
        $ttl   = isset($tokenData['expireTimeSeconds']) ? (int) $tokenData['expireTimeSeconds'] : 3600;

        $this->tokenCache->setToken($adminUserId, $token, (int) floor($ttl * 0.9));

        return $token;
    }

    /**
     * 调用 OpenIM user_token 接口获取新的 APP 管理员 Token。
     * 注意：具体 URL 和参数需根据 OpenIM REST API 文档调整。
     *
     * @return array<string, mixed>
     */
    private function requestNewToken(): array
    {
        $body = [
            'secret' => $this->config->getAdminSecret(),
            'userID' => $this->config->getAdminUserId(),
        ];

        $headers = [
            'operationID' => (string) (int) floor(microtime(true) * 1000),
        ];

        $response = $this->httpClient->postJson('/auth/get_admin_token', $body, $headers);

        if (!isset($response['errCode']) || (int) $response['errCode'] !== 0) {
            $msg = $response['errMsg'] ?? '未知错误';
            throw new OpenImException('获取 OpenIM 管理员 Token 失败: ' . (string) $msg);
        }

        /** @var array<string, mixed> $data */
        $data = $response['data'] ?? [];

        return $data;
    }
}
