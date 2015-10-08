<?php

    /**
     * Reprint a voucher
     */

    namespace CodeChap\Request;

    Class Reprint
    {
        /**
         * @var String The meter number to check against 
         */
        public $meterNumber = false;

        /**
         * @var String The origional reference number check against 
         */
        public $reference = false;

        /**
         * @var String The origional transaction reference to check against 
         */
        public $transref = false;

        /**
         * Constructor
         *
         * @param String The meter number to check against 
         * @param String The origional reference to check against 
         * @param String The origional transaction to check against 
         */
        public function __construct($meterNumber = false, $reference = false, $transref = false)
        {
            // Set the reference number to confirm
            $this->meterNumber = $meterNumber;
            $this->reference = $reference;
            $this->transref = $transref;
        }

        /**
         * Confirmation of the request
         */
        public function execute($config)
        {
            // Post strings
            $xml_post_string_one = '<request><EventType>ConfirmMeter</EventType><event><DeviceId>'.$config['DeviceId'].'</DeviceId><DeviceSer>'.$config['DeviceSer'].'</DeviceSer><UserPin>'.$config['UserPin'].'</UserPin><MeterNum>'.$this->meterNumber.'</MeterNum><Amount>0</Amount><Reference></Reference></event></request>'.PHP_EOL;
            $xml_post_string_two = '<request><SessionId></SessionId><EventType>Reprint</EventType><event><TransRef>'.$this->transref.'</TransRef><MeterNum>'.$this->meterNumber.'</MeterNum><OrigReference>'.$this->reference.'</OrigReference></event></request>'.PHP_EOL;

            // Create a TCP/IP socket
            $socket = new \CodeChap\Socket($config);

            // STEP 1. Authenticate //

            // Send confirmation request
            $socket->write($xml_post_string_one);
            // Get result of send
            $result_one = $socket->get();

            // STEP 2. Reprint //

            // Find session id field
            preg_match('/<SessionId>(.*)<\/SessionId>/', $result_one, $SessionId);
            // Swop in Session ID
            $xml_post_string_two = preg_replace('/<SessionId><\/SessionId>/', $SessionId[0], $xml_post_string_two);
            // Send confirmation request
            $socket->write($xml_post_string_two);
            
            // Get result of send
            $result = $socket->get();

            // Done
            $finalResult = \LSS\XML2Array::createArray($result);

            // Return usefull data
            return $finalResult['response'];
        }
    } 