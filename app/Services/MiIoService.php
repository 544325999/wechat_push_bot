<?php
namespace App\Services;


class MiIoService {
    protected static $client;
    public function __construct()
    {
        $config = config('mio');
        if (!$config['sid'] || !$config['user'] || !$config['pwd']) {
            throw new \Exception('请生成env文件并填写米家配置配置');
        }
        self::$client = new MiIoClient($config['sid'], $config['user'], $config['pwd']);
    }

    /**
     * 获取全部设备列表
     *   返回结果说明
     *   name: 设备名称
     *   did: 设备ID
     *   isOnline: 设备是否在线
     *   model: 设备产品型号, 根据这个去米家产品库查该产品相关的信息
     * @return mixed
     */
    public static function getList()
    {
        $postData = [
            'getVirtualModel' => false,
            'getHuamiDevices' => 0
        ];
        return self::$client::send('/home/device_list', $postData);
    }

    /**
     * 获取设备属性
     * @param $did
     * @param $siid
     * @param $piid
     * @return mixed
     */
    public static function getMioSpec($did, $siid, $piid)
    {
        $postData = [
            'params' => [
                [
                    'did' => $did,
                    'siid' => $siid,
                    'piid' => $piid
                ]
            ]
        ];
        $result = self::$client::send('/miotspec/prop/get', $postData);
        if (isset($result[0])) {
            return $result[0]['value'];
        }
        return $result;
    }

    public static function setMioSpec($params)
    {
        //"{\"params\":[{\"did\":\"111111111\",\"siid\":2,\"piid\":1,\"value\":true},{\"did\":\"111111111\",\"siid\":2,\"piid\":6,\"value\":70}]}");
        $postData = [
            'params' => $params
        ];
        return self::$client::send('/miotspec/prop/set', $postData);
    }

}
