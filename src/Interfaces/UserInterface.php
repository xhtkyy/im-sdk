<?php

namespace KyyIM\Interfaces;

/**
 * Interface ImUserInterface
 * @package App\Im\Contracts
 */
interface UserInterface {

    /**
     * 创建
     * @param string $name
     * @param $portrait
     * @param array $extra
     * @return string
     */
    public function createUser(string $name, $portrait = null, array $extra = []): string;

    /**
     * 更新
     * @param string $userId
     * @param string $name
     * @param $portrait
     * @param array $extra
     * @return bool
     */
    public function updateUser(string $userId, string $name, $portrait = null, array $extra = []): bool;

    /**
     * 查询im user
     * @param string $userId
     * @return array
     */
    public function getUser(string $userId): array;
}
