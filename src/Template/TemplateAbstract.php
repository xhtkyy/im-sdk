<?php
/**
 * @author ThompsonCr
 * @date 2022/3/24 0024
 */

namespace KyyIM\Template;


use KyyIM\Constants\ImUserType;
use KyyIM\Constants\MessageAction;
use KyyIM\Constants\MessageClass;
use KyyIM\Facades\Im;
use KyyIM\Template\Models\Institution;
use KyyIM\Template\Models\Kefu;
use KyyIM\Template\Models\Member;
use KyyIM\Template\Models\Project;
use KyyIM\Template\Models\TemplateMessage;
use KyyIM\Template\Services\TemplateMessageService;
use KyyPush\exception\KyyThirdPushException;
use KyyPush\PushService;
use KyyTools\Facades\Lang;
use KyyTools\Facades\Log;
use ThirdPush\APP;
use ThirdPush\UserType;

abstract class TemplateAbstract implements TemplateInterface {
    //模板
    protected $template;
    //结构
    protected $type;
    protected $scene;
    protected $message_class = MessageClass::SERVICE_NOTICE;
    protected $header = [];
    protected $content = [];
    protected $data = [];
    protected $config = [];
    protected $app = APP::MALL_APP;

    public function setConfig(array $config): TemplateInterface {
        $this->config = $config;
        return $this;
    }

    public function getConfig(): array {
        return $this->config;
    }

    public function setType(int $type): TemplateInterface {
        $this->type = $type;
        return $this;
    }

    /**
     * 设置场景
     * @param int $scene
     * @return TemplateInterface
     */
    public function setScene(int $scene): TemplateInterface {
        $this->scene = $scene;
        return $this;
    }

    public function setMessageClass(int $messageClass): TemplateInterface {
        $this->message_class = $messageClass;
        return $this;
    }

    public function setTemplate(string $class): TemplateInterface {
        $this->template = $class;
        return $this;
    }

    public function setHeaders(array $param): TemplateInterface {
        $this->header = array_merge($this->header, $param);
        return $this;
    }

    public function setHeader(int $institution_id, int $project_id = null): TemplateInterface {
        $institution = Institution::query()->where('id', '=', $institution_id)
            ->selectRaw("id as institution_id,institution_name,institution_logo") //,"purchaser","supplier"
            ->first();
        $data        = $institution ? $institution->toArray() : [];
        if ($project_id) {
            $project = Project::query()->where('id', '=', $project_id)->selectRaw("id as project_id,project_name")->first();
            if ($project) $data = array_merge($data, $project->toArray());
        }
        $this->header = array_merge($this->header, $data);
        return $this;
    }

    public function setData(array $param): TemplateInterface {
        $this->data = array_merge($this->data, $param);
        return $this;
    }

    public function setContent(array $param): TemplateInterface {
        //判断是否是body
        if (isset($param['body']) && !empty($param['body'])) {
            $this->setBody($param['body']);
            unset($param['body']);
        }

        $this->content = array_merge($this->content, $param);
        return $this;
    }

    public function setBody(array $body): TemplateInterface {
        $temp = [];
        if (is_array(reset($body))) {
            foreach ($body as $item) {
                $temp[] = ["key" => current($item) ?? '', "value" => next($item) ?? ''];
            }
        } else {
            !is_array($body) && $body = [$body];
            foreach ($body as $key => $value) {
                $temp[] = compact('key', 'value');
            }
        }
        $this->content = array_merge($this->content, ['body' => $temp]);
        return $this;
    }

    public function getContent(): array {
        return $this->content;
    }

    public function setAll(array $content, array $header = [], array $data = []): TemplateInterface {
        $this->header  = $header;
        $this->data    = $data;
        $this->content = $content;
        return $this;
    }


    public function send(array $user_ids, int $from_user_id = 0, int $from_user_type = ImUserType::MEMBER): bool {
        $user_ids = array_unique(array_filter($user_ids));
        //处理模板数据
        !empty($header) && $this->setHeaders($header);
        !empty($body) && $this->setContent(["body" => $body]);
        !empty($data) && $this->setData($data);
        //设置
        $this->setData([
            'from_member_id'   => $from_user_id,
            'from_member_type' => $from_user_type,
        ]);
        if (empty($user_ids)) return false;
        try {
            //推送到IM
            $im  = Im::message();
            $tss = new TemplateMessageService;
            //区别多发/单发模式
            if (count($user_ids) > 1) {
                //多发模式 适用于无状态消息发送
                //IM推送
                //查找用户ids
                $im_users = Member::query()
                    ->whereIn('member_id', $user_ids)
                    ->selectRaw('member_id,im_user_id')
                    ->get();
                //保存到数据库
                foreach ($im_users as $im_user) {
                    if (empty($im_user->im_user_id)) continue;
                    $template_message_id = $tss->addGetId(array_merge($this->toArray(), [
                        'from_member_id'   => $from_user_id,
                        'from_member_type' => $from_user_type,
                        'accept_member_id' => $im_user->member_id,
                        'im_push_status'   => 1000
                    ]));
                    $this->setData(compact("template_message_id"));
                    $imPushRes = $im->template([$im_user->im_user_id], $this);
                    if ($imPushRes['code'] !== 1000) {
                        TemplateMessage::query()->where("id", "=", $template_message_id)->update([
                            'im_push_status' => $imPushRes['code'] //im推送状态
                        ]);
                    }
                }
            } else {
                //单发模式 适用于有状态消息发送
                $accept_member_id    = current($user_ids);
                $template_message_id = $tss->addGetId(array_merge($this->toArray(), [
                    'from_member_id'   => $from_user_id,
                    'from_member_type' => $from_user_type,
                    'accept_member_id' => $accept_member_id,
                    'im_push_status'   => 1000
                ]));
                $this->setData(compact("template_message_id"));
                //查找用户ids
                $im_user_id = Member::query()->where('member_id', "=", $accept_member_id)->value("im_user_id");
                //发送im消息
                $imPushRes = $im->template([$im_user_id], $this);
                //更新消息发送状态
                if ($imPushRes['code'] !== 1000) {
                    TemplateMessage::query()->where("id", "=", $template_message_id)->update([
                        'im_push_status' => $imPushRes['code'] //im推送状态
                    ]);
                }
            }
            //推送
            $this->sendToApp(array_values($user_ids));
        } catch (\Exception $exception) {
            Log::exception("im-template", $exception);
        }

        return true;
    }

    /**
     * @param array $user_ids
     * @param array $kefu_ids
     * @param int $from_user_id
     * @param int $from_user_type
     * @return bool
     * @throws \Exception
     */
    public function sendToAll(array $user_ids, array $kefu_ids, int $from_user_id = 0, int $from_user_type = ImUserType::MEMBER): bool {
        $res = true;
        //发送到 客户
        if (!empty($user_ids)) {
            $res = $this->send($user_ids, $from_user_id, $from_user_type);
        }
        //发送到 客服
        if (!empty($kefu_ids) && $res) {
            $res = $this->sendToKefu($kefu_ids, $from_user_id, $from_user_type);
        }
        return $res;
    }

    public function sendToKefu(array $kefu_ids, int $from_user_id = 0, int $from_user_type = ImUserType::MEMBER): bool {
        $kefu_ids = array_unique(array_filter($kefu_ids));
        if (empty($kefu_ids)) return false;
        $im  = Im::message();
        $tss = new TemplateMessageService;
        try {
            //区别多发/单发模式
            if (count($kefu_ids) > 1) {
                //多发模式 适用于无状态消息发送
                //IM推送
                //查找用户ids
                $im_user_ids = Kefu::query()->whereIn('id', $kefu_ids)->pluck('im_user_id')->toArray();
                //发送
                $imPushRes = $im->template($im_user_ids, $this);
                //保存到数据库
                foreach ($kefu_ids as $kefu_id) {
                    $tss->add(array_merge($this->toArray(), [
                        'from_member_id'     => $from_user_id,
                        'from_member_type'   => $from_user_type,
                        'accept_member_id'   => $kefu_id,
                        'accept_member_type' => UserType::KEFU,
                        'im_push_status'     => $imPushRes['code'] //im推送状态
                    ]));
                }

            } else {
                //单发模式 适用于有状态消息发送
                $accept_member_id    = current($kefu_ids);
                $template_message_id = $tss->addGetId(array_merge($this->toArray(), [
                    'from_member_id'     => $from_user_id,
                    'from_member_type'   => $from_user_type,
                    'accept_member_id'   => $accept_member_id,
                    'accept_member_type' => UserType::KEFU,
                ]));
                $this->setData(compact("template_message_id"));
                //查找用户ids
                $im_user_id = Kefu::query()->where('id', "=", $accept_member_id)->value("im_user_id");
                //发送im消息
                $imPushRes = $im->template([$im_user_id], $this);
                //更新消息发送状态
                TemplateMessage::query()->where("id", "=", $template_message_id)->update([
                    'im_push_status' => $imPushRes['code'] //im推送状态
                ]);
            }
            //推送到坐席端
            $this->setAPP(APP::SEAT_APP)->sendToApp(array_values($kefu_ids), UserType::KEFU);
        } catch (\Exception $exception) {
            //写入日志
            Log::exception("im-template", $exception);
        }

        return true;
    }

    public function setAPP(int $app): TemplateInterface {
        $this->app = $app;
        return $this;
    }

    private function sendToApp(array $ids, int $user_type = UserType::MEMBER) {
        //im action
        $extra           = $this->data;
        $extra['action'] = MessageAction::IM;
        try {
            $content = current(Lang::trans2tw([$this->content['title']]));
            $title   = MessageClass::CONSTANT_NAME[$this->message_class] ?? '你有一條新的通知';

            /**
             * @var PushService $thirdPush
             */
            $thirdPush = app(PushService::class);
            $thirdPush->singleAppPush($this->app, $user_type, $ids, $title, $content, $extra);

        } catch (KyyThirdPushException $exception) {
            //不影响主业务
        }
    }
}
