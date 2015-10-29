<?php

    namespace CodeChap\Aeon\Request;

    Class Electricity
    {
        /**
         * @var String The meter number to credit 
         */
        public $meterNumber = 0;

        /**
         * @var String The amount to credit
         */
        public $credit = 0;
        
        /**
         * @var String An optional reference number
         */
        public $reference = false;

        /**
         * Constructor
         *
         * @param string    The meter number to credit
         * @param numeric   The monies to credit the meter number with
         * @param string    An optional reference number of this transaction
         */
        public function __construct($meterNumber, $credit, $reference)
        {
            $this->meterNumber = $meterNumber;
            $this->credit = $credit;
            $this->reference = $reference;
        }

        /**
         * Execute the call
         */
        public function execute($config)
        {
            // Post strings
            $xml_post_string_one = '<request><EventType>ConfirmMeter</EventType><event><DeviceId>'.$config['DeviceId'].'</DeviceId><DeviceSer>'.$config['DeviceSer'].'</DeviceSer><UserPin>'.$config['UserPin'].'</UserPin><MeterNum>'.$this->meterNumber.'</MeterNum><Amount>'.$this->credit.'</Amount><Reference>'.$this->reference.'</Reference></event></request>'.PHP_EOL;
            $xml_post_string_two = '<request><SessionId></SessionId><EventType>GetVoucher</EventType><event><Type></Type><TransRef></TransRef><Reference>'.$this->reference.'</Reference></event></request>'.PHP_EOL;

            // Create step three's confirmation settings
            $xml_post_string_thr = '<request><EventType>SoldVoucher</EventType><event><DeviceId>'.$config['DeviceId'].'</DeviceId><DeviceSer>'.$config['DeviceSer'].'</DeviceSer><UserPin>'.$config['UserPin'].'</UserPin><TransRef></TransRef><Reference>'.$this->reference.'</Reference></event></request>'.PHP_EOL;

            // Create a TCP/IP socket
            $socket = new \CodeChap\Aeon\Socket($config);

            // STEP 1. AUTHENTICATE //

            // Send confirmation request
            $socket->write($xml_post_string_one);

            // Get result of send
            $result_one = $socket->get();

            // STEP 2. BUY //

            // Find session id field
            preg_match('/<SessionId>(.*)<\/SessionId>/', $result_one, $SessionId);
            // Find transfer reference field
            preg_match('/<TransRef>(.*)<\/TransRef>/', $result_one, $TransRef);
            // Swop in Session ID and transfer ref with first result
            $xml_post_string_two = preg_replace('/<SessionId><\/SessionId>/', $SessionId[0], $xml_post_string_two);
            $xml_post_string_two = preg_replace('/<TransRef><\/TransRef>/', $TransRef[0], $xml_post_string_two);

            // Send voucher request
            $socket->write($xml_post_string_two);
            // Get result of send
            $result_two = $socket->get();

            // Shutdown and close the socket
            $socket->close();

            // Done
            $finalResult = \LSS\XML2Array::createArray($result_two);
             
            // STEP 3. Confirmation (To be performed by user later so we store it in a temp file) //

            // Find transfer reference field
            preg_match('/<TransRef>(.*)<\/TransRef>/', $result_two, $TransRef);
            // Swop in transfer reference
            $xml_post_string_thr = preg_replace('/<TransRef><\/TransRef>/', $TransRef[0], $xml_post_string_thr);
            // Store this xml string in the php temp folder
            if($file = fopen($this->getTemp().(md5($this->meterNumber)).".xml", "w")){
                fwrite($file, $xml_post_string_thr);
                fclose($file);
            }
            
            // Return usefull data
            return $finalResult['response'];
        }

        /**
         * Retuens the php temp dir with a trailing slash
         */
        public function getTemp()
        {
            return rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        }
    } 