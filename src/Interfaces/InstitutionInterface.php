<?php

namespace KyyIM\Interfaces;

/**
 * 机构
 */
interface InstitutionInterface {
    /**
     * 初始化同步
     * @param array $batch
     * @return bool
     */
    public function init(array $batch): bool;

    /**
     * 批量添加
     * @param array $batch
     * @return bool
     */
    public function add(array $batch): bool;

    /**
     * 批量删除
     * @param array $batch
     * @return bool
     */
    public function del(array $batch): bool;
}
