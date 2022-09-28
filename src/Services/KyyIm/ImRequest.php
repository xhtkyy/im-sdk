<?php

namespace KyyIM\Services\KyyIm;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Redis\Connections\Connection;
use Illuminate\Support\Facades\Redis;
use KyyTools\Facades\Log;
use Monolog\Logger;
use Psr\Http\Message\ResponseInterface;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Manager;

trait ImRequest {
    protected $config;
    protected $requestLogData = [];//请求日志数据

    /**
     * 获取请求token
     * @return string
     */
    private function getRequestToken(): string {
        $ttl = $this->config['jwt_ttl'] ?? 7200;
        /**
         * @var Connection $redis
         */
        $redis = Redis::connection();
        $token = null;
        if ($redis->exists("kyy_im_token")) {
            $token = $redis->get("kyy_im_token");
        }
        if (empty($token)) {
            /**
             * @var Manager $manager
             */
            $manager = JWTAuth::manager();
            $manager->getJWTProvider()->setSecret($this->config['jwt_secret'] ?? '');
            $payload = $manager->getPayloadFactory()
                ->setTTL($ttl)
                ->customClaims([
                    'sub' => 0
                ])
                ->make();
            $token   = $manager->encode($payload)->get();
            $redis->set("kyy_im_token", $token, "EX", $ttl - 200);
        }
        return $token;
    }

    /**
     * 移除请求token
     * @return void
     */
    private function removeRequestToken() {
        /**
         * @var Connection $redis
         */
        $redis = Redis::connection();
        $redis->del(["kyy_im_token"]);
    }

    /**
     * request http
     * @param string $method 请求方式
     * @param string $url 请求连接
     * @param array $data 请求数据
     * @return array|mixed
     */
    private function requestHttp(string $method, string $url, array $data = []) {
        $client  = new Client([
            'base_uri' => $this->config['base_uri'] ?? '',
            'timeout'  => 5,
        ]);
        $options = [
            'headers' => [
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . $this->getRequestToken(),
            ],
        ];
        if (env('APP_ENV') == 'pre') {

            $proxy            = sprintf("http://%s:%s@%s:%s", $this->config['proxy_user'], $this->config['proxy_pwd'], $this->config['proxy_address'], $this->config['proxy_port']);
            $options['proxy'] = [
                'http'  => $proxy,
                'https' => $proxy,
            ];
        }
        $method = strtolower($method);
        switch ($method) {
            case 'get':
                $options['query'] = $data;
                break;
            default:
                $options['json'] = $data;
                break;
        }
        $this->saveLogData([
            'base_uri' => $this->config['base_uri'] ?? '',
            'method'   => $method,
            'api'      => $url,
            'options'  => $options,
        ]);
        try {
            return $this->formatResponse($client->request($method, $url, $options));
        } catch (GuzzleException $exception) {
            return $this->formatResponse($exception);
        }
    }

    /**
     * 格式化response
     * @param $response
     * @return array
     */
    private function formatResponse($response): array {
        $result = [
            'success' => false,  //对响应数据进行判断（成功：true，失败：false）
            'code'    => 0,
            'msg'     => 'default error',
            'data'    => null,
        ];
        if ($response instanceof ResponseInterface) {
            if ($response->getStatusCode() == 200) {
                $body = json_decode($response->getBody(), true);
                if (is_array($body) && !empty($body)) {
                    $result            = $body;
                    $result['success'] = $body['code'] == 1000;
                } else {
                    $result['msg']  = 'body json error';
                    $result['data'] = $response->getBody();
                }
            } else {
                $result['msg']  = 'status code error ' . $response->getStatusCode();
                $result['data'] = $response->getBody();
            }
        } else if ($response instanceof GuzzleException) {
            //todo 校验是否是令牌失效 然后清除缓存令牌
            $result['msg']  = $response->getMessage();
            $result['data'] = $response;
        }
        //写入日志
        $this->saveLogData(['result' => $result]);
        $this->writeLog();

        return $result;
    }

    /**
     * 保存日志数据
     * @param array $data 记录数据
     */
    private function saveLogData(array $data) {
        $this->requestLogData = array_merge($this->requestLogData, $data);
    }

    //写日志
    private function writeLog() {
        $logger = Log::channel("im-request");
        if (isset($this->requestLogData['result'])) {
            $logger->log(
                !$this->requestLogData['result']['success'] ? Logger::ERROR : Logger::DEBUG,
                $this->requestLogData['result']['msg'],
                $this->requestLogData
            );
        } else {
            $logger->log(Logger::ERROR, 'no result', $this->requestLogData);
        }
    }
}
