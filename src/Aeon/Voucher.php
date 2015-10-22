<?php

    /**
     * PHP library to communicate with Aeon Switch
     *
     * @author     codeChap
     * @license    MIT
     * @copyright  2015 codeChap
     * 
     */

    namespace CodeChap\Aeon;

    use Fuel\Common\Arr;

    Class Voucher
    {
        /**
         * @var Arary The data result of a successfull electricity purchase or reprint
         */
        public $data = false;

        /**
         * Produces a voucher to be displayed to the end user
         *
         * @param Array The result of a successfull electricity purchase or reprint 
         */
        public function __construct($result)
        {
            $this->data = $result[0]['data'];
        }

        public function html()
        {
            // Pull Meter Details
            if($meter = Arr::get($this->data, 'Meter')){
                foreach($meter as $key => $value){
                    $meterDetails[] = '<div class="aeon '.strtolower($key).'">'.$value.'</div>';
                }
                $return[] = implode($meterDetails, PHP_EOL) . PHP_EOL;
            }

            // Check for key change
            if( $bss = Arr::get($this->data, 'BSSTTokens.BSSTToken')){
                foreach($bss as $token){
                    $bssTokens[] = '<div class="aeon bss '.strtolower($key).'">'.$value.'</div>';
                }
                $return[] = implode($bssTokens, PHP_EOL) . PHP_EOL;
            }

            // Other info
            $otherInfo['utility'] = Arr::get($this->data, 'Utility');
            $otherInfo['custMsg'] = Arr::get($this->data, 'CustMsg');
            $otherInfo['vatNo'] = Arr::get($this->data, 'VatNo');
            $otherInfo['transRef'] = Arr::get($this->data, 'TransRef');
            $otherInfo['tariffName'] = Arr::get($this->data, 'TariffName');
            $otherInfo['reference'] = Arr::get($this->data, 'Reference');
            $otherInfo['reprint'] = Arr::get($this->data, 'Reprint');
            foreach(array_filter($otherInfo) as $key => $value){
                $info[] = '<div class="aeon info '.strtolower($key).'">'.$value.'</div>';
            }
            $return[] = implode($info, PHP_EOL) . PHP_EOL;
            
            // Return all;
            return implode($return);
        }

        public function __toString()
        {
            return $this->html();
        }
    }