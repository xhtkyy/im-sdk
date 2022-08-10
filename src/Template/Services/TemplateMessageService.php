<?php

namespace KyyIM\Template\Services;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use KyyIM\Constants\ImUserType;
use KyyIM\Constants\MessageConstant;
use KyyIM\Constants\TemplateMsgStatus\Common;
use KyyIM\Facades\Im;
use KyyIM\Template\Models\Member;
use KyyIM\Template\Models\TemplateMessage;
use KyyTools\Facades\Log;

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

    public function getStatus($id) {
        if (Str::contains($id, ",")) {
            $id = array_filter(explode(",", $id));
        }
        if (is_array($id)) {
            return TemplateMessage::query()->whereIn("id", $id)->selectRaw("id,status")->get();
        }
        return TemplateMessage::query()->where("id", '=', $id)->value('status');
    }

    public function updateStatus($scenes, int $status, int $operator, int $relation_id, string $relation_field = "id", bool $limit_one = true): bool {
        $query = TemplateMessage::query()
            ->where("accept_member_type", "=", ImUserType::MEMBER) //暂时只支持 访客端模板消息
            ->where("status", "=", Common::WAIT)
            ->where("message->data->$relation_field", "=", $relation_id);
        if (is_array($scenes)) {
            $query->whereIn("type", $scenes);
        } else {
            $query->where("type", "=", $scenes);
        }
        if (!$limit_one && $operator != 0) {
            $query->where("accept_member_id", "=", $operator);
        }
        $list = $query->select(["id", "accept_member_id", "status"])->get();
        if ($list->count() > 0) {
            $update_member_ids = [];
            DB::beginTransaction();
            try {
                foreach ($list as $item) {
                    $update_member_ids[] = $item->accept_member_id;
                    if ($operator == 0) {
                        //无指定操作用户
                        $item->status = $status;
                    } elseif ($item->accept_member_id == $operator) {
                        //有操作用户
                        $item->status = $status;
                    } else {
                        $item->status = Common::OTHER_DO; //被人处理
                    }
                    $item->save();
                }
                DB::commit();
            } catch (\Exception $exception) {
                DB::rollBack();
                Log::channel("template-message")->error("更新模板消息状态失败：" . $exception->getMessage(), func_get_args());
                return false;
            }
            //通知用户页面状态更新
            //查找用户ids
            $im_user_ids = Member::query()->whereIn('member_id', array_unique($update_member_ids))->pluck("im_user_id")->toArray();
            $im_user_ids && Im::message()->notice($im_user_ids, ["status_update" => true], 2);
        }
        return true;
    }
}
