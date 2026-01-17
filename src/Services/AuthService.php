<?php

declare(strict_types = 1);

namespace QingzeLab\ESignBao\Services;

use QingzeLab\ESignBao\Exceptions\ESignBaoException;
use QingzeLab\ESignBao\Http\HttpClient;

/**
 * 实名认证领域服务
 * 基于易签宝官方文档 V3 API
 */
class AuthService
{
    /**
     * @var HttpClient
     */
    private HttpClient $httpClient;

    /**
     * @param HttpClient $httpClient
     */
    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * 获取个人认证&授权页面链接
     * 接口文档: https://open.esign.cn/doc/opendoc/auth3/rx8igf
     *
     * @param array       $psnAuthConfig   个人实名认证配置项
     * @param array|null  $authorizeConfig 个人授权配置项（可选）
     * @param array|null  $redirectConfig  重定向配置项（可选）
     * @param string|null $notifyUrl       异步通知地址（可选）
     * @param string|null $clientType      客户端类型（可选，默认ALL）
     * @return array 包含authUrl和authFlowId
     * @throws ESignBaoException
     */
    public function getPersonAuthUrl(
        array   $psnAuthConfig,
        ?array  $authorizeConfig = null,
        ?array  $redirectConfig = null,
        ?string $notifyUrl = null,
        ?string $clientType = null
    ): array
    {
        $data = ['psnAuthConfig' => $psnAuthConfig];

        if ($authorizeConfig !== null) {
            $data['authorizeConfig'] = $authorizeConfig;
        }
        if ($redirectConfig !== null) {
            $data['redirectConfig'] = $redirectConfig;
        }
        if ($notifyUrl !== null) {
            $data['notifyUrl'] = $notifyUrl;
        }
        if ($clientType !== null) {
            $data['clientType'] = $clientType;
        }

        return $this->httpClient->post('/v3/psn-auth-url', $data);
    }

    /**
     * 获取机构认证&授权页面链接
     * 接口文档: https://open.esign.cn/doc/opendoc/auth3/kcbdu7
     *
     * @param array       $orgAuthConfig   组织机构认证配置项
     * @param array|null  $authorizeConfig 机构授权配置项（可选）
     * @param string|null $notifyUrl       异步通知地址（可选）
     * @param array|null  $redirectConfig  重定向配置项（可选）
     * @param string|null $clientType      客户端类型（可选，默认ALL）
     * @param string|null $appScheme       App Scheme（可选，用于端外唤起APP）
     * @return array 包含authUrl和authFlowId
     * @throws ESignBaoException
     */
    public function getOrganizationAuthUrl(
        array   $orgAuthConfig,
        ?array  $authorizeConfig = null,
        ?string $notifyUrl = null,
        ?array  $redirectConfig = null,
        ?string $clientType = null,
        ?string $appScheme = null
    ): array
    {
        $data = ['orgAuthConfig' => $orgAuthConfig];
        if ($authorizeConfig !== null) {
            $data['authorizeConfig'] = $authorizeConfig;
        }
        if ($notifyUrl !== null) {
            $data['notifyUrl'] = $notifyUrl;
        }
        if ($redirectConfig !== null) {
            $data['redirectConfig'] = $redirectConfig;
        }
        if ($clientType !== null) {
            $data['clientType'] = $clientType;
        }
        if ($appScheme !== null) {
            $data['appScheme'] = $appScheme;
        }

        return $this->httpClient->post('/v3/org-auth-url', $data);
    }

    /**
     * 查询认证授权流程详情
     * 接口文档: https://open.esign.cn/doc/opendoc/auth3/hlrs7s
     *
     * @param string $authFlowId 认证授权流程ID
     * @return array 流程详情
     * @throws ESignBaoException
     */
    public function getAuthFlowDetail(string $authFlowId): array
    {
        return $this->httpClient->get("/v3/auth-flow/{$authFlowId}");
    }

    /**
     * 查询个人认证信息
     * 接口文档: https://open.esign.cn/doc/opendoc/auth3/rx8igf
     *
     * @param string|null $psnId         个人账号ID
     * @param string|null $psnAccount    个人账号标识（手机号或邮箱）
     * @param string|null $psnIDCardNum  个人证件号
     * @param string|null $psnIDCardType 证件类型，默认CRED_PSN_CH_IDCARD
     * @return array 认证信息
     * @throws ESignBaoException
     */
    public function getPersonIdentityInfo(
        ?string $psnId = null,
        ?string $psnAccount = null,
        ?string $psnIDCardNum = null,
        ?string $psnIDCardType = 'CRED_PSN_CH_IDCARD'
    ): array
    {
        $params = [];

        if ($psnId !== null) {
            $params['psnId'] = $psnId;
        }
        if ($psnAccount !== null) {
            $params['psnAccount'] = $psnAccount;
        }
        if ($psnIDCardNum !== null) {
            $params['psnIDCardNum']  = $psnIDCardNum;
            $params['psnIDCardType'] = $psnIDCardType;
        }

        if (empty($params)) {
            throw new ESignBaoException('必须提供 psnId、psnAccount 或 psnIDCardNum 中的至少一个参数');
        }

        return $this->httpClient->get('/v3/persons/identity-info', $params);
    }

    /**
     * 查询组织机构认证信息
     *
     * @param string|null $orgId        机构账号ID
     * @param string|null $orgIDCardNum 统一社会信用代码
     * @return array 认证信息
     * @throws ESignBaoException
     */
    public function getOrganizationIdentityInfo(
        ?string $orgId = null,
        ?string $orgIDCardNum = null
    ): array
    {
        $params = [];

        if ($orgId !== null) {
            $params['orgId'] = $orgId;
        }
        if ($orgIDCardNum !== null) {
            $params['orgIDCardNum'] = $orgIDCardNum;
        }

        if (empty($params)) {
            throw new ESignBaoException('必须提供 orgId 或 orgIDCardNum 中的至少一个参数');
        }

        return $this->httpClient->get('/v3/organizations/identity-info', $params);
    }
}
