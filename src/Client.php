<?php

declare(strict_types = 1);

namespace QingzeLab\ESignBao;

use QingzeLab\ESignBao\Config\Configuration;
use QingzeLab\ESignBao\Http\HttpClient;
use QingzeLab\ESignBao\Services\AuthService;
use QingzeLab\ESignBao\Services\FileService;
use QingzeLab\ESignBao\Services\SignFlowService;

/**
 * 易签宝SDK主客户端类
 * 参考OpenIM SDK的Client设计
 *
 * @example
 * $config = new Configuration('your_app_id', 'your_app_secret', 'https://smlopenapi.esign.cn');
 * $client = new Client($config);
 *
 * // 实名认证链接获取
 * $result = $client->auth()->getPersonAuthUrl(['psnAccount' => '...']);
 */
class Client
{
    /**
     * @var Configuration
     */
    private Configuration $config;

    /**
     * @var HttpClient
     */
    private HttpClient $httpClient;

    /**
     * @var AuthService|null
     */
    private ?AuthService $authService = null;

    /**
     * @var SignFlowService|null
     */
    private ?SignFlowService $signFlowService = null;

    /**
     * @var FileService|null
     */
    private ?FileService $fileService = null;

    /**
     * 构造函数
     *
     * @param Configuration $config 配置对象
     */
    public function __construct(Configuration $config)
    {
        $this->config     = $config;
        $this->httpClient = new HttpClient($this->config);
    }

    /**
     * 获取实名认证服务
     */
    public function auth(): AuthService
    {
        if ($this->authService === null) {
            $this->authService = new AuthService($this->httpClient);
        }
        return $this->authService;
    }

    /**
     * 获取签署流程服务
     */
    public function signFlow(): SignFlowService
    {
        if ($this->signFlowService === null) {
            $this->signFlowService = new SignFlowService($this->httpClient);
        }
        return $this->signFlowService;
    }

    /**
     * 获取文件服务
     */
    public function file(): FileService
    {
        if ($this->fileService === null) {
            $this->fileService = new FileService($this->httpClient);
        }
        return $this->fileService;
    }

    /**
     * 获取配置
     */
    public function getConfig(): Configuration
    {
        return $this->config;
    }

    /**
     * 获取HTTP客户端
     */
    public function getHttpClient(): HttpClient
    {
        return $this->httpClient;
    }
}
