<?php

require_once __DIR__ . '/../vendor/autoload.php';

use QingzeLab\ESignBao\Client;
use QingzeLab\ESignBao\Config\Configuration;
use QingzeLab\ESignBao\Exceptions\ESignBaoException;

// 用户提供的沙箱配置
$config = [
    'app_id'     => 'your_app_id',
    'app_secret' => 'your_app_secret',
    'sandbox'    => true,
    'debug'      => true, // 开启调试模式（如果支持）
];

try {
    echo "初始化客户端...\n";
    $client = new Client($config);
    
    echo "配置信息:\n";
    echo "AppID: " . $client->getConfig()->getAppId() . "\n";
    echo "BaseURL: " . $client->getConfig()->getApiBaseUrl() . "\n";
    echo "Sandbox: " . ($client->getConfig()->isSandbox() ? 'Yes' : 'No') . "\n";

    // 测试1: 获取个人认证链接 (无需真实用户，仅测试鉴权)
    // 接口: /v3/psn-auth-url (POST)
    echo "\n----------------------------------------\n";
    echo "测试1: 获取个人认证链接 (POST /v3/psn-auth-url)\n";
    
    // 使用一个随机的手机号或简单的配置来触发请求
    // 注意：沙箱环境可能对手机号有格式校验，但主要为了测试鉴权是否通过
    $psnAccount = '138' . rand(10000000, 99999999);
    
    // 构造请求参数
    // 根据文档: https://open.esign.cn/doc/opendoc/auth3/rx8igf
    $psnAuthConfig = [
        'psnAccount' => $psnAccount,
    ];
    
    // 使用 AuthService 调用
    // 接口文档: https://open.esign.cn/doc/opendoc/auth3/rx8igf
    $psnAuthConfig = [
        'psnAccount' => $psnAccount,
    ];
    
    // 注意：沙箱环境可能需要更详细的配置，这里仅测试最简参数以验证鉴权通过
    // 如果鉴权失败，会抛出 401 错误
    // 如果参数校验失败，会抛出 400 错误 (但鉴权已通过)
    
    $response = $client->auth()->getPersonAuthUrl($psnAuthConfig);
    
    echo "请求成功!\n";
    print_r($response);

} catch (ESignBaoException $e) {
    echo "请求失败: " . $e->getMessage() . "\n";
    echo "Code: " . $e->getCode() . "\n";
    echo "Response: " . print_r($e->getResponse(), true) . "\n"; // 使用 getResponse()
    
    if ($e->getPrevious()) {
        echo "Previous Exception: " . $e->getPrevious()->getMessage() . "\n";
    }
} catch (Exception $e) {
    echo "未知错误: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
