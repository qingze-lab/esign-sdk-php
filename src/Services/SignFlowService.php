<?php

declare(strict_types = 1);

namespace QingzeLab\ESignBao\Services;

use QingzeLab\ESignBao\Exceptions\ESignBaoException;
use QingzeLab\ESignBao\Http\HttpClient;

/**
 * 签署流程领域服务
 * 基于易签宝官方文档 V3 API
 */
class SignFlowService
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
     * 基于文件创建签署流程
     * 接口文档: https://open.esign.cn/doc/opendoc/pdf-sign3/su5g42
     * 接口路径: POST /v3/sign-flow/create-by-file
     *
     * @param array|null $docs              待签署文件信息
     * @param array|null $attachments       附件信息（可选）
     * @param array|null $signFlowConfig    签署流程配置项
     * @param array|null $signFlowInitiator 签署发起方信息（可选）
     * @param array|null $signers           签署方信息（可选）
     * @param array|null $copiers           抄送方信息（可选）
     * @return array 包含signFlowId的流程信息
     * @throws ESignBaoException
     */
    public function createByFile(
        ?array $docs = null,
        ?array $attachments = null,
        ?array $signFlowConfig = null,
        ?array $signFlowInitiator = null,
        ?array $signers = null,
        ?array $copiers = null
    ): array
    {
        $data = [];

        if ($docs !== null) {
            $data['docs'] = $docs;
        }

        if ($attachments !== null) {
            $data['attachments'] = $attachments;
        }

        if ($signFlowConfig !== null) {
            $data['signFlowConfig'] = $signFlowConfig;
        }

        if ($signFlowInitiator !== null) {
            $data['signFlowInitiator'] = $signFlowInitiator;
        }

        if ($signers !== null) {
            $data['signers'] = $signers;
        }

        if ($copiers !== null) {
            $data['copiers'] = $copiers;
        }

        if (empty($data)) {
            throw new ESignBaoException('创建签署流程参数不能为空');
        }

        return $this->httpClient->post('/v3/sign-flow/create-by-file', $data);
    }

    /**
     * 获取签署页面链接
     * 接口文档: https://open.esign.cn/doc/opendoc/pdf-sign3/pvfkwd
     * 接口路径: POST /v3/sign-flow/{signFlowId}/sign-url
     *
     * @param string      $signFlowId     签署流程ID
     * @param array|null  $operator       操作人信息（个人传psnAccount/psnId，机构传经办人信息）
     * @param array|null  $organization   机构信息（可选，机构签署时传）
     * @param bool        $needLogin      是否需要登录（默认false）
     * @param int         $urlType        链接类型：1-预览链接，2-签署链接（默认2）
     * @param string      $clientType     客户端类型：ALL/H5/PC（默认ALL）
     * @param array|null  $redirectConfig 重定向配置（可选）
     * @param string|null $appScheme      APP唤起协议（可选）
     * @return array 包含shortUrl和url的链接信息
     * @throws ESignBaoException
     */
    public function getSignUrl(
        string  $signFlowId,
        ?array  $operator = null,
        ?array  $organization = null,
        bool    $needLogin = false,
        int     $urlType = 2,
        string  $clientType = 'ALL',
        ?array  $redirectConfig = null,
        ?string $appScheme = null
    ): array
    {
        $data = [
            'needLogin'  => $needLogin,
            'urlType'    => $urlType,
            'clientType' => $clientType,
        ];

        if ($operator !== null) {
            $data['operator'] = $operator;
        }

        if ($organization !== null) {
            $data['organization'] = $organization;
        }

        if ($redirectConfig !== null) {
            $data['redirectConfig'] = $redirectConfig;
        }

        if ($appScheme !== null) {
            $data['appScheme'] = $appScheme;
        }

        return $this->httpClient->post("/v3/sign-flow/{$signFlowId}/sign-url", $data);
    }

    /**
     * 撤销签署流程
     * 接口文档: https://open.esign.cn/doc/opendoc/pdf-sign3/klbicu
     * 接口路径: POST /v3/sign-flow/{signFlowId}/revoke
     *
     * @param string      $signFlowId   签署流程ID
     * @param string|null $revokeReason 撤销原因（可选，最多50字）
     * @return array 空对象
     * @throws ESignBaoException
     */
    public function revoke(string $signFlowId, ?string $revokeReason = null): array
    {
        $data = [];

        if ($revokeReason !== null) {
            $data['revokeReason'] = $revokeReason;
        }

        return $this->httpClient->post("/v3/sign-flow/{$signFlowId}/revoke", $data);
    }

    /**
     * 查询签署流程详情
     * 接口文档: https://open.esign.cn/doc/opendoc/pdf-sign3/xxk4q6
     * 接口路径: GET /v3/sign-flow/{signFlowId}/detail
     *
     * @param string $signFlowId 签署流程ID
     * @return array 包含流程状态、文件信息、签署方信息等详情
     * @throws ESignBaoException
     */
    public function getSignFlowDetail(string $signFlowId): array
    {
        return $this->httpClient->get("/v3/sign-flow/{$signFlowId}/detail");
    }

    /**
     * 下载已签署文件及附属材料
     * 接口文档: https://open.esign.cn/doc/opendoc/pdf-sign3/kczf8g
     * 接口路径: POST /v3/sign-flow/{signFlowId}/file-download-url
     *
     * @param string      $signFlowId       签署流程ID
     * @param int|null    $urlAvailableDate 下载链接有效期，单位：秒。默认：3600
     * @param bool|null   $internalUrl      是否是内网地址，默认：false
     * @param string|null $rsaSecret        文件需要加密时使用的RSA公钥（base64编码）
     * @param string|null $rsaSecretKey     RSA公钥版本
     * @return array 包含files和attachments的下载链接信息
     * @throws ESignBaoException
     */
    public function getFileDownloadUrl(
        string  $signFlowId,
        ?int    $urlAvailableDate = null,
        ?bool   $internalUrl = null,
        ?string $rsaSecret = null,
        ?string $rsaSecretKey = null
    ): array
    {
        $data = [
            'signFlowId' => $signFlowId,
        ];

        if ($urlAvailableDate !== null) {
            $data['urlAvailableDate'] = $urlAvailableDate;
        }

        if ($internalUrl !== null) {
            $data['internalUrl'] = $internalUrl;
        }

        if ($rsaSecret !== null) {
            $data['rsaSecret'] = $rsaSecret;
        }

        if ($rsaSecretKey !== null) {
            $data['rsaSecretKey'] = $rsaSecretKey;
        }

        return $this->httpClient->post("/v3/sign-flow/{$signFlowId}/file-download-url", $data);
    }

    /**
     * 下载签署中文件
     * 接口文档: https://open.esign.cn/doc/opendoc/pdf-sign3/gkgc4729sa67upfn
     * 接口路径: GET /v3/sign-flow/{signFlowId}/preview-file-download-url
     *
     * @param string   $signFlowId       签署流程ID
     * @param string   $docFileId        签署流程中的文件ID
     * @param int|null $urlAvailableDate 下载链接有效期，单位：秒。默认：3600
     * @return array 包含文件下载链接信息
     * @throws ESignBaoException
     */
    public function getPreviewFileDownloadUrl(
        string $signFlowId,
        string $docFileId,
        ?int   $urlAvailableDate = null
    ): array
    {
        $data = [
            'docFileId' => $docFileId,
        ];

        if ($urlAvailableDate !== null) {
            $data['urlAvailableDate'] = $urlAvailableDate;
        }

        return $this->httpClient->get("/v3/sign-flow/{$signFlowId}/preview-file-download-url", $data);
    }

}