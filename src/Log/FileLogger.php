<?php
declare(strict_types = 1);

namespace QingzeLab\ESignBao\Log;

use DateTimeImmutable;
use DateTimeZone;

/**
 * Class FileLogger
 *
 * 简易文件日志器，按行写入 JSON 格式日志。
 */
final class FileLogger implements LoggerInterface
{
    /**
     * @var string
     */
    private string $path;

    /**
     * FileLogger constructor.
     *
     * @param string $path 日志文件路径
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * 记录信息级日志。
     *
     * @param string               $message 日志消息
     * @param array<string, mixed> $context 上下文数据
     *
     * @return void
     */
    public function info(string $message, array $context = []): void
    {
        $this->write('info', $message, $context);
    }

    /**
     * 记录错误级日志。
     *
     * @param string               $message 日志消息
     * @param array<string, mixed> $context 上下文数据
     *
     * @return void
     */
    public function error(string $message, array $context = []): void
    {
        $this->write('error', $message, $context);
    }

    /**
     * 将一条日志写入文件。
     *
     * @param string               $level   日志级别
     * @param string               $message 日志消息
     * @param array<string, mixed> $context 上下文数据
     *
     * @return void
     */
    private function write(string $level, string $message, array $context): void
    {
        $record = [
            'ts'      => (new DateTimeImmutable('now', new DateTimeZone('UTC')))->format('c'),
            'level'   => $level,
            'message' => $message,
            'context' => $context,
        ];
        $line = json_encode($record, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL;
        $this->append($line);
    }

    /**
     * 追加写入文本到日志文件。
     *
     * @param string $text 文本内容
     *
     * @return void
     */
    private function append(string $text): void
    {
        $dir = dirname($this->path);
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }
        $fh = @fopen($this->path, 'ab');
        if ($fh === false) {
            return;
        }
        @flock($fh, LOCK_EX);
        @fwrite($fh, $text);
        @flock($fh, LOCK_UN);
        @fclose($fh);
    }
}

