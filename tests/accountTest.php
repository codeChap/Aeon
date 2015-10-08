<?php

    class accountTest extends PHPUnit_Framework_TestCase
    {
        public $config = array(
            'ip' => '196.38.158.118',
            'port' => '7898',
            'DeviceId' => '2002',
            'DeviceSer' => '',
            'UserPin' => ''
        );

        public function testAccount()
        {
            $this->config['DeviceSer'] = getenv('DEVICE');
            $this->config['UserPin'] = getenv('PIN');

            $aeon = new CodeChap\Aeon($this->config);
            $aeon[] = new CodeChap\Request\Account('Testing Pull account info');
            $r = $aeon->execute();

            $this->assertEmpty($r[0]['event']['EventCode']);
        }
    }