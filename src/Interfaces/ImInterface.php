<?php

namespace KyyIM\Interfaces;

/**
 * Interface ImServiceInterface
 * @package App\Im\Contracts
 */
interface ImInterface {
    /**
     * 用户相关
     * @return UserInterface
     */
    public function user(): UserInterface;

    /**
     * 群组相关
     * @return GroupInterface
     */
    public function group(): GroupInterface;

    /**
     * 消息相关
     * @return MessageInterface
     */
    public function message(): MessageInterface;

    /**
     * 机构相关
     * @return InstitutionInterface
     */
    public function institution(): InstitutionInterface;
}
