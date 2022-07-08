<?php

namespace KyyIM\Services\KyyIm;

use KyyIM\Exception\ImException;
use KyyIM\Interfaces\InstitutionInterface;

class Institution extends AbstractKyyIm  implements InstitutionInterface {

    use ImRequest;

    /**
     * @param array $batch
     * @return bool
     * @throws ImException
     */
    public function init(array $batch): bool {
        if (empty($batch)) {
            return true;
        }
        $result = $this->requestHttp('post', '/im/institutions/init', $batch);
        if ($result['status']) {
            return true;
        } else {
            throw new ImException("im请求[/im/institution/init]失败，错误信息：" . json_encode($result));
        }
    }

    /**
     * @param array $batch
     * @return bool
     * @throws ImException
     */
    public function add(array $batch): bool {
        $result = $this->requestHttp('post', '/im/batch/institutions/add', $batch);
        if ($result['status']) {
            return true;
        } else {
            throw new ImException("im请求[/im/batch/institutions/add]失败，错误信息：" . json_encode($result));
        }
    }

    /**
     * @param array $batch
     * @return bool
     * @throws ImException
     */
    public function del(array $batch): bool {
        $result = $this->requestHttp('post', '/im/batch/institutions/del', $batch);
        if ($result['status']) {
            return true;
        } else {
            throw new ImException("im请求[/im/batch/institutions/del]失败，错误信息：" . json_encode($result));
        }
    }
}
