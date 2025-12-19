<?php
declare(strict_types = 1);

namespace QingzeLab\OpenIM\Client;

use Psr\SimpleCache\CacheInterface;
use QingzeLab\OpenIM\Auth\TokenManager;
use QingzeLab\OpenIM\Cache\TokenCache;
use QingzeLab\OpenIM\Config\OpenImConfig;
use QingzeLab\OpenIM\Http\GuzzleHttpClient;
use QingzeLab\OpenIM\Service\ConversationService;
use QingzeLab\OpenIM\Service\GroupService;
use QingzeLab\OpenIM\Service\MessageService;
use QingzeLab\OpenIM\Service\UserService;

final class OpenImClient
{
    /**
     * @var GuzzleHttpClient
     */
    private GuzzleHttpClient $http;

    /**
     * @var TokenManager
     */
    private TokenManager $tokenManager;

    /**
     * @param OpenImConfig   $config
     * @param CacheInterface $cache
     */
    public function __construct(OpenImConfig $config, CacheInterface $cache)
    {
        $this->http         = new GuzzleHttpClient($config);
        $this->tokenManager = new TokenManager($config, $this->http, new TokenCache($cache));
    }

    /**
     * @return MessageService
     */
    public function messages(): MessageService
    {
        return new MessageService($this->http, $this->tokenManager);
    }

    /**
     * @return UserService
     */
    public function users(): UserService
    {
        return new UserService($this->http, $this->tokenManager);
    }

    /**
     * @return GroupService
     */
    public function groups(): GroupService
    {
        return new GroupService($this->http, $this->tokenManager);
    }

    /**
     * @return ConversationService
     */
    public function conversations(): ConversationService
    {
        return new ConversationService($this->http, $this->tokenManager);
    }
}
