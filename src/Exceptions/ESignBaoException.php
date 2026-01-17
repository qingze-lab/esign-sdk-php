<?php

declare(strict_types = 1);

namespace QingzeLab\ESignBao\Exceptions;

use Exception;
use Throwable;

/**
 * 易签宝异常类
 */
class ESignBaoException extends Exception
{
    /**
     * API响应数据
     * @var array|null
     */
    private ?array $response;

    /**
     * 构造函数
     *
     * @param string         $message
     * @param int            $code
     * @param array|null     $response
     * @param Throwable|null $previous
     */
    public function __construct(
        string     $message = "",
        int        $code = 0,
        ?array     $response = null,
        ?Throwable $previous = null
    )
    {
        parent::__construct($message, $code, $previous);
        $this->response = $response;
    }

    /**
     * 获取API响应数据
     * @return array|null
     */
    public function getResponse(): ?array
    {
        return $this->response;
    }
}