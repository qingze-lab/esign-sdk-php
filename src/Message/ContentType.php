<?php
declare(strict_types = 1);

namespace QingzeLab\OpenIM\Message;

/**
 * 消息内容类型枚举，映射 OpenIM REST API 的 contentType。
 */
enum ContentType: int
{
    case TEXT = 101;// 文本消息
    case IMAGE = 102;// 图片消息
    case SOUND = 103;// 音频消息
    case VIDEO = 104;// 视频消息
    case FILE = 105;// 文件消息
    case AT = 106;// @消息
    case LOCATION = 109;// 位置消息
    case CUSTOM = 110;// 自定义消息
    case SYSTEM = 1400;// 系统通知类型消息
}

