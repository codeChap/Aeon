<?php

    /**
     * Reprint a voucher
     */

    namespace CodeChap\Request;

    Class Account
    {
        /**
         * @var String An optional reference to set with this request
         */
        public $reference = false;

        /**
         * Constructor
         *
         * @param String An optional reference to set with this request 
         */
        public function __construct($reference = false)
        {
            // Set the reference number to confirm
            $this->reference = $reference;
        }

        /**
         * Confirmation of the request
         */
        public function execute($config)
        {
            // Post strings
            $xml_post_string_one = '<request><EventType>Authentication</EventType><event><DeviceId>'.$config['DeviceId'].'</DeviceId><DeviceSer>'.$config['DeviceSer'].'</DeviceSer><UserPin>'.$config['UserPin'].'</UserPin><TransType>AccountInfo</TransType><Reference>'.$this->reference.'</Reference></event></request>'.PHP_EOL;

            // Create a TCP/IP socket
            $socket = new \CodeChap\Socket($config);

            // Send confirmation request
            $socket->write($xml_post_string_one);
            
            // Get result of send
            $result = $socket->get();

            // Done
            $finalResult = \LSS\XML2Array::createArray($result);

            // Return usefull data
            return $finalResult['response'];
        }
    } 