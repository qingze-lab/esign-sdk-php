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
     * 接口文档: https://open.esign.cn/doc/opendoc/pdf-sign3/aoq509
     * 接口路径: POST /v3/sign-flow/create-by-file
     *
     * @param array      $docs           文档信息列表，格式：[['fileId' => 'xxx', 'fileName' => 'xxx']]
     * @param string     $signFlowTitle  签署流程标题
     * @param array|null $signFlowConfig 签署流程配置（可选）
     * @param array|null $signers        签署方列表（可选）
     * @return array 包含signFlowId的流程信息
     * @throws ESignBaoException
     */
    public function createByFile(
        array  $docs,
        string $signFlowTitle,
        ?array $signFlowConfig = null,
        ?array $signers = null
    ): array
    {
        $data = [
            'docs'          => $docs,
            'signFlowTitle' => $signFlowTitle,
        ];

        if ($signFlowConfig !== null) {
            $data['signFlowConfig'] = $signFlowConfig;
        }
        if ($signers !== null) {
            $data['signers'] = $signers;
        }

        return $this->httpClient->post('/v3/sign-flow/create-by-file', $data);
    }

    /**
     * 基于签署模板创建签署流程
     * 接口路径: POST /v3/sign-flow/create-by-sign-template
     *
     * @param string     $signTemplateId 签署模板ID
     * @param string     $signFlowTitle  签署流程标题
     * @param array|null $docs           文档信息列表（可选）
     * @param array|null $signFlowConfig 签署流程配置（可选）
     * @param array|null $fillValues     填充值（可选）
     * @return array 包含signFlowId的流程信息
     * @throws ESignBaoException
     */
    public function createBySignTemplate(
        string $signTemplateId,
        string $signFlowTitle,
        ?array $docs = null,
        ?array $signFlowConfig = null,
        ?array $fillValues = null
    ): array
    {
        $data = [
            'signTemplateId' => $signTemplateId,
            'signFlowTitle'  => $signFlowTitle,
        ];

        if ($docs !== null) {
            $data['docs'] = $docs;
        }
        if ($signFlowConfig !== null) {
            $data['signFlowConfig'] = $signFlowConfig;
        }
        if ($fillValues !== null) {
            $data['fillValues'] = $fillValues;
        }

        return $this->httpClient->post('/v3/sign-flow/create-by-sign-template', $data);
    }

    /**
     * 添加签署方
     * 接口文档: https://open.esign.cn/doc/opendoc/pdf-sign3/su5g42
     *
     * @param string $signFlowId 签署流程ID
     * @param array  $signers    签署方列表
     * @return array 添加结果
     * @throws ESignBaoException
     */
    public function addSigners(string $signFlowId, array $signers): array
    {
        $data = ['signers' => $signers];
        return $this->httpClient->post("/v3/sign-flow/{$signFlowId}/signers", $data);
    }

    /**
     * 开启签署流程
     * 接口文档: https://open.esign.cn/doc/opendoc/pdf-sign3/pvfkwd
     *
     * @param string $signFlowId 签署流程ID
     * @return array 开启结果
     * @throws ESignBaoException
     */
    public function startFlow(string $signFlowId): array
    {
        return $this->httpClient->put("/v3/sign-flow/{$signFlowId}/start", []);
    }

    /**
     * 获取签署地址
     *
     * @param string      $signFlowId  签署流程ID
     * @param string      $signerId    签署方ID
     * @param int         $urlType     链接类型：1-短链接，2-长链接
     * @param string|null $appScheme   APP唤起协议（可选）
     * @param string|null $redirectUrl 签署完成后跳转地址（可选）
     * @return array 包含签署链接
     * @throws ESignBaoException
     */
    public function getSignUrl(
        string  $signFlowId,
        string  $signerId,
        int     $urlType = 1,
        ?string $appScheme = null,
        ?string $redirectUrl = null
    ): array
    {
        $data = ['urlType' => $urlType];

        if ($appScheme !== null) {
            $data['appScheme'] = $appScheme;
        }
        if ($redirectUrl !== null) {
            $data['redirectUrl'] = $redirectUrl;
        }

        return $this->httpClient->post("/v3/sign-flow/{$signFlowId}/signers/{$signerId}/sign-url", $data);
    }

    /**
     * 查询签署流程详情
     *
     * @param string $signFlowId 签署流程ID
     * @return array 流程详情
     * @throws ESignBaoException
     */
    public function getFlowDetail(string $signFlowId): array
    {
        return $this->httpClient->get("/v3/sign-flow/{$signFlowId}");
    }

    /**
     * 撤销签署流程
     *
     * @param string      $signFlowId   签署流程ID
     * @param string      $operatorId   操作人账号ID
     * @param string|null $revokeReason 撤销原因（可选）
     * @return array 撤销结果
     * @throws ESignBaoException
     */
    public function revokeFlow(
        string  $signFlowId,
        string  $operatorId,
        ?string $revokeReason = null
    ): array
    {
        $data = ['operatorId' => $operatorId];

        if ($revokeReason !== null) {
            $data['revokeReason'] = $revokeReason;
        }

        return $this->httpClient->put("/v3/sign-flow/{$signFlowId}/revoke", $data);
    }

    /**
     * 下载签署后的文件
     *
     * @param string $signFlowId 签署流程ID
     * @return array 包含下载链接
     * @throws ESignBaoException
     */
    public function getSignedFiles(string $signFlowId): array
    {
        return $this->httpClient->get("/v3/sign-flow/{$signFlowId}/signed-files");
    }

    /**
     * 构建签署方数据结构（个人）
     *
     * @param string      $psnId      个人账号ID
     * @param string|null $psnAccount 个人账号标识（可选）
     * @param int         $signOrder  签署顺序
     * @param array|null  $signFields 签署区域（可选）
     * @return array 签署方数据
     */
    public static function buildPersonSigner(
        string  $psnId,
        ?string $psnAccount = null,
        int     $signOrder = 1,
        ?array  $signFields = null
    ): array
    {
        $signer = [
            'signerType' => 0, // 0-个人
            'psnId'      => $psnId,
            'signOrder'  => $signOrder,
        ];

        if ($psnAccount !== null) {
            $signer['psnAccount'] = $psnAccount;
        }
        if ($signFields !== null) {
            $signer['signFields'] = $signFields;
        }

        return $signer;
    }

    /**
     * 构建签署方数据结构（企业）
     *
     * @param string     $orgId      企业账号ID
     * @param string     $psnId      经办人账号ID
     * @param int        $signOrder  签署顺序
     * @param array|null $signFields 签署区域（可选）
     * @return array 签署方数据
     */
    public static function buildOrganizationSigner(
        string $orgId,
        string $psnId,
        int    $signOrder = 1,
        ?array $signFields = null
    ): array
    {
        $signer = [
            'signerType' => 1, // 1-企业
            'orgId'      => $orgId,
            'psnId'      => $psnId,
            'signOrder'  => $signOrder,
        ];

        if ($signFields !== null) {
            $signer['signFields'] = $signFields;
        }

        return $signer;
    }

    /**
     * 构建签署区域数据结构
     *
     * @param int        $fileId      文件序号（从0开始）
     * @param bool       $autoExecute 是否自动签署
     * @param int|null   $posPage     页码（从1开始）
     * @param float|null $posX        X坐标（0-1之间）
     * @param float|null $posY        Y坐标（0-1之间）
     * @param float|null $width       宽度（0-1之间）
     * @param float|null $height      高度（0-1之间）
     * @return array 签署区域数据
     */
    public static function buildSignField(
        int    $fileId = 0,
        bool   $autoExecute = false,
        ?int   $posPage = null,
        ?float $posX = null,
        ?float $posY = null,
        ?float $width = null,
        ?float $height = null
    ): array
    {
        $signField = [
            'fileId'      => $fileId,
            'autoExecute' => $autoExecute,
        ];

        if ($posPage !== null) {
            $signField['posPage'] = $posPage;
        }
        if ($posX !== null) {
            $signField['posX'] = $posX;
        }
        if ($posY !== null) {
            $signField['posY'] = $posY;
        }
        if ($width !== null) {
            $signField['width'] = $width;
        }
        if ($height !== null) {
            $signField['height'] = $height;
        }

        return $signField;
    }
}