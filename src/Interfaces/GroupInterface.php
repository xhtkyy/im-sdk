<?php

namespace KyyIM\Interfaces;

/**
 * 群组
 */
interface GroupInterface {

    //创建im channel（消息通道）
    public function createChannel(array $extra = []): string;

    //创建im channel_users（消息通道用户）
    public function createChannelUser(string $channelId, string $userId): bool;

    //批量创建im channel_users（消息通道用户）
    public function createChannelUsers(string $channelId, array $userIds): bool;

    /**
     * 删除im channel_users（消息通道用户）
     * @param string $channelId 消息通道id
     * @param string $userId 用户id
     * @return bool
     */
    public function deleteChannelUser(string $channelId, string $userId): bool;

    /**
     * 批量删除im channel_users（消息通道用户）
     * @param string $channelId 消息通道id
     * @param array $userIds 用户ids
     * @return bool
     */
    public function deleteChannelUsers(string $channelId, array $userIds): bool;

    /**
     * 查找im channel user list（消息通道用户）
     * @param string $channelId 消息通道id
     * @param array $extra 额外的参数
     * @return array
     */
    public function searchChannelUsers(string $channelId, array $extra = []): array;

    /**
     * 创建im session（会话：群）
     * @param string $title 标题
     * @param array $extra 额外的参数
     * @return string
     */
    public function createSession(string $title, array $extra = []): string;

    /**
     * 更新im session（会话：群）
     * @param string $sessionId 群标识
     * @param string $title 标题
     * @param array $extra 额外的参数
     * @return bool
     */
    public function updateSession(string $sessionId, string $title, array $extra = []): bool;

    /**
     * 删除im session（会话：群）
     * @param string $sessionId 群标识
     * @return bool
     */
    public function deleteSession(string $sessionId): bool;

    /**
     * 查找im session user list（会话：群）
     * @param string $sessionId 群标识
     * @param array $extra 额外的参数
     * @return array
     */
    public function searchSessionUsers(string $sessionId, array $extra = []): array;

    /**
     * 获取im session user（会话：群）
     * @param string $sessionId 群标识
     * @param string $userId 用户ID
     * @return array|mixed
     */
    public function getSessionUser(string $sessionId, string $userId): array;

    /**
     * 群成员是否存在
     * @param string $sessionId 群标识
     * @param string $userId 用户ID
     * @return int 0：异常，1：存在，2：不存在
     */
    public function existsSessionUser(string $sessionId, string $userId): int;

    /**
     * 创建im session_users（会话群成员）
     * @param string $sessionId 群标识
     * @param string $userId 用户ID
     * @param array $extra 额外的参数
     * @return bool
     */
    public function createSessionUser(string $sessionId, string $userId, array $extra = []): bool;

    /**
     * 批量创建im session_users（会话群成员）
     * @param string $sessionId 群标识
     * @param array $userData 用户数据
     * @param string $executor_id 邀请人
     * @return bool
     */
    public function createSessionUsers(string $sessionId, array $userData, string $executor_id = ''): bool;

    /**
     * 删除im session_users（会话群成员）
     * @param string $sessionId 群标识
     * @param string $userId 用户ID
     * @param string $executor_id 执行人
     * @return bool
     */
    public function deleteSessionUser(string $sessionId, string $userId, string $executor_id = ''): bool;

    /**
     * 批量删除im session_users（会话群成员）
     * @param string $sessionId 群标识
     * @param array $userIds 用户ID列表
     * @param string $executor_id 执行人
     * @return bool
     */
    public function deleteSessionUsers(string $sessionId, array $userIds, string $executor_id = ''): bool;

    /**
     * 更新im session_users（会话群成员）
     * @param string $sessionId 群标识
     * @param string $userId 用户ID
     * @param array $extra 额外的参数
     * @return bool
     */
    public function updateSessionUser(string $sessionId, string $userId, array $extra = []): bool;

    /**
     * 创建联系人
     * @param string $userId 用户标识
     * @param string $friendId 好友标识
     * @param array $extra 额外的参数
     * @return array
     */
    public function createContact(string $userId, string $friendId, array $extra = []): array;

    /**
     * 获取咨询会话信息
     * @param string $userId 用户标识
     * @return array
     */
    public function getConsultSession(string $userId): array;

    /**
     * 设置用户是否可查看历史消息
     * @param string $session_id
     * @param string $im_user_id
     * @param int|null $can_view_history //0允许 1不允许
     * @return bool
     */
    public function setCanViewHistory(string $session_id, string $im_user_id, int $can_view_history = 0): bool;

    /**
     * 更新群主
     * @param string $session_id
     * @param string $im_user_id
     * @return bool
     */
    public function setSessionCreator(string $session_id, string $im_user_id): bool;
}
