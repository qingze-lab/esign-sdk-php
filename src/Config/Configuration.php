<?php

declare(strict_types = 1);

namespace QingzeLab\ESignBao\Config;

use QingzeLab\ESignBao\Log\LoggerInterface;
use QingzeLab\ESignBao\Log\NullLogger;

/**
 * 易签宝配置类
 */
class Configuration
{
    /**
     * 应用ID
     * @var string
     */
    private string $appId;

    /**
     * 应用密钥
     * @var string
     */
    private string $appSecret;

    /**
     * 接口地址
     * @var string
     */
    private string $apiBaseUrl;

    /**
     * 请求超时时间
     * @var int
     */
    private int $timeout;

    /**
     * 连接超时时间（秒）
     * @var float
     */
    private float $connectTimeout;

    /**
     * 是否沙箱环境
     * @var bool
     */
    private bool $sandbox;

    /**
     * 重试次数
     * @var int
     */
    private int $maxRetries;

    /**
     * 重试状态码
     * @var array
     */
    private array $retryStatusCodes;

    /**
     * 重试基础延迟（毫秒）
     * @var int
     */
    private int $retryDelayMs;

    /**
     * @var LoggerInterface 日志器
     */
    private LoggerInterface $logger;

    /**
     * 构造函数
     *
     * @param array $config 配置数组
     */
    public function __construct(array $config)
    {
        // 必填参数
        $this->appId     = $config['app_id'] ?? throw new \InvalidArgumentException('app_id is required');
        $this->appSecret = $config['app_secret'] ?? throw new \InvalidArgumentException('app_secret is required');

        // 可选参数
        $this->sandbox    = $config['sandbox'] ?? false;
        $this->apiBaseUrl = $config['api_base_url'] ?? ($this->sandbox
            ? 'https://smlopenapi.esign.cn'
            : 'https://openapi.esign.cn');

        $this->timeout          = $config['timeout'] ?? 30;
        $this->connectTimeout   = $config['connect_timeout'] ?? 2.0;
        $this->maxRetries       = $config['max_retries'] ?? 3;
        $this->retryStatusCodes = $config['retry_status_codes'] ?? [408, 429, 500, 502, 503, 504];
        $this->retryDelayMs     = $config['retry_delay_ms'] ?? 200;

        $this->apiBaseUrl = rtrim($this->apiBaseUrl, '/');
        $this->logger     = new NullLogger();
    }

    /**
     * 获取应用ID
     * @return string
     */
    public function getAppId(): string
    {
        return $this->appId;
    }

    /**
     * 获取应用密钥
     * @return string
     */
    public function getAppSecret(): string
    {
        return $this->appSecret;
    }

    /**
     * 获取接口地址
     * @return string
     */
    public function getApiBaseUrl(): string
    {
        return $this->apiBaseUrl;
    }

    /**
     * 获取请求超时时间
     * @return int
     */
    public function getTimeout(): int
    {
        return $this->timeout;
    }

    /**
     * 获取连接超时时间（秒）
     * @return float
     */
    public function getConnectTimeout(): float
    {
        return $this->connectTimeout;
    }

    /**
     * 是否沙箱环境
     * @return bool
     */
    public function isSandbox(): bool
    {
        return $this->sandbox;
    }

    /**
     * 获取重试次数
     * @return int
     */
    public function getMaxRetries(): int
    {
        return $this->maxRetries;
    }

    /**
     * 获取重试状态码
     * @return array
     */
    public function getRetryStatusCodes(): array
    {
        return $this->retryStatusCodes;
    }

    /**
     * 获取重试基础延迟（毫秒）
     * @return int
     */
    public function getRetryDelayMs(): int
    {
        return $this->retryDelayMs;
    }

    /**
     * 从数组创建配置实例
     * @param array $config
     * @return Configuration
     */
    public static function fromArray(array $config): self
    {
        return new self($config);
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * @param LoggerInterface $logger
     * @return void
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}
