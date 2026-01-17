<?php
declare(strict_types = 1);

namespace QingzeLab\ESignBao\Log;

/**
 * Class LaravelLogger
 *
 * Laravel/PSR-3 日志适配器，将 SDK 的日志写入到 Laravel 的日志系统。
 */
final class LaravelLogger implements LoggerInterface
{
    /**
     * @var object
     */
    private object $logger;

    /**
     * LaravelLogger constructor.
     *
     * @param object $logger Laravel 的 PSR-3 日志实例（如 Log::channel(...) 返回值）
     */
    public function __construct(object $logger)
    {
        $this->logger = $logger;
    }

    /**
     * 记录信息级日志，通过 Laravel 的 info 级别输出。
     *
     * @param string               $message 日志消息
     * @param array<string, mixed> $context 上下文数据
     *
     * @return void
     */
    public function info(string $message, array $context = []): void
    {
        $this->logger->info($message, $context);
    }

    /**
     * 记录错误级日志，通过 Laravel 的 error 级别输出。
     *
     * @param string               $message 日志消息
     * @param array<string, mixed> $context 上下文数据
     *
     * @return void
     */
    public function error(string $message, array $context = []): void
    {
        $this->logger->error($message, $context);
    }
}
