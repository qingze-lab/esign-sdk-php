<?php
declare(strict_types = 1);

namespace QingzeLab\OpenIM\Service;

/**
 * Class UserService
 *
 * 用户领域服务，封装用户相关的 OpenIM 接口。
 */
final class UserService extends AbstractService
{
    /**
     * 获取用户的 token，需指定用户登录时所使用的终端类型。
     *
     * @param string $userId     用户 ID
     * @param int    $platformId 用户登录时的终端类型，取值为1-9
     * @return array<string, mixed>
     */
    public function getUserToken(string $userId, int $platformId): array
    {
        $body = [
            'userID'     => $userId,
            'platformID' => $platformId,
        ];

        return $this->post('/auth/get_user_token', $body);
    }

    /**
     * 获取指定用户详情列表。
     *
     * @param array<int, string> $userIds
     * @return array<string, mixed>
     */
    public function getUsersInfo(array $userIds): array
    {
        $body = [
            'userIDs' => $userIds,
        ];

        return $this->post('/user/get_users_info', $body);
    }

    /**
     * 用户通过 AppServer 完成账号注册后，AppServer 再调用此接口导入 IM 以实现账号打通。
     *
     * @param array $fields
     * @return array<string, mixed> OpenIM 返回的数据
     */
    public function userRegister(array $fields = []): array
    {
        $body = [
            'users' => $fields,
        ];

        return $this->post('/user/user_register', $body);
    }

    /**
     * 修改用户的头像、名称、Ex等信息。仅传需要修改的字段，也支持零值。
     *
     * @param array $fields
     * @return array<string, mixed>
     */
    public function updateUserInfoEx(array $fields): array
    {
        $body = [
            'userInfo' => $fields,
        ];

        return $this->post('/user/update_user_info_ex', $body);
    }
}
