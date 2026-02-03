<?php

declare(strict_types = 1);

namespace QingzeLab\ESignBao\Utils;

/**
 * 签名工具类
 * 实现易签宝请求头签名鉴权方式
 */
class SignatureUtil
{
    /**
     * 生成Content-MD5
     *
     * @param string $content 请求体内容
     * @return string Base64编码的MD5值
     */
    public static function generateContentMD5(string $content): string
    {
        if (empty($content)) {
            return '';
        }
        return base64_encode(md5($content, true));
    }

    /**
     * 生成文件的Content-MD5
     * 建议大文件使用此方法，避免读取文件内容到内存
     *
     * @param string $filePath 文件路径
     * @return string Base64编码的MD5值
     */
    public static function generateFileMD5(string $filePath): string
    {
        if (!file_exists($filePath)) {
            return '';
        }
        $md5Bytes = md5_file($filePath, true);
        return base64_encode($md5Bytes);
    }

    /**
     * 生成请求签名
     * 按照易签宝官方文档的签名算法
     *
     * @param string $appSecret   应用密钥
     * @param string $method      HTTP方法
     * @param string $accept      Accept头
     * @param string $contentMD5  Content-MD5值
     * @param string $contentType Content-Type
     * @param string $date        请求时间
     * @param string $headers     自定义头部（可选）
     * @param string $url         请求路径（不含域名和query参数）
     * @return string 签名字符串
     */
    public static function generateSignature(
        string $appSecret,
        string $method,
        string $accept,
        string $contentMD5,
        string $contentType,
        string $date,
        string $headers,
        string $url
    ): string
    {
        // 构建待签名字符串
        // 格式：HTTP方法 + "\n" + Accept + "\n" + Content-MD5 + "\n" + Content-Type + "\n" + Date + "\n" + Headers + URL
        $stringToSign = strtoupper($method) . "\n"
            . $accept . "\n"
            . $contentMD5 . "\n"
            . $contentType . "\n"
            . $date . "\n";

        if (!empty($headers)) {
            $stringToSign .= $headers . "\n";
        }

        $stringToSign .= $url;

        // 使用HMAC-SHA256算法生成签名
        $signature = hash_hmac('sha256', $stringToSign, $appSecret, true);

        // Base64编码
        return base64_encode($signature);
    }

    /**
     * 生成GMT格式的时间字符串
     *
     * @param int|null $timestamp 时间戳，默认为当前时间
     * @return string GMT格式时间
     */
    public static function generateGMTDate(?int $timestamp = null): string
    {
        if ($timestamp === null) {
            $timestamp = time();
        }
        return gmdate('D, d M Y H:i:s', $timestamp) . ' GMT';
    }

    /**
     * 格式化自定义头部
     * 将自定义头部按照规则排序并格式化
     *
     * @param array $headers 自定义头部数组
     * @return string 格式化后的头部字符串
     */
    public static function formatCustomHeaders(array $headers): string
    {
        if (empty($headers)) {
            return '';
        }

        // 筛选出X-Tsign-Open-开头的头部
        $customHeaders = [];
        foreach ($headers as $key => $value) {
            $lowerKey = strtolower($key);
            if (str_starts_with($lowerKey, 'x-tsign-open-')) {
                $customHeaders[$lowerKey] = trim($value);
            }
        }

        if (empty($customHeaders)) {
            return '';
        }

        // 按字典序排序
        ksort($customHeaders);

        // 格式化为 key:value\n 的形式
        $formatted = [];
        foreach ($customHeaders as $key => $value) {
            $formatted[] = $key . ':' . $value;
        }

        return implode("\n", $formatted);
    }

    /**
     * 构建PathAndParameters
     * 包含Path和Query中的所有参数，Query参数需按字典升序排序
     *
     * @param string $url 完整URL或路径
     * @return string
     */
    public static function buildPathAndParameters(string $url): string
    {
        $parsedUrl = parse_url($url);
        $path      = $parsedUrl['path'] ?? '/';
        $query     = $parsedUrl['query'] ?? '';

        if (empty($query)) {
            return $path;
        }

        // 解析查询参数
        parse_str($query, $queryParams);

        // 按字典序排序
        ksort($queryParams);

        // 重建查询字符串
        // 注意：这里需要保持与发送请求时一致的编码方式
        // http_build_query 会进行URL编码
        $sortedQuery = http_build_query($queryParams);

        return $path . '?' . $sortedQuery;
    }
}