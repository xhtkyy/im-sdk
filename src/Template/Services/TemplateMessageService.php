<?php

namespace KyyIM\Template\Services;


use KyyIM\Constants\MessageConstant;
use KyyIM\Template\Models\TemplateMessage;

class TemplateMessageService {
    /**
     * 添加
     * @param $param
     * @return bool
     */

    public function add($param): bool {
        return TemplateMessage::query()
            ->insert([
                "institution_id"   => $param['header']['institution_id'] ?? 0,
                "institution_type" => $param['header']['institution_type'] ?? MessageConstant::BUYER,
                "from_member_id"   => $param['from_member_id'],
                "accept_member_id" => $param['accept_member_id'],
                "project_id"       => $param['header']['project_id'] ?? null,
                "template"         => $param['template'],
                "class"            => $param['class'],
                "type"             => $param['type'],
                "title"            => $param['content']['title'],
                "message"          => json_encode($param, JSON_UNESCAPED_UNICODE),
            ]);
    }

    public function addGetId($param): int {
        return TemplateMessage::query()
            ->insertGetId([
                "institution_id"   => $param['header']['institution_id'] ?? 0,
                "institution_type" => $param['header']['institution_type'] ?? MessageConstant::BUYER,
                "from_member_id"   => $param['from_member_id'],
                "accept_member_id" => $param['accept_member_id'],
                "project_id"       => $param['header']['project_id'] ?? null,
                "template"         => $param['template'],
                "class"            => $param['class'],
                "type"             => $param['type'],
                "title"            => $param['content']['title'],
                "message"          => json_encode($param, JSON_UNESCAPED_UNICODE),
            ]);
    }

    public function getStatus($id): int {
        return TemplateMessage::query()->where("id", '=', $id)->value('status');
    }
}
