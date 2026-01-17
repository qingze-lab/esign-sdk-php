<?php
declare(strict_types = 1);

namespace QingzeLab\ESignBao\Log;

/**
 * Interface LoggerInterface
 *
 * 日志接口，提供基础的 info 与 error 方法。
 */
interface LoggerInterface
{
    /**
     * 记录信息级日志。
     *
     * @param string               $message 日志消息
     * @param array<string, mixed> $context 上下文数据
     *
     * @return void
     */
    public function info(string $message, array $context = []): void;

    /**
     * 记录错误级日志。
     *
     * @param string               $message 日志消息
     * @param array<string, mixed> $context 上下文数据
     *
     * @return void
     */
    public function error(string $message, array $context = []): void;
}

