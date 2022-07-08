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
 * 项目模板
 */
class Project extends TemplateAbstract implements TemplateInterface {

    /**
     * @return array
     * @throws ImException
     */
    public function toArray(): array {
        //验证器验证
        $validator = Validator::make([
            "header"  => $this->header,
            "content" => $this->content
        ], [
            'header.institution_id'   => 'required',
            'header.institution_logo' => '',
            'header.institution_name' => 'required',
            'header.project_name'     => 'required',
            'content.title'           => 'required',
        ]);
        if ($validator->fails()) {
            throw new ImException($validator->errors()->first(), ErrorCode::ERROR);
        }
        //返回
        return [
            "template" => $this->template,
            "type"     => $this->type,
            "scene"     => $this->scene,
            "class"    => $this->message_class,
            "header"   => [
                'institution_id'   => $this->header['institution_id'],
                'institution_logo' => $this->header['institution_logo'],
                'institution_name' => $this->header['institution_name'],
                'institution_type' => $this->header['institution_type'] ?? MessageConstant::BUYER,
                'project_id'       => $this->header['project_id'] ?? 0,
                'project_name'     => $this->header['project_name'],
            ],
            "content"  => [
                'title' => $this->content['title'],
                'body'  => $this->content['body'] ?? []
            ],
            "data"     => $this->data
        ];
    }
}
