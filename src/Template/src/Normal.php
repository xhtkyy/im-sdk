<?php
/**
 * @author ThompsonCr
 * @date 2022/3/24 0024
 */

namespace KyyIM\Template\src;

use KyyIM\Constants\ErrorCode;
use KyyIM\Constants\MessageConstant;
use KyyIM\Exception\ImException;
use KyyIM\Template\TemplateAbstract;
use KyyIM\Template\TemplateInterface;
use Illuminate\Support\Facades\Validator;

/**
 * 普通模板
 */
class Normal extends TemplateAbstract implements TemplateInterface {

    /**
     * @throws ImException
     */
    public function toArray(): array {
        //验证器验证
        $validator = Validator::make([
            "header"  => $this->header,
            "content" => $this->content
        ], [
            'content.title' => 'required',
        ]);
        if ($validator->fails()) {
            throw new ImException($validator->errors()->first(), ErrorCode::ERROR);
        }
        //返回
        return [
            "template" => $this->template,
            "type"     => $this->type,
            "scene"    => $this->scene,
            "class"    => $this->message_class,
            "header"   => [
                'institution_id'   => $this->header['institution_id'] ?? 0,
                'institution_logo' => $this->header['institution_logo'] ?? '',
                'institution_name' => $this->header['institution_name'] ?? '',
                'institution_type' => $this->header['institution_type'] ?? MessageConstant::BUYER,
            ],
            "content"  => [
                'title' => $this->content['title'],
                'body'  => $this->content['body']
            ],
            "data"     => $this->data,
        ];
    }
}
