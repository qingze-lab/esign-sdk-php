<?php
declare(strict_types = 1);

namespace QingzeLab\OpenIM\Service;

/**
 * Class ConversationService
 *
 * 会话领域服务。
 */
final class ConversationService extends AbstractService
{
    /**
     * 根据是否置顶、发消息的时间先后获取排序后的会话列表。
     *
     * @param string $userId 用户 ID
     * @return array<string, mixed> OpenIM 返回的数据n
     */
    public function getSortedConversationList(string $userId, array $fields = []): array
    {
        $query = array_merge([
            'userID' => $userId,
        ], $fields);

        return $this->get('/conversation/get_sorted_conversation_list', $query);
    }
}
