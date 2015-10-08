<?php

    class flowTest extends PHPUnit_Framework_TestCase
    {
        public $config = array(
            'ip' => '196.38.158.118',
            'port' => '7898',
            'DeviceId' => '2002',
            'DeviceSer' => '',
            'UserPin' => ''
        );

        public function testPurchase()
        {
            $this->config['DeviceSer'] = getenv('DEVICE');
            $this->config['UserPin'] = getenv('PIN');

            $aeon = new CodeChap\Aeon($this->config);
            $aeon[] = new CodeChap\Request\Electricity('01060029501', '120', 'Testing Normal Vendor request');
            $aeon->execute();

            $this->assertEmpty($aeon->get()[0]['event']['EventCode']);
        }

        public function testConfirm()
        {
            $this->config['DeviceSer'] = getenv('DEVICE');
            $this->config['UserPin'] = getenv('PIN');

            $aeon = new CodeChap\Aeon($this->config);
            $aeon[] = new CodeChap\Request\Confirm('01060029501');
            $aeon->execute();

            $this->assertEmpty($aeon->get()[0]['event']['EventCode']);
        }

        public function testReprint()
        {
            $this->config['DeviceSer'] = getenv('DEVICE');
            $this->config['UserPin'] = getenv('PIN');

            $aeon = new CodeChap\Aeon($this->config);
            $aeon[] = new CodeChap\Request\Reprint('01060029501', 'Testing reprint');
            $aeon->execute();

            $this->assertEmpty($aeon->get()[0]['event']['EventCode']);
        }

        public function testVoucher()
        {
            $this->config['DeviceSer'] = getenv('DEVICE');
            $this->config['UserPin'] = getenv('PIN');            

            $aeon = new CodeChap\Aeon($this->config);
            $aeon[] = new CodeChap\Request\Reprint('01060029501', 'Testing reprint and voucher generation');
            $aeon->execute();

            $voucher = new CodeChap\Voucher($aeon->get());
            $voucher->html();
        }
    }