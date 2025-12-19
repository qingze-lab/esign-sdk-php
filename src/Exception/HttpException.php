<?php
declare(strict_types = 1);

namespace QingzeLab\OpenIM\Exception;

use RuntimeException;
use Throwable;

/**
 * Class HttpException
 *
 * HTTP 层异常（网络错误、响应解析失败等）。
 */
class HttpException extends RuntimeException
{
    /**
     * HttpException constructor.
     *
     * @param string         $message  错误信息
     * @param int            $code     错误码（可为 HTTP 状态码）
     * @param Throwable|null $previous 上一个异常
     */
    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
