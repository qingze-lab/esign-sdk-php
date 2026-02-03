<?php

declare(strict_types = 1);

namespace QingzeLab\ESignBao\Services;

use QingzeLab\ESignBao\Exceptions\ESignBaoException;
use QingzeLab\ESignBao\Http\HttpClient;

/**
 * 合同模板服务
 * 基于易签宝官方文档 V3 API
 */
class TemplateService
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
     * 获取制作合同模板页面
     * 接口文档: https://open.esign.cn/doc/opendoc/pdf-sign3/xagpot
     * 接口路径: POST /v3/doc-templates/doc-template-create-url
     *
     * @param string      $docTemplateName        模板名称
     * @param string      $fileId                 底稿文件ID
     * @param int|null    $docTemplateType        模板类型：0-PDF模板(默认)，1-HTML模板
     * @param string|null $redirectUrl            制作完成重定向地址
     * @param bool|null   $hiddenOriginComponents 是否隐藏原始控件，默认false
     * @param array|null  $basicComponentsType    展示的基础控件类型列表
     * @param bool|null   $showReplaceFraft       是否展示替换底稿按钮，默认false
     * @param array|null  $customComponentGroups  自定义控件组ID列表
     * @param array|null  $customComponents       自定义控件ID列表
     * @param array|null  $signerRoles            签署方角色标识列表
     * @param string|null $dedicatedCloudId       专属云项目ID
     * @return array 包含docTemplateCreateUrl等信息的数组
     * @throws ESignBaoException
     */
    public function getDocTemplateCreateUrl(
        string  $docTemplateName,
        string  $fileId,
        ?int    $docTemplateType = 0,
        ?string $redirectUrl = null,
        ?bool   $hiddenOriginComponents = null,
        ?array  $basicComponentsType = null,
        ?bool   $showReplaceFraft = null,
        ?array  $customComponentGroups = null,
        ?array  $customComponents = null,
        ?array  $signerRoles = null,
        ?string $dedicatedCloudId = null
    ): array
    {
        $data = [
            'docTemplateName' => $docTemplateName,
            'fileId'          => $fileId,
        ];

        if ($docTemplateType !== null) {
            $data['docTemplateType'] = $docTemplateType;
        }

        if ($redirectUrl !== null) {
            $data['redirectUrl'] = $redirectUrl;
        }

        if ($hiddenOriginComponents !== null) {
            $data['hiddenOriginComponents'] = $hiddenOriginComponents;
        }

        if ($basicComponentsType !== null) {
            $data['basicComponentsType'] = $basicComponentsType;
        }

        if ($showReplaceFraft !== null) {
            $data['showReplaceFraft'] = $showReplaceFraft;
        }

        if ($customComponentGroups !== null) {
            $data['customComponentGroups'] = $customComponentGroups;
        }

        if ($customComponents !== null) {
            $data['customComponents'] = $customComponents;
        }

        if ($signerRoles !== null) {
            $data['signerRoles'] = $signerRoles;
        }

        if ($dedicatedCloudId !== null) {
            $data['dedicatedCloudId'] = $dedicatedCloudId;
        }

        return $this->httpClient->post('/v3/doc-templates/doc-template-create-url', $data);
    }

    /**
     * 获取编辑合同模板页面
     * 接口文档: https://open.esign.cn/doc/opendoc/pdf-sign3/lgb2go
     * 接口路径: POST /v3/doc-templates/{docTemplateId}/doc-template-edit-url
     *
     * @param string      $docTemplateId          模板ID
     * @param string|null $redirectUrl            编辑完成重定向地址
     * @param bool|null   $hiddenOriginComponents 是否隐藏原始控件，默认false
     * @param array|null  $basicComponentsType    展示的基础控件类型列表
     * @param bool|null   $showReplaceFraft       是否展示替换底稿按钮，默认false
     * @param array|null  $customComponentGroups  自定义控件组ID列表
     * @param array|null  $customComponents       自定义控件ID列表
     * @param array|null  $signerRoles            签署方角色标识列表
     * @return array 包含docTemplateEditUrl等信息的数组
     * @throws ESignBaoException
     */
    public function getDocTemplateEditUrl(
        string  $docTemplateId,
        ?string $redirectUrl = null,
        ?bool   $hiddenOriginComponents = null,
        ?array  $basicComponentsType = null,
        ?bool   $showReplaceFraft = null,
        ?array  $customComponentGroups = null,
        ?array  $customComponents = null,
        ?array  $signerRoles = null
    ): array
    {
        $data = [];

        if ($redirectUrl !== null) {
            $data['redirectUrl'] = $redirectUrl;
        }

        if ($hiddenOriginComponents !== null) {
            $data['hiddenOriginComponents'] = $hiddenOriginComponents;
        }

        if ($basicComponentsType !== null) {
            $data['basicComponentsType'] = $basicComponentsType;
        }

        if ($showReplaceFraft !== null) {
            $data['showReplaceFraft'] = $showReplaceFraft;
        }

        if ($customComponentGroups !== null) {
            $data['customComponentGroups'] = $customComponentGroups;
        }

        if ($customComponents !== null) {
            $data['customComponents'] = $customComponents;
        }

        if ($signerRoles !== null) {
            $data['signerRoles'] = $signerRoles;
        }

        return $this->httpClient->post("/v3/doc-templates/{$docTemplateId}/doc-template-edit-url", $data);
    }

    /**
     * 获取填写合同模板页面
     * 接口文档: https://open.esign.cn/doc/opendoc/pdf-sign3/ub4ncy
     * 接口路径: POST /v3/doc-templates/doc-template-fill-url
     *
     * @param string      $docTemplateId          模板ID
     * @param string|null $customBizNum           自定义业务编号（可选）
     * @param array|null  $componentFillingValues 模板控件预填内容列表（可选）
     * @param bool|null   $editFillingValue       是否可以修改预填内容（可选，默认true）
     * @param string|null $clientType             客户端类型：ALL, H5, PC（可选，默认ALL）
     * @param string|null $notifyUrl              回调通知地址（可选）
     * @param string|null $redirectUrl            跳转页面地址（可选）
     * @return array 包含docTemplateFillUrl等信息的数组
     * @throws ESignBaoException
     */
    public function getDocTemplateFillUrl(
        string  $docTemplateId,
        ?string $customBizNum = null,
        ?array  $componentFillingValues = null,
        ?bool   $editFillingValue = null,
        ?string $clientType = null,
        ?string $notifyUrl = null,
        ?string $redirectUrl = null
    ): array
    {
        $data = [
            'docTemplateId' => $docTemplateId,
        ];

        if ($customBizNum !== null) {
            $data['customBizNum'] = $customBizNum;
        }

        if ($componentFillingValues !== null) {
            $data['componentFillingtValues'] = $componentFillingValues;
        }

        if ($editFillingValue !== null) {
            $data['editFillingValue'] = $editFillingValue;
        }

        if ($clientType !== null) {
            $data['clientType'] = $clientType;
        }

        if ($notifyUrl !== null) {
            $data['notifyUrl'] = $notifyUrl;
        }

        if ($redirectUrl !== null) {
            $data['redirectUrl'] = $redirectUrl;
        }

        return $this->httpClient->post('/v3/doc-templates/doc-template-fill-url', $data);
    }

    /**
     * 查询合同模板中控件详情
     * 接口文档: https://open.esign.cn/doc/opendoc/pdf-sign3/aoq509
     * 接口路径: GET /v3/doc-templates/{docTemplateId}
     *
     * @param string $docTemplateId 合同模板ID
     * @return array 包含模板详情和控件列表
     * @throws ESignBaoException
     */
    public function getDocTemplateComponents(string $docTemplateId): array
    {
        return $this->httpClient->get("/v3/doc-templates/{$docTemplateId}");
    }

    /**
     * 查询填写合同模板任务结果
     * 接口文档: https://open.esign.cn/doc/opendoc/pdf-sign3/ovhittqcf7cooxxv
     * 接口路径: POST /v3/doc-templates/fill-task-result
     *
     * @param string $docTemplateId 文件模板ID
     * @param string $fillTaskId    填写任务ID
     * @return array 包含填写状态和内容的结果
     * @throws ESignBaoException
     */
    public function getFillTaskResult(string $docTemplateId, string $fillTaskId): array
    {
        $data = [
            'docTemplateId' => $docTemplateId,
            'fillTaskId'    => $fillTaskId,
        ];

        return $this->httpClient->post('/v3/doc-templates/fill-task-result', $data);
    }
}
