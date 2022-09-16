<?php

namespace App\Services;

class AccountService
{
    /**
     * 文本信息处理
     * @param $message
     * @return string
     */
    public function textHandler($message) :string
    {
        if ($message == '打开空调') {
            return $this->turnOnAirConditioner();
        } else if ($message == '关闭空调') {
            return $this->turnOffAirConditioner();
        }
        return '';
    }


    /**
     * 开启空调
     * @return string
     */
    protected function turnOnAirConditioner() :string
    {
        $config = config('mio');
        $params = [
            ['did' => $config['air_conditioner_did'], 'siid' => 2, 'piid' => 1, 'value' => true],
            ['did' => $config['air_conditioner_did'], 'siid' => 2, 'piid' => 2, 'value' => 1],
        ];
        try {
            $service = new MiIoService();
            $service::setMioSpec($params);
            return '开启成功';
        } catch (\Exception $exception) {
            return '开启失败';
        }
    }

    /**
     * 关闭空调
     * @return string
     */
    protected function turnOffAirConditioner() :string
    {
        $config = config('mio');

        $params = [
            ['did' => $config['air_conditioner_did'], 'siid' => 2, 'piid' => 1, 'value' => false],
        ];
        try {
            $service = new MiIoService();
            $service::setMioSpec($params);
            return '已关闭';
        } catch (\Exception $exception) {
            return '关闭失败';
        }
    }
}
