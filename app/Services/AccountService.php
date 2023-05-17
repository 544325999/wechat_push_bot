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
    public function turnOnAirConditioner() :string
    {
        $config = config('mio');
        $params = [
            ['did' => $config['air_conditioner_did'], 'siid' => 2, 'piid' => 1, 'value' => true],
            // 0 - Auto 1 - Cool 制冷  2 - Dry 3 - Heat 4 - Fan
            ['did' => $config['air_conditioner_did'], 'siid' => 2, 'piid' => 2, 'value' => 1],
            // 温度 16-30
            ['did' => $config['air_conditioner_did'], 'siid' => 2, 'piid' => 3, 'value' => 24],
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
