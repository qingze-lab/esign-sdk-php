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
    echo "========== 个人账号管理示例 ==========\n\n";

    // 1. 创建个人账号
    echo "1. 创建个人账号...\n";
    $accountResult = $client->auth()->createPersonAccount(
        thirdPartyUserId: 'user_' . time(),
        psnAccount: '13800138000',
        name: '张三',
        idNumber: '110101199001011234'
    );

    if (isset($accountResult['data']['psnId'])) {
        $psnId = $accountResult['data']['psnId'];
        echo "✓ 个人账号创建成功，psnId: {$psnId}\n\n";
    }

    // 2. 查询个人认证信息（通过psnId）
    if (isset($psnId)) {
        echo "2. 查询个人认证信息（通过psnId）...\n";
        $identityInfo = $client->auth()->getPersonIdentityInfo(psnId: $psnId);
        echo "认证状态: " . ($identityInfo['data']['realnameStatus'] ?? '未认证') . "\n\n";
    }

    // 3. 查询个人认证信息（通过手机号）
    echo "3. 查询个人认证信息（通过手机号）...\n";
    $identityInfo2 = $client->auth()->getPersonIdentityInfo(psnAccount: '13800138000');
    echo "查询结果: " . json_encode($identityInfo2, JSON_UNESCAPED_UNICODE) . "\n\n";

    // 4. 查询个人认证信息（通过身份证号）
    echo "4. 查询个人认证信息（通过身份证号）...\n";
    $identityInfo3 = $client->auth()->getPersonIdentityInfo(
        psnIDCardNum: '110101199001011234',
        psnIDCardType: 'CRED_PSN_CH_IDCARD'
    );
    echo "查询结果: " . json_encode($identityInfo3, JSON_UNESCAPED_UNICODE) . "\n\n";

    echo "\n========== 企业账号管理示例 ==========\n\n";

    // 5. 创建企业账号
    echo "5. 创建企业账号...\n";
    $orgResult = $client->auth()->createOrganizationAccount(
        thirdPartyUserId: 'org_' . time(),
        orgName: '测试科技有限公司',
        orgIDCardNum: '91110000000000000X'
    );

    if (isset($orgResult['data']['orgId'])) {
        $orgId = $orgResult['data']['orgId'];
        echo "✓ 企业账号创建成功，orgId: {$orgId}\n\n";
    }

    // 6. 查询企业认证信息
    if (isset($orgId)) {
        echo "6. 查询企业认证信息...\n";
        $orgIdentityInfo = $client->auth()->getOrganizationIdentityInfo(orgId: $orgId);
        echo "认证状态: " . ($orgIdentityInfo['data']['realnameStatus'] ?? '未认证') . "\n\n";
    }

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