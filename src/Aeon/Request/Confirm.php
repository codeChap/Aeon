<?php

    /**
     * Confirm a sale after an Electricity request 
     */

    namespace CodeChap\Aeon\Request;

    Class Confirm
    {
        /**
         * @var String Meter number previously used
         */
        public $meternumber = false;

        public function __construct( $meternumber )
        {
            // Set the reference number to confirm
            $this->meternumber = $meternumber;
        }

        /**
         * Confirmation of the request
         */
        public function execute($config)
        {
            // Set path to temp file
            $filePath = $this->getTemp().(md5($this->meternumber)).".xml";

            // Open it
            if($xml = file_get_contents($filePath)){

                 // Create a TCP/IP socket
                $socket = new \CodeChap\Aeon\Socket($config);

                // Send confirmation request
                $socket->write($xml);
                
                // Get result of send
                $result = $socket->get();

                // Done
                $finalResult = \LSS\XML2Array::createArray($result);

                // Return usefull data
                return $finalResult['response'];
            }
        }

        /**
         * Retuens the php temp dir with a trailing slash
         */
        public function getTemp()
        {
            return rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        }
    } 