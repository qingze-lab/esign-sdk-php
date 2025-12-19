<?php
declare(strict_types = 1);

namespace QingzeLab\OpenIM\Message;

/**
 * 会话类型枚举，映射 OpenIM 的 sessionType。
 */
enum SessionType: int
{
    case SINGLE = 1;// 单聊
    case GROUP = 2;// 群聊
}

