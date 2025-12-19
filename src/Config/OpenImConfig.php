<?php
declare(strict_types = 1);

namespace QingzeLab\OpenIM\Config;

/**
 * Class OpenImConfig
 *
 * OpenIM SDK 配置对象，封装基础 URL、管理员账号信息以及 HTTP 超时等参数。
 * 注意：userTokenEndpoint 和管理员凭证字段需根据实际 OpenIM REST API 文档调整。
 */
final class OpenImConfig
{
    /**
     * @var string OpenIM Server 对外 API 地址，例如：http://{your_im_server_ip}:10002
     */
    private string $baseUrl;

    /**
     * @var string APP 管理员 userID，默认通常为 imAdmin
     */
    private string $adminUserId;

    /**
     * @var string APP 管理员密码或鉴权凭证
     */
    private string $adminSecret;

    /**
     * @var float 请求超时时间（秒）
     */
    private float $timeout;

    /**
     * @var float 连接超时时间（秒）
     */
    private float $connectTimeout;

    /**
     * @var int 重试最大次数
     */
    private int $retryMaxAttempts;

    /**
     * @var int 重试间隔时间（毫秒）
     */
    private int $retryDelayMs;

    /**
     * OpenImConfig constructor.
     *
     * @param string $baseUrl        OpenIM Server API 地址
     * @param string $adminUserId    APP 管理员 userID
     * @param string $adminSecret    APP 管理员密码或鉴权凭证
     * @param float  $timeout        请求超时时间（秒）
     * @param float  $connectTimeout 连接超时时间（秒）
     */
    public function __construct(
        string $baseUrl,
        string $adminUserId,
        string $adminSecret,
        float  $timeout = 5.0,
        float  $connectTimeout = 2.0,
        int    $retryMaxAttempts = 3,
        int    $retryDelayMs = 200
    )
    {
        $this->baseUrl          = rtrim($baseUrl, '/');
        $this->adminUserId      = $adminUserId;
        $this->adminSecret      = $adminSecret;
        $this->timeout          = $timeout;
        $this->connectTimeout   = $connectTimeout;
        $this->retryMaxAttempts = $retryMaxAttempts;
        $this->retryDelayMs     = $retryDelayMs;
    }

    /**
     * 获取 OpenIM Server API 地址。
     *
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * 获取 APP 管理员 userID。
     *
     * @return string
     */
    public function getAdminUserId(): string
    {
        return $this->adminUserId;
    }

    /**
     * 获取 APP 管理员密码或鉴权凭证。
     *
     * @return string
     */
    public function getAdminSecret(): string
    {
        return $this->adminSecret;
    }

    /**
     * 获取请求超时时间（秒）。
     *
     * @return float
     */
    public function getTimeout(): float
    {
        return $this->timeout;
    }

    /**
     * 获取连接超时时间（秒）。
     *
     * @return float
     */
    public function getConnectTimeout(): float
    {
        return $this->connectTimeout;
    }

    /**
     * 获取重试最大次数。
     *
     * @return int
     */
    public function getRetryMaxAttempts(): int
    {
        return $this->retryMaxAttempts;
    }

    /**
     * 获取重试间隔时间（毫秒）。
     *
     * @return int
     */
    public function getRetryDelayMs(): int
    {
        return $this->retryDelayMs;
    }
}
