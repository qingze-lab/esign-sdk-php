<?php

require_once __DIR__ . '/../vendor/autoload.php';

use QingzeLab\ESignBao\Client;
use QingzeLab\ESignBao\Exceptions\ESignBaoException;

// 初始化客户端
$client = new Client([
    'app_id'      => 'your_app_id',
    'app_secret'  => 'your_app_secret',
    'sandbox'     => true,
    'max_retries' => 3,
    'enable_log'  => true,
    'log_path'    => __DIR__ . '/../logs/esignbao.log',
]);

try {
    echo "========== 实名认证链接获取示例 ==========\n\n";

    // 1. 获取个人认证链接
    echo "1. 获取个人认证链接...\n";
    $psnAuthUrl = $client->auth()->getPersonAuthUrl(
        psnAuthConfig: [
            'psnAccount' => '13800138000'
        ],
        authorizeConfig: [
            'authorizedScopes' => ['get_psn_identity_info']
        ],
        redirectConfig: [
            'redirectUrl' => 'https://example.com/callback'
        ]
    );
    echo "个人认证链接: " . ($psnAuthUrl['data']['authUrl'] ?? '获取失败') . "\n\n";


    // 2. 获取机构认证链接
    echo "2. 获取机构认证链接...\n";
    $orgAuthUrl = $client->auth()->getOrganizationAuthUrl(
        orgAuthConfig: [
            'orgName' => '测试科技有限公司'
        ],
        transactorInfo: [
            'psnAccount' => '13800138000'
        ],
        redirectConfig: [
            'redirectUrl' => 'https://example.com/callback'
        ]
    );
    echo "机构认证链接: " . ($orgAuthUrl['data']['authUrl'] ?? '获取失败') . "\n\n";

    // 假设已知 authFlowId
    if (isset($psnAuthUrl['data']['authFlowId'])) {
        $authFlowId = $psnAuthUrl['data']['authFlowId'];
        echo "3. 查询认证授权流程详情 (flowId: {$authFlowId})...\n";
        $flowDetail = $client->auth()->getAuthFlowDetail($authFlowId);
        echo "流程状态: " . ($flowDetail['data']['realNameStatus'] ?? '未知') . "\n\n";
    }

    echo "========== 认证信息查询示例 ==========\n\n";

    // 4. 查询个人认证信息（通过手机号）
    echo "4. 查询个人认证信息（通过手机号）...\n";
    $identityInfo = $client->auth()->getPersonIdentityInfo(psnAccount: '13800138000');
    echo "查询结果: " . json_encode($identityInfo, JSON_UNESCAPED_UNICODE) . "\n\n";

    // 5. 查询企业认证信息
    echo "5. 查询企业认证信息...\n";
    // 假设已知 orgId
    // $orgIdentityInfo = $client->auth()->getOrganizationIdentityInfo(orgId: 'org_xxxx');
    // echo "认证状态: " . ($orgIdentityInfo['data']['realnameStatus'] ?? '未认证') . "\n\n";

    echo "========== 所有测试完成 ==========\n";

} catch (ESignBaoException $e) {
    echo "\n❌ 错误: {$e->getMessage()}\n";
    echo "错误码: {$e->getCode()}\n";

    if ($response = $e->getResponse()) {
        echo "响应详情:\n";
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n";
    }
} catch (Exception $e) {
    echo "\n❌ 系统错误: {$e->getMessage()}\n";
}
