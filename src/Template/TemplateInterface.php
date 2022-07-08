<?php
/**
 * @author ThompsonCr
 * @date 2022/3/24 0024
 */

namespace KyyIM\Template;

use KyyIM\Constants\ImUserType;

interface TemplateInterface {

    /**
     * 模板配置
     * @param array $config
     * @return TemplateInterface
     */
    public function setConfig(array $config): TemplateInterface;

    public function getConfig(): array;

    /**
     * 设置消息类型
     * @param int $type
     * @return $this
     */
    public function setType(int $type): self;

    /**
     * 设置消息来源类
     * @param int $messageClass
     * @return $this
     */
    public function setMessageClass(int $messageClass): self;

    /**
     * 设置模板名称
     * @param string $class
     * @return $this
     */
    public function setTemplate(string $class): self;

    /**
     * 设置头部数据
     * @param array $param
     */
    public function setHeaders(array $param): self;

    /**
     * 设置头部数据 通过机构id、项目id
     * @param int $institution_id
     * @param int|null $project_id
     * @return $this
     */
    public function setHeader(int $institution_id, int $project_id = null): self;

    /**
     * 设置跳转数据
     * @param array $param
     */
    public function setData(array $param): self;

    /**
     * 设置内容
     * @param array $param
     */
    public function setContent(array $param): self;

    /**
     * 设置内容体
     * @param array $body
     * @return $this
     */
    public function setBody(array $body): self;

    /**
     * 设置全部
     * @param array $content
     * @param array $header
     * @param array $data
     * @return mixed
     */
    public function setAll(array $content, array $header = [], array $data = []): self;

    /**
     * 获取内容
     * @return array
     */
    public function getContent(): array;

    /**
     * 转数组格式
     * @return array
     */
    public function toArray(): array;

    /**
     * 发送
     * @param array $user_ids 接收用户（数组）
     * @param int $from_user_id 发送用户
     * @param int $from_user_type
     * @return bool
     */
    public function send(array $user_ids, int $from_user_id = 0, int $from_user_type = ImUserType::MEMBER): bool;

    public function sendToAll(array $user_ids, array $kefu_ids, int $from_user_id = 0, int $from_user_type = ImUserType::MEMBER): bool;

    public function sendToKefu(array $kefu_ids, int $from_user_id = 0, int $from_user_type = ImUserType::MEMBER): bool;

    /**
     * 设置场景值
     * @param int $scene
     * @return $this
     */
    public function setScene(int $scene): self;

    /**
     * 设置推送app
     * @param int $app
     * @return $this
     */
    public function setApp(int $app): self;
}
