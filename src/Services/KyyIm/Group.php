<?php

namespace KyyIM\Services\KyyIm;

use KyyIM\Exception\ImException;
use KyyIM\Interfaces\GroupInterface;

class Group extends AbstractKyyIm implements GroupInterface {
    use ImRequest;

    /**
     * 创建im channel（消息通道）
     * @param array $extra 额外的参数
     * @return string
     */
    public function createChannel(array $extra = []): string {
        $data   = [
            'domain_id' => $this->config['domain_id'],
            'scene'     => 0,//通道应用的场景：0=all 全部，1=guest 匿名访问，2=user 普通个人用户, 3=org 机构下用户 4=project 特定项目用户
            'mode'      => 0,//模式：0=自由发言模式, 1=禁言模式（仅管理员能发言）, 2=主持模式（由管理员指定发言人），3=受控模式（由系统根据算法来指定发言人，通道管理）
            'puppet'    => 0,//模拟单人模式下的用户标识符
        ];
        $data   = array_merge($data, $extra);
        $result = $this->requestHttp('post', 'im/channels', $data);
        if ($result['success']) {
            return $result['data']['id'] ?? '';
        }
        return '';
    }

    /**
     * 创建im channel_users（消息通道用户）
     * @param string $channelId 消息通道id
     * @param string $userId 用户id
     * @return bool
     */
    public function createChannelUser(string $channelId, string $userId): bool {
        $data   = [
            'domain_id'  => $this->config['domain_id'],
            'channel_id' => $channelId,
            'user_id'    => $userId,
        ];
        $result = $this->requestHttp('post', 'im/channel_users', $data);
        if ($result['success']) {
            return true;
        }
        return false;
    }

    /**
     * 批量创建im channel_users（消息通道用户）
     * @param string $channelId 消息通道id
     * @param array $userIds 用户ids
     * @return bool
     */
    public function createChannelUsers(string $channelId, array $userIds): bool {
        foreach ($userIds as $userId) {
            $result = $this->createChannelUser($channelId, $userId);
            if (!$result) {
                return false;
            }
        }
        return true;
    }

    /**
     * 删除im channel_users（消息通道用户）
     * @param string $channelId 消息通道id
     * @param string $userId 用户id
     * @return bool
     */
    public function deleteChannelUser(string $channelId, string $userId): bool {
        $result = $this->requestHttp('delete', 'im/channel_users/' . $userId . '?channel_id=' . $channelId);
        if ($result['success']) {
            return true;
        }
        return false;
    }

    /**
     * 批量删除im channel_users（消息通道用户）
     * @param string $channelId 消息通道id
     * @param array $userIds 用户ids
     * @return bool
     */
    public function deleteChannelUsers(string $channelId, array $userIds): bool {
        foreach ($userIds as $userId) {
            $result = $this->deleteChannelUser($channelId, $userId);
            if (!$result) {
                return false;
            }
        }
        return true;
    }

    /**
     * 查找im channel user list（消息通道用户）
     * @param string $channelId 消息通道id
     * @param array $extra 额外的参数
     * @return array
     */
    public function searchChannelUsers(string $channelId, array $extra = []): array {
        $data   = [
            'index'    => 1,
            'size'     => 1000,
            'queryRaw' => [
                'domain_id'  => $this->config['domain_id'],
                'channel_id' => $channelId,
            ],
        ];
        $data   = array_merge($data, $extra);
        $result = $this->requestHttp('post', 'im/channel_users/search', $data);
        if ($result['success']) {
            return $result['data'];
        }
        return [];
    }

    /**
     * 创建im session（会话：群）
     * @param string $title 标题
     * @param array $extra 额外的参数
     * @return string
     */
    public function createSession(string $title, array $extra = []): string {
        $data   = [
            'domain_id'   => $this->config['domain_id'],
            'org_id'      => $this->config['org_id'],
            'type'        => 0,//类型：0=临时会话
            'mode'        => 0,//模式：0=自由发言模式, 1=禁言模式（仅管理员能发言）, 2=主持模式（由管理员指定发言人），3=受控模式（由系统根据算法来指定发言人，通道管理）
            'locked'      => 0,//锁定，除了管理员，无法进行管理操作，包括更换标题、更换参与者，无法退出
            'mask'        => 0,//0' COMMENT '允许使用面具（匿名效果）
            'stay_on_top' => 0,//在所有用户的会话里置顶
            'capacity'    => 0,//参与者的最大数量，0 表示不限制
            'size'        => 0,//参与者的最大数量，0 表示不限制
            'title'       => $title,
            'icon'        => '',//图标资源标识号
            'spec_icon'   => '',//群头像地址
            'puppet'      => 0,//模拟单人模式下的用户标识符
            'creator'     => '',//创建人标识号，特殊标识号：(空)=系统自动创建
        ];
        $data   = array_merge($data, $extra);
        $result = $this->requestHttp('post', 'im/sessions', $data);
        if ($result['success']) {
            return $result['data']['id'] ?? '';
        }
        return '';
    }

    /**
     * 获取im session（会话：群）
     * @param string $sessionId 群标识
     * @return array
     */
    public function getSession(string $sessionId): array {
        $result = $this->requestHttp('get', 'im/sessions/' . $sessionId, [
            'domain_id' => $this->config['domain_id']
        ]);
        if ($result['success']) {
            return $result['data'];
        }
        return [];
    }

    /**
     * 更新im session（会话：群）
     * @param string $sessionId 群标识
     * @param string $title 标题
     * @param array $extra 额外的参数
     * @return bool
     */
    public function updateSession(string $sessionId, string $title, array $extra = []): bool {
        $data   = [
            'domain_id'   => $this->config['domain_id'],
            'session_id'  => $sessionId,
            'type'        => 0,//类型：0=临时会话
            'mode'        => 0,//模式：0=自由发言模式, 1=禁言模式（仅管理员能发言）, 2=主持模式（由管理员指定发言人），3=受控模式（由系统根据算法来指定发言人，通道管理）
            'locked'      => 0,//锁定，除了管理员，无法进行管理操作，包括更换标题、更换参与者，无法退出
            'mask'        => 0,//0' COMMENT '允许使用面具（匿名效果）
            'stay_on_top' => 0,//在所有用户的会话里置顶
            'capacity'    => 0,//参与者的最大数量，0 表示不限制
            'size'        => 0,//参与者的最大数量，0 表示不限制
            'title'       => $title,
            'icon'        => '',//图标资源标识号
            'spec_icon'   => '',//群头像地址
            'puppet'      => 0,//模拟单人模式下的用户标识符
            'creator'     => '',//创建人标识号，特殊标识号：(空)=系统自动创建
        ];
        $data   = array_merge($data, $extra);
        $result = $this->requestHttp('put', 'im/sessions', $data);
        if ($result['success']) {
            return true;
        }
        return false;
    }

    /**
     * 删除im session（会话：群）
     * @param string $sessionId 群标识
     * @return bool
     */
    public function deleteSession(string $sessionId): bool {
        $result = $this->requestHttp('delete', 'im/sessions/' . $sessionId);
        if ($result['success']) {
            return true;
        }
        return false;
    }

    /**
     * 查找im session user list（会话：群）
     * @param string $sessionId 群标识
     * @param array $extra 额外的参数
     * @return array
     */
    public function searchSessionUsers(string $sessionId, array $extra = []): array {
        $data   = [
            'index'    => 1,
            'size'     => 1000,
            'queryRaw' => [
                'domain_id'  => $this->config['domain_id'],
                'session_id' => $sessionId,
                'title'      => '',
            ],
        ];
        $data   = array_merge($data, $extra);
        $result = $this->requestHttp('post', 'im/session_users/search', $data);
        if ($result['success']) {
            return $result['data'];
        }
        return [];
    }

    /**
     * 获取im session user（会话：群）
     * @param string $sessionId 群标识
     * @param string $userId 用户ID
     * @return array|mixed
     */
    public function getSessionUser(string $sessionId, string $userId): array {
        $result = $this->requestHttp('get', 'im/session_users/' . $userId, [
            'domain_id'  => $this->config['domain_id'],
            'session_id' => $sessionId,
        ]);
        if ($result['success']) {
            return $result['data'];
        }
        return [];
    }

    /**
     * 群成员是否存在
     * @param string $sessionId 群标识
     * @param string $userId 用户ID
     * @return int 0：异常，1：存在，2：不存在
     */
    public function existsSessionUser(string $sessionId, string $userId): int {
        $result = $this->getSessionUser($sessionId, $userId);
        if (empty($result)) {
            return 0;
        }
        if (!$result['closed']) {
            return 1;
        }
        return 2;
    }

    /**
     * 创建im session_users（会话群成员）
     * @param string $sessionId 群标识
     * @param string $userId 用户ID
     * @param array $extra 额外的参数
     * @return bool
     */
    public function createSessionUser(string $sessionId, string $userId, array $extra = []): bool {
        $data   = [
            'domain_id'   => $this->config['domain_id'],
            'session_id'  => $sessionId,
            'user_id'     => $userId,
            'executor_id' => ''
        ];
        $data   = array_merge($data, $extra);
        $result = $this->requestHttp('post', 'im/session_users', $data);
        if ($result['success']) {
            return true;
        }
        //针对IM接口异常处理：添加已存在群成员错误，检查群成员是否存在
        if ($this->existsSessionUser($sessionId, $userId) == 1) {
            return true;
        }
        return false;
    }

    /**
     * 批量创建im session_users（会话群成员）
     * @param string $sessionId 群标识
     * @param array $userData 用户数据
     * @param string $executor_id 邀请人
     * @return bool
     */
    public function createSessionUsers(string $sessionId, array $userData, string $executor_id = ''): bool {
        $data       = [];
        $commonData = [
            'domain_id'   => $this->config['domain_id'],
            'session_id'  => $sessionId,
            'executor_id' => $executor_id,//邀请人ID 系统行为传空字符
        ];
        foreach ($userData as $user) {
            $data[] = array_merge($commonData, $user);
        }
        $result = $this->requestHttp('post', 'im/batch/session_users/add', $data);
        if ($result['success']) {
            return true;
        }
        return false;
    }

    /**
     * 删除im session_users（会话群成员）
     * @param string $sessionId 群标识
     * @param string $userId 用户ID
     * @param string $executor_id 执行人
     * @return bool
     */
    public function deleteSessionUser(string $sessionId, string $userId, string $executor_id = ''): bool {
        $url    = sprintf("im/session_users/%s?session_id=%s&executor_id=%s", $userId, $sessionId, $executor_id);
        $result = $this->requestHttp('delete', $url);
        if ($result['success']) {
            return true;
        }
        //针对IM接口异常处理：删除不存在群成员错误，检查群成员是否存在
        if ($this->existsSessionUser($sessionId, $userId) == 2) {
            return true;
        }
        return false;
    }

    /**
     * 批量删除im session_users（会话群成员）
     * @param string $sessionId 群标识
     * @param array $userIds 用户ID列表
     * @param string $executor_id 执行人
     * @return bool
     */
    public function deleteSessionUsers(string $sessionId, array $userIds, string $executor_id = ''): bool {
        $data   = [
            'domain_id'   => $this->config['domain_id'],
            'executor_id' => $executor_id,
            'session_id'  => $sessionId,
            'ids'         => $userIds
        ];
        $result = $this->requestHttp('post', 'im/batch/session_users/del', $data);
        if ($result['success']) {
            return true;
        }
        foreach ($userIds as $userId) {
            if (!$this->deleteSessionUser($sessionId, $userId)) {
                return false;
            }
        }
        return true;
    }

    /**
     * 更新im session_users（会话群成员）
     * @param string $sessionId 群标识
     * @param string $userId 用户ID
     * @param array $extra 额外的参数
     * @return bool
     */
    public function updateSessionUser(string $sessionId, string $userId, array $extra = []): bool {
        $data   = [
            //'domain_id'  => $this->config['domain_id'],
            'session_id' => $sessionId,
            'user_id'    => $userId,
        ];
        $data   = array_merge($data, $extra);
        $result = $this->requestHttp('put', 'im/session_users', $data);
        if ($result['success']) {
            return true;
        }
        //针对IM接口异常处理：更新不存在群成员错误，检查群成员是否存在
        if ($this->existsSessionUser($sessionId, $userId) == 2) {
            return $this->createSessionUser($sessionId, $userId, $extra);
        }
        return false;
    }

    /**
     * 创建联系人
     * @param string $userId 用户标识
     * @param string $friendId 好友标识
     * @param array $extra 额外的参数
     * @return array
     * @throws ImException
     */
    public function createContact(string $userId, string $friendId, array $extra = []): array {
        $data   = [
            'domain_id' => $this->config['domain_id'],
            'user_id'   => $userId,
            'friend_id' => $friendId,
            'type'      => 1,
        ];
        $data   = array_merge($data, $extra);
        $result = $this->requestHttp('post', 'im/contacts', $data);
        if ($result['success']) {
            return [
                'id'         => $result['data']['id'],
                'session_id' => $result['data']['session_id'],
            ];
        }
        throw new ImException($result['msg'], $result['code']);
    }

    /**
     * 获取咨询会话信息
     * @param string $userId 用户标识
     * @return array
     */
    public function getConsultSession(string $userId): array {
        $result = $this->requestHttp('get', 'im/consult_sessions/by_user/' . $userId);
        if ($result['success']) {
            return $result['data'];
        }
        return [];
    }

    /**
     * @param string $session_id
     * @param string $im_user_id
     * @param int $can_view_history
     * @return bool
     * @throws ImException
     */
    public function setCanViewHistory(string $session_id, string $im_user_id, int $can_view_history = 0): bool {
        $result = $this->requestHttp('PUT', "im/sessions/can_view_history/{$session_id}", [
            "user_id"          => $im_user_id,
            "can_view_history" => $can_view_history
        ]);
        if ($result['success']) {
            return true;
        }
        throw new ImException("IM设置是否可查看历史消息失败");
    }

    /**
     * 设置会话群群主
     * @param string $session_id
     * @param string $im_user_id
     * @return bool
     * @throws ImException
     */
    public function setSessionCreator(string $session_id, string $im_user_id): bool {
        $result = $this->requestHttp('PUT', "im/sessions/creator", [
            "creator"    => $im_user_id,
            "session_id" => $session_id
        ]);
        if ($result['success']) {
            return true;
        }
        throw new ImException("IM修改会话群主失败");
    }
}
