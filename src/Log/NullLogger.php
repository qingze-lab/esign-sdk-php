<?php
declare(strict_types = 1);

namespace QingzeLab\ESignBao\Log;

/**
 * Class NullLogger
 *
 * 空实现日志器，不做任何输出。
 */
final class NullLogger implements LoggerInterface
{
    /**
     * 记录信息级日志（空实现）。
     *
     * @param string               $message 日志消息
     * @param array<string, mixed> $context 上下文数据
     *
     * @return void
     */
    public function info(string $message, array $context = []): void
    {
    }

    /**
     * 记录错误级日志（空实现）。
     *
     * @param string               $message 日志消息
     * @param array<string, mixed> $context 上下文数据
     *
     * @return void
     */
    public function error(string $message, array $context = []): void
    {
    }
}

