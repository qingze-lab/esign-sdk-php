<?php

declare(strict_types = 1);

namespace QingzeLab\ESignBao\Http;

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\Create;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * HTTP重试中间件
 * 参考OpenIM SDK的重试机制设计
 */
class RetryMiddleware
{
    /**
     * 最大重试次数
     * @var int
     */
    private int $maxRetries;

    /**
     * 需要重试的HTTP状态码
     * @var array
     */
    private array $retryStatusCodes;

    /**
     * 延迟时间（毫秒）
     * @var int
     */
    private int $retryDelay;

    /**
     * @param int   $maxRetries       最大重试次数
     * @param array $retryStatusCodes 需要重试的HTTP状态码
     * @param int   $retryDelay       重试延迟（毫秒）
     */
    public function __construct(
        int   $maxRetries = 3,
        array $retryStatusCodes = [408, 429, 500, 502, 503, 504],
        int   $retryDelay = 200
    )
    {
        $this->maxRetries       = $maxRetries;
        $this->retryStatusCodes = $retryStatusCodes;
        $this->retryDelay       = $retryDelay;
    }

    /**
     * 创建Guzzle中间件
     * @param callable $handler
     * @return callable
     */
    public function __invoke(callable $handler): callable
    {
        return function (RequestInterface $request, array $options) use ($handler) {
            $retries = 0;

            $retry = function () use ($handler, $request, $options, &$retries, &$retry) {
                return $handler($request, $options)->then(
                    function (ResponseInterface $response) use (&$retries, &$retry) {
                        // 检查是否需要重试
                        if ($this->shouldRetry($response, $retries)) {
                            $retries++;
                            $this->delay($retries);
                            return $retry();
                        }
                        return $response;
                    },
                    function ($reason) use (&$retries, &$retry) {
                        // 处理异常情况
                        if ($this->shouldRetryException($reason, $retries)) {
                            $retries++;
                            $this->delay($retries);
                            return $retry();
                        }
                        return Create::rejectionFor($reason);
                    }
                );
            };

            return $retry();
        };
    }

    /**
     * 判断是否应该重试
     * @param ResponseInterface $response
     * @param int               $retries
     * @return bool
     */
    private function shouldRetry(ResponseInterface $response, int $retries): bool
    {
        if ($retries >= $this->maxRetries) {
            return false;
        }

        return in_array($response->getStatusCode(), $this->retryStatusCodes, true);
    }

    /**
     * 判断异常是否应该重试
     * @param     $reason
     * @param int $retries
     * @return bool
     */
    private function shouldRetryException($reason, int $retries): bool
    {
        if ($retries >= $this->maxRetries) {
            return false;
        }

        // 连接异常或请求异常时重试
        return $reason instanceof ConnectException ||
            ($reason instanceof RequestException && $reason->hasResponse());
    }

    /**
     * 延迟执行（指数退避）
     * @param int $retries
     */
    private function delay(int $retries): void
    {
        $delay = $this->retryDelay * (2 ** ($retries - 1));
        usleep($delay * 1000); // 转换为微秒
    }
}
