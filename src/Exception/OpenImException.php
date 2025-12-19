<?php
declare(strict_types = 1);

namespace QingzeLab\OpenIM\Exception;

use RuntimeException;
use Throwable;

/**
 * Class OpenImException
 *
 * OpenIM SDK 通用业务异常。
 */
class OpenImException extends RuntimeException
{
    /**
     * OpenImException constructor.
     *
     * @param string         $message  错误信息
     * @param int            $code     错误码
     * @param Throwable|null $previous 上一个异常
     */
    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
