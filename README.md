# 易签宝 PHP SDK

易签宝（e签宝）PHP SDK，基于官方 API V3 文档开发，支持实名认证和PDF合同签署功能。

## 特性

- ✅ PHP 8.2+ 支持
- ✅ 完全基于易签宝官方 API V3 文档
- ✅ 使用请求头格式的签名鉴权方式（官方推荐）
- ✅ HTTP请求自动重试机制
- ✅ 领域服务设计模式
- ✅ 中间件架构（Guzzle Middleware）
- ✅ 灵活的配置管理
- ✅ PSR-4 自动加载

## 安装

使用 Composer 安装：

```bash
composer require your-vendor/esignbao-sdk
```

## 快速开始

### 初始化客户端

```php
<?php

use ESignBao\Client;

$client = new Client([
    'app_id' => 'your_app_id',
    'app_secret' => 'your_app_secret',
    'sandbox' => false,                 // 是否使用沙箱环境
    'timeout' => 30,                    // 请求超时时间（秒）
    'connect_timeout' => 2.0,           // 连接超时时间（秒）
    'max_retries' => 3,                 // 最大重试次数
    'retry_delay_ms' => 200,            // 重试基础延迟（毫秒）
    'enable_log' => true,               // 是否开启HTTP请求/响应日志
    'log_path' => '/path/to/log.log',   // 日志文件路径
]);
```

## API 文档

### 1. 账号管理

#### 创建个人账号

```php
$result = $client->auth()->createPersonAccount(
    thirdPartyUserId: 'user_12345',
    psnAccount: '13800138000',
    name: '张三',
    idNumber: '110101199001011234'
);

$psnId = $result['data']['psnId'];
```

#### 查询个人认证信息

```php
// 通过psnId查询
$info = $client->auth()->getPersonIdentityInfo(psnId: $psnId);

// 通过手机号查询
$info = $client->auth()->getPersonIdentityInfo(psnAccount: '13800138000');

// 通过身份证号查询
$info = $client->auth()->getPersonIdentityInfo(
    psnIDCardNum: '110101199001011234',
    psnIDCardType: 'CRED_PSN_CH_IDCARD'
);
```

#### 创建企业账号

```php
$result = $client->auth()->createOrganizationAccount(
    thirdPartyUserId: 'org_12345',
    orgName: '测试科技有限公司',
    orgIDCardNum: '91110000000000000X'
);

$orgId = $result['data']['orgId'];
```

#### 查询企业认证信息

```php
$info = $client->auth()->getOrganizationIdentityInfo(orgId: $orgId);
```

### 2. 文件管理

#### 上传文件

```php
// 通过文件路径上传
$result = $client->file()->uploadFileByPath('/path/to/contract.pdf');
$fileId = $result['data']['fileId'];
```

#### 通过模板创建文件

```php
$result = $client->file()->createFileByTemplate(
    templateId: 'your_template_id',
    name: '合同文件',
    simpleFormFields: [
        '甲方' => '甲方公司',
        '乙方' => '乙方公司',
        '金额' => '100000',
    ]
);

$fileId = $result['data']['fileId'];
$downloadUrl = $result['data']['downloadUrl'];
```

### 3. 签署流程

#### 创建签署流程

```php
use ESignBao\Services\SignFlowService;

// 构建签署方
$signers = [
    SignFlowService::buildPersonSigner(
        psnId: 'person_id_1',
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
];

// 创建签署流程
$result = $client->signFlow()->createByFile(
    docs: [
        ['fileId' => $fileId, 'fileName' => '合同.pdf'],
    ],
    signFlowTitle: '合同签署',
    signFlowConfig: [
        'autoFinish' => true,
        'notifyUrl' => 'https://your-domain.com/callback',
    ],
    signers: $signers
);

$signFlowId = $result['data']['signFlowId'];
```

#### 开启签署流程

```php
$client->signFlow()->startFlow($signFlowId);
```

#### 获取签署链接

```php
$result = $client->signFlow()->getSignUrl(
    signFlowId: $signFlowId,
    signerId: $signerId,
    urlType: 1,
    redirectUrl: 'https://your-domain.com/callback'
);

$signUrl = $result['data']['shortUrl'];
```

#### 查询流程详情

```php
$detail = $client->signFlow()->getFlowDetail($signFlowId);
```

#### 撤销流程

```php
$client->signFlow()->revokeFlow(
    signFlowId: $signFlowId,
    operatorId: $operatorId,
    revokeReason: '撤销原因'
);
```

#### 下载已签署文件

```php
$result = $client->signFlow()->getSignedFiles($signFlowId);
```

## API 接口对照表

### 认证服务 (AuthService)

| SDK方法 | 官方接口 | 说明 |
|---------|---------|------|
| `createPersonAccount()` | `POST /v3/persons` | 创建个人账号 |
| `getPersonIdentityInfo()` | `GET /v3/persons/identity-info` | 查询个人认证信息 |
| `createOrganizationAccount()` | `POST /v3/organizations` | 创建企业账号 |
| `getOrganizationIdentityInfo()` | `GET /v3/organizations/identity-info` | 查询企业认证信息 |

### 文件服务 (FileService)

| SDK方法 | 官方接口 | 说明 |
|---------|---------|------|
| `getUploadUrl()` | `POST /v3/files/file-upload-url` | 获取文件上传地址 |
| `uploadFileByPath()` | - | 简化的文件上传方法 |
| `createFileByTemplate()` | `POST /v3/files/createByTemplate` | 通过模板创建文件 |
| `getFileInfo()` | `GET /v3/files/{fileId}` | 查询文件详情 |
| `downloadFile()` | `GET /v3/files/{fileId}/download-url` | 下载文件 |

### 签署流程服务 (SignFlowService)

| SDK方法 | 官方接口 | 说明 |
|---------|---------|------|
| `createByFile()` | `POST /v3/sign-flow/create-by-file` | 基于文件创建签署流程 |
| `createBySignTemplate()` | `POST /v3/sign-flow/create-by-sign-template` | 基于签署模板创建流程 |
| `addSigners()` | `POST /v3/sign-flow/{signFlowId}/signers` | 添加签署方 |
| `startFlow()` | `PUT /v3/sign-flow/{signFlowId}/start` | 开启签署流程 |
| `getSignUrl()` | `POST /v3/sign-flow/{signFlowId}/signers/{signerId}/sign-url` | 获取签署地址 |
| `getFlowDetail()` | `GET /v3/sign-flow/{signFlowId}` | 查询流程详情 |
| `revokeFlow()` | `PUT /v3/sign-flow/{signFlowId}/revoke` | 撤销流程 |
| `getSignedFiles()` | `GET /v3/sign-flow/{signFlowId}/signed-files` | 下载已签署文件 |

## 错误处理

```php
use ESignBao\Exceptions\ESignBaoException;

try {
    $result = $client->auth()->createPersonAccount(...);
} catch (ESignBaoException $e) {
    echo "错误: " . $e->getMessage() . "\n";
    echo "错误码: " . $e->getCode() . "\n";
    
    if ($response = $e->getResponse()) {
        print_r($response);
    }
}
```

## 示例代码

- `examples/auth_example.php` - 账号管理和认证示例
- `examples/sign_flow_example.php` - 签署流程示例

运行示例：

```bash
php examples/auth_example.php
php examples/sign_flow_example.php
```

## 环境要求

- PHP >= 8.2
- ext-json
- guzzlehttp/guzzle ^7.8
- psr/simple-cache ^3.0

## 官方文档

- 易签宝开放平台：https://open.esign.cn/
- API V3 文档：https://open.esign.cn/doc/opendoc

## 许可证

MIT License
