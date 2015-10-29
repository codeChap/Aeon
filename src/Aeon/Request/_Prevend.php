<?php

    namespace CodeChap\Aeon\Request;

    Class Prevend
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

            // Create a TCP/IP socket
            $socket = new \CodeChap\Aeon\Socket($config);

            // Send confirmation request
            $socket->write($xml_post_string_one);
            
            // Get result of send
            $result = $socket->get();

            //Close the socket
            $socket->close();

            // Done
            $finalResult = \LSS\XML2Array::createArray($result);
            
            // Return usefull data
            return $finalResult['response'];
        }
    } 