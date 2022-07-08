<?php

namespace KyyIM\Services\KyyIm;

use KyyIM\Interfaces\UserInterface;

class User extends AbstractKyyIm implements UserInterface {
    use ImRequest;

    /**
     * 创建im user
     * @param string $name 昵称
     * @param mixed $portrait 头像
     * @param array $extra 额外的参数
     * @return string
     */
    public function createUser(string $name, $portrait = null, array $extra = []): string {
        $data   = [
            'domain_id' => $this->config['domain_id'],
            'title'     => $name,
            'avatar'    => $portrait ?? '',
        ];
        $data   = array_merge($data, $extra);
        $result = $this->requestHttp('post', 'im/users', $data);
        if ($result['success']) {
            return $result['data']['id'] ?? '';
        }
        return '';
    }

    /**
     * 更新im user
     * @param string $userId im user id
     * @param string $name 昵称
     * @param mixed $portrait 头像
     * @param array $extra 额外的参数
     * @return bool
     */
    public function updateUser(string $userId, string $name, $portrait = null, array $extra = []): bool {
        $data   = [
            'domain_id' => $this->config['domain_id'],
            'user_id'   => $userId,
            'title'     => $name,
            'avatar'    => $portrait ?? '',
        ];
        $data   = array_merge($data, $extra);
        $result = $this->requestHttp('put', 'im/users', $data);
        if ($result['success']) {
            return true;
        }
        return false;
    }

    /**
     * 查询im user
     * @param string $userId im user id
     * @return array|mixed
     */
    public function getUser(string $userId): array {
        $data   = [
            'domain_id' => $this->config['domain_id'],
        ];
        $result = $this->requestHttp('get', 'im/users/' . $userId, $data);
        if ($result['success']) {
            return $result['data'];
        }
        return [];
    }
}
