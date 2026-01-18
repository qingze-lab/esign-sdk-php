# 易签宝 PHP SDK

易签宝（e签宝）开放平台 V3 API 的非官方 PHP SDK。

[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

> **注意**: 本 SDK 基于易签宝 V3 版本 API 开发，支持 PHP 8.2+。

## ✨ 特性

- **完整覆盖**: 支持实名认证、文件管理、合同模板、签署流程等核心业务。
- **强类型**: 充分利用 PHP 8.2 类型系统，代码更加健壮。
- **易于使用**: 采用领域服务模式设计，调用逻辑清晰直观。
- **自动签名**: 内置 API 请求签名逻辑，开发者无需关心鉴权细节。
- **重试机制**: 内置 HTTP 请求自动重试机制，提高网络波动下的稳定性。

## 📦 安装

使用 Composer 安装：

```bash
composer require qingze-lab/esignbao-sdk-php
```

## 🚀 快速开始

### 1. 初始化客户端

```php
use QingzeLab\ESignBao\Client;
use QingzeLab\ESignBao\Config\Configuration;

$config = new Configuration(
    appId: 'your_app_id',
    appSecret: 'your_app_secret',
    apiBaseUrl: 'https://smlopenapi.esign.cn' // 沙箱环境
);

$client = new Client($config);
```

### 2. 发起一份签署流程

```php
// 1. 上传文件
$uploadResult = $client->file()->uploadFileByPath('./contract.pdf');
$fileId = $uploadResult['data']['fileId'];

// 2. 创建签署流程
$flowResult = $client->signFlow()->createByFile(
    docs: [
        ['fileId' => $fileId, 'fileName' => '合同.pdf']
    ],
    signFlowConfig: [
        'signFlowTitle' => '测试合同签署',
        'autoStart' => true,
        'autoFinish' => true
    ],
    signers: [
        [
            'signerType' => 0, // 个人签署
            'psnSignerInfo' => ['psnAccount' => '188****8888'],
            'signFields' => [
                ['fileId' => $fileId, 'posPage' => '1', 'posX' => 100, 'posY' => 100]
            ]
        ]
    ]
);
$signFlowId = $flowResult['data']['signFlowId'];

// 3. 获取签署链接
$signUrl = $client->signFlow()->getSignUrl(
    signFlowId: $signFlowId,
    operator: ['psnAccount' => '188****8888']
);

echo "签署链接: " . $signUrl['data']['shortUrl'];
```

## 📖 文档

详细的接口说明和使用文档请查阅 [Wiki](.wiki.md)。

- [认证服务 (AuthService)](.wiki.md#认证服务-authservice)
- [文件服务 (FileService)](.wiki.md#文件服务-fileservice)
- [模板服务 (TemplateService)](.wiki.md#模板服务-templateservice)
- [签署流程服务 (SignFlowService)](.wiki.md#签署流程服务-signflowservice)

## 🤝 贡献

欢迎提交 Issue 或 Pull Request 来改进本项目。

## 📄 许可证

本项目基于 MIT 许可证开源。详情请参阅 [LICENSE](LICENSE) 文件。
