<?php

require_once __DIR__ . '/../vendor/autoload.php';

use QingzeLab\ESignBao\Client;
use QingzeLab\ESignBao\Config\Configuration;
use QingzeLab\ESignBao\Exceptions\ESignBaoException;

// 替换为您的应用配置
$appId = 'your_app_id';
$appSecret = 'your_app_secret';
$baseUrl = 'https://smlopenapi.esign.cn'; // 沙箱环境

try {
    echo "初始化客户端...\n";
    $config = new Configuration($appId, $appSecret, $baseUrl);
    $client = new Client($config);

    // ==========================================
    // 示例1: 获取个人认证链接 (包含完整参数)
    // ==========================================
    echo "\n----------------------------------------\n";
    echo "示例1: 获取个人认证链接\n";
    
    // 1. 个人认证配置
    $psnAuthConfig = [
        'psnAccount' => '188' . rand(10000000, 99999999), // 手机号
    ];
    
    // 2. 授权配置
    $authorizeConfig = [
        'authorizedScopes' => ['get_psn_identity_info']
    ];
    
    // 3. 重定向配置
    $redirectConfig = [
        'redirectUrl' => 'https://www.your-site.com/callback'
    ];
    
    // 4. 通知地址
    $notifyUrl = 'https://www.your-site.com/notify';
    
    // 5. 客户端类型
    $clientType = 'ALL';
    
    // 调用接口
    // $response = $client->auth()->getPersonAuthUrl(
    //     $psnAuthConfig, 
    //     $authorizeConfig, 
    //     $redirectConfig, 
    //     $notifyUrl, 
    //     $clientType
    // );
    // print_r($response);
    echo "代码示例已更新，请填入真实 AppID 运行。\n";


    // ==========================================
    // 示例2: 获取机构认证链接
    // ==========================================
    echo "\n----------------------------------------\n";
    echo "示例2: 获取机构认证链接\n";

    $orgAuthConfig = [
        'orgName' => '测试企业' . rand(1000, 9999),
    ];

    // $response = $client->auth()->getOrganizationAuthUrl(
    //     $orgAuthConfig,
    //     null, // transactorInfo
    //     $authorizeConfig,
    //     $redirectConfig,
    //     $notifyUrl,
    //     $clientType
    // );
    // print_r($response);

} catch (ESignBaoException $e) {
    echo "请求失败: " . $e->getMessage() . "\n";
    if ($e->getResponse()) {
        echo "Response: " . json_encode($e->getResponse(), JSON_UNESCAPED_UNICODE) . "\n";
    }
}
