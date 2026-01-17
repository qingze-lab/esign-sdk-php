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
     * @param array $psnAuthConfig 个人实名认证配置项
     * @param array $authorizeConfig 个人授权配置项（可选）
     * @return array 包含authUrl和authFlowId
     * @throws ESignBaoException
     */
    public function getPersonAuthUrl(array $psnAuthConfig, ?array $authorizeConfig = null): array
    {
        $data = ['psnAuthConfig' => $psnAuthConfig];
        
        if ($authorizeConfig !== null) {
            $data['authorizeConfig'] = $authorizeConfig;
        }

        return $this->httpClient->post('/v3/psn-auth-url', $data);
    }

    /**
     * 获取机构认证&授权页面链接
     * 接口文档: https://open.esign.cn/doc/opendoc/auth3/kcbdu7
     *
     * @param array $orgAuthConfig 组织机构认证配置项
     * @param array $transactorInfo 经办人身份信息（可选）
     * @param array $authorizeConfig 机构授权配置项（可选）
     * @return array 包含authUrl和authFlowId
     * @throws ESignBaoException
     */
    public function getOrganizationAuthUrl(
        array $orgAuthConfig,
        ?array $transactorInfo = null,
        ?array $authorizeConfig = null
    ): array
    {
        $data = ['orgAuthConfig' => $orgAuthConfig];

        if ($transactorInfo !== null) {
            $data['transactorInfo'] = $transactorInfo;
        }
        if ($authorizeConfig !== null) {
            $data['authorizeConfig'] = $authorizeConfig;
        }

        return $this->httpClient->post('/v3/org-auth-url', $data);
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
     * 创建个人账号
     * 接口文档: https://open.esign.cn/doc/opendoc/auth3/kcbdu7
     *
     * @param string      $thirdPartyUserId 第三方平台的用户唯一标识
     * @param string|null $psnAccount       个人账号标识（手机号或邮箱）
     * @param string|null $name             姓名
     * @param string|null $idNumber         证件号
     * @param string|null $idType           证件类型，默认CRED_PSN_CH_IDCARD
     * @return array 包含psnId的账号信息
     * @throws ESignBaoException
     */
    public function createPersonAccount(
        string  $thirdPartyUserId,
        ?string $psnAccount = null,
        ?string $name = null,
        ?string $idNumber = null,
        ?string $idType = 'CRED_PSN_CH_IDCARD'
    ): array
    {
        $data = [
            'thirdPartyUserId' => $thirdPartyUserId,
        ];

        if ($psnAccount !== null) {
            $data['psnAccount'] = $psnAccount;
        }
        if ($name !== null) {
            $data['name'] = $name;
        }
        if ($idNumber !== null) {
            $data['idNumber'] = $idNumber;
            $data['idType']   = $idType;
        }

        return $this->httpClient->post('/v3/persons', $data);
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

    /**
     * 创建机构账号
     * 接口文档: https://open.esign.cn/doc/opendoc/auth3/hlrs7s
     *
     * @param string      $thirdPartyUserId 第三方平台的机构唯一标识
     * @param string|null $orgName          机构名称
     * @param string|null $orgIDCardNum     统一社会信用代码
     * @param string|null $orgIDCardType    证件类型，默认CRED_ORG_USCC
     * @return array 包含orgId的账号信息
     * @throws ESignBaoException
     */
    public function createOrganizationAccount(
        string  $thirdPartyUserId,
        ?string $orgName = null,
        ?string $orgIDCardNum = null,
        ?string $orgIDCardType = 'CRED_ORG_USCC'
    ): array
    {
        $data = [
            'thirdPartyUserId' => $thirdPartyUserId,
        ];

        if ($orgName !== null) {
            $data['orgName'] = $orgName;
        }
        if ($orgIDCardNum !== null) {
            $data['orgIDCardNum']  = $orgIDCardNum;
            $data['orgIDCardType'] = $orgIDCardType;
        }

        return $this->httpClient->post('/v3/organizations', $data);
    }
}
