<?php
declare(strict_types = 1);

namespace QingzeLab\OpenIM\Service;

/**
 * Class GroupService
 *
 * 群组领域服务。
 */
final class GroupService extends AbstractService
{
    /**
     * 创建群组。
     *
     * @param string   $ownerUserId   群主用户 ID
     * @param string[] $memberUserIds 普通成员用户 ID 列表
     * @param array    $groupInfo     群信息列表
     * @param string[] $adminUserIds  管理员用户 ID 列表
     * @return array<string, mixed> OpenIM 返回的数据
     */
    public function createGroup(string $ownerUserId, array $memberUserIds, array $groupInfo, array $adminUserIds = []): array
    {
        $body = [
            'memberUserIDs' => $memberUserIds,
            'adminUserIDs'  => $adminUserIds,
            'ownerUserID'   => $ownerUserId,
            'groupInfo'     => $groupInfo,
        ];

        return $this->post('/group/create_group', $body);
    }

    /**
     * 获取指定群组的详细信息。
     *
     * @param array $groupIds 群ID列表
     * @return array<string, mixed> OpenIM 返回的数据
     */
    public function getGroupsInfo(array $groupIds): array
    {
        $body = [
            'groupIDs' => $groupIds,
        ];

        return $this->post('/group/get_groups_info', $body);
    }

    /**
     * 修改群组信息。仅传需要修改的字段，也支持零值。
     *
     * @param array<string, mixed> $fields
     * @return array<string, mixed>
     */
    public function setGroupInfo(array $fields): array
    {
        return $this->post('/group/set_group_info_ex', $fields);
    }

    /**
     * 邀请用户进群。
     *
     * @param string             $groupId
     * @param array<int, string> $invitedUserIds
     * @param string             $reason
     * @return array<string, mixed>
     */
    public function inviteUserToGroup(string $groupId, array $invitedUserIds, string $reason = ''): array
    {
        $body = [
            'groupID'        => $groupId,
            'invitedUserIDs' => $invitedUserIds,
            'reason'         => $reason,
        ];

        return $this->post('/group/invite_user_to_group', $body);
    }

    /**
     * 将用户从群组中移除。不能移除群主，如果移除群主，先转让群主身份。
     *
     * @param string   $groupId 群组 ID
     * @param string[] $userIds 被移除的用户 ID 列表
     * @param string   $reason  移除原因
     * @return array<string, mixed> OpenIM 返回的数据
     */
    public function kickGroup(string $groupId, array $userIds, string $reason = ''): array
    {
        $body = [
            'groupID'       => $groupId,
            'kickedUserIDs' => $userIds,
            'reason'        => $reason,
        ];

        return $this->post('/group/kick_group', $body);
    }

    /**
     * 解散群，解散后无法恢复。
     *
     * @param string $groupId      群ID
     * @param bool   $deleteMember 是否删除群成员信息
     * @return array<string, mixed>
     */
    public function dismissGroup(string $groupId, bool $deleteMember = false): array
    {
        $body = [
            'groupID'      => $groupId,
            'deleteMember' => $deleteMember,
        ];

        return $this->post('/group/dismiss_group', $body);
    }

    /**
     * 将群主转让给其他群成员，转让后原群主变为普通成员。
     *
     * @param string $groupId        群组 ID
     * @param string $oldOwnerUserId 原群主用户 ID
     * @param string $newOwnerUserId 新群主用户 ID
     * @return array<string, mixed> OpenIM 返回的数据
     */
    public function transferGroup(string $groupId, string $oldOwnerUserId, string $newOwnerUserId): array
    {
        $body = [
            'groupID'        => $groupId,
            'oldOwnerUserID' => $oldOwnerUserId,
            'newOwnerUserID' => $newOwnerUserId,
        ];

        return $this->post('/group/transfer_group', $body);
    }
}
