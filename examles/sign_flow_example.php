<?php

require_once __DIR__ . '/../vendor/autoload.php';

use QingzeLab\ESignBao\Client;
use QingzeLab\ESignBao\Exceptions\ESignBaoException;
use QingzeLab\ESignBao\Services\SignFlowService;

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
    echo "========== 签署流程示例 ==========\n\n";

    // ===== 方式1：通过上传文件创建签署流程 =====
    echo "方式1：通过上传文件创建签署流程\n\n";

    // 1. 上传文件
    echo "1. 上传PDF文件...\n";
    $pdfPath = __DIR__ . '/test_contract.pdf';

    if (!file_exists($pdfPath)) {
        echo "⚠ 文件不存在: {$pdfPath}\n";
        echo "请准备一个测试PDF文件\n\n";
    }
    else {
        $fileResult = $client->file()->uploadFileByPath($pdfPath);
        $fileId     = $fileResult['data']['fileId'];
        echo "✓ 文件上传成功，fileId: {$fileId}\n\n";

        // 2. 构建签署方
        echo "2. 构建签署方信息...\n";

        // 假设已有个人账号ID
        $psnId1 = 'your_psn_id_1'; // 替换为实际的个人账号ID
        $psnId2 = 'your_psn_id_2';

        $signers = [
            SignFlowService::buildPersonSigner(
                psnId: $psnId1,
                psnAccount: '13800138000',
                signOrder: 1,
                signFields: [
                    SignFlowService::buildSignField(
                        fileId: 0,
                        autoExecute: false,
                        posPage: 1,
                        posX: 0.2,
                        posY: 0.8,
                        width: 0.15,
                        height: 0.08
                    ),
                ]
            ),
            SignFlowService::buildPersonSigner(
                psnId: $psnId2,
                psnAccount: '13900139000',
                signOrder: 2,
                signFields: [
                    SignFlowService::buildSignField(
                        fileId: 0,
                        autoExecute: false,
                        posPage: 1,
                        posX: 0.6,
                        posY: 0.8,
                        width: 0.15,
                        height: 0.08
                    ),
                ]
            ),
        ];
        echo "✓ 签署方信息构建完成\n\n";

        // 3. 创建签署流程
        echo "3. 创建签署流程...\n";
        $flowResult = $client->signFlow()->createByFile(
            docs: [
                [
                    'fileId'   => $fileId,
                    'fileName' => '测试合同.pdf',
                ],
            ],
            signFlowTitle: '测试合同签署-' . date('YmdHis'),
            signFlowConfig: [
                'autoFinish' => true, // 自动完结
                'notifyUrl'  => 'https://your-domain.com/callback',
            ],
            signers: $signers
        );

        $signFlowId = $flowResult['data']['signFlowId'];
        echo "✓ 签署流程创建成功，signFlowId: {$signFlowId}\n\n";

        // 4. 开启签署流程
        echo "4. 开启签署流程...\n";
        $client->signFlow()->startFlow($signFlowId);
        echo "✓ 签署流程已开启\n\n";

        // 5. 获取签署链接
        echo "5. 获取签署链接...\n";
        $signerId1 = $flowResult['data']['signers'][0]['signerId'];
        $signUrl1  = $client->signFlow()->getSignUrl(
            signFlowId: $signFlowId,
            signerId: $signerId1,
            urlType: 1,
            redirectUrl: 'https://your-domain.com/callback'
        );
        echo "签署方1签署链接: {$signUrl1['data']['shortUrl']}\n\n";

        // 6. 查询流程详情
        echo "6. 查询流程详情...\n";
        $flowDetail = $client->signFlow()->getFlowDetail($signFlowId);
        echo "流程状态: {$flowDetail['data']['signFlowStatus']}\n";
        echo "流程标题: {$flowDetail['data']['signFlowTitle']}\n\n";
    }

    // ===== 方式2：通过模板创建文件并签署 =====
    echo "\n方式2：通过模板创建文件并签署\n\n";

    echo "1. 通过模板创建文件...\n";
    $templateId = 'your_template_id'; // 替换为实际的模板ID

    $fileResult2 = $client->file()->createFileByTemplate(
        templateId: $templateId,
        name: '模板合同-' . date('YmdHis'),
        simpleFormFields: [
            '甲方' => '甲方公司名称',
            '乙方' => '乙方公司名称',
            '金额' => '100000',
            '日期' => date('Y-m-d'),
        ]
    );

    echo "✓ 模板文件创建成功\n";
    echo "fileId: {$fileResult2['data']['fileId']}\n";
    echo "下载链接: {$fileResult2['data']['downloadUrl']}\n\n";

    echo "========== 所有签署流程测试完成 ==========\n";

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