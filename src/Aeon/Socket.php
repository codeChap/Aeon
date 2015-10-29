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

    Class Socket
    {
        /**
         * Holds the open socket conenction
         */
        public static $socket = false;

        /**
         * Opens a persistant socket connection
         */
        public function __construct($config)
        {
            // Connect
            if( ! static::$socket = pfsockopen($config['ip'], $config['port'], $errno, $errstr, 30)){
                throw new Exception("Unable to connect to " . $config['ip'] . ' on port ' . $config['port']);
            }

            // FuelPHP logger
            @\Log::debug('SOCKET OPEN');
        }

        /**
         * Writes to the socket
         */
        public function write($xmlPostString)
        {
            // FuelPHP logger
            @\Log::debug('SOCKET: ' . trim($xmlPostString));

            fwrite(static::$socket, $xmlPostString);
        }

        /**
         * Gets the results of the socket
         */
        public function get()
        {
            while( $buffer = fgets(static::$socket, 1024) ){

                $response = isset($response) ? $response.$buffer : $buffer;
                
                if(preg_match('/<\/response>/', $buffer)){
                    break;
                }
            }

            // Check for complete response
            if(isset($response)){

                // FuelPHP logger
                @\Log::debug('SOCKET: ' . trim($response));

                // Check for error code
                if(preg_match('/<EventCode>(.*)<\/EventCode>/', $response, $error)){
                    if($error[1] !== '0'){
                        
                        // Find error code and text
                        preg_match('/<ErrorCode>(.*)<\/ErrorCode>/', $response, $errorCode);
                        preg_match('/<ErrorText>(.*)<\/ErrorText>/', $response, $errorText);

                        // Throw exception
                        throw new \Exception('Error code ' . $errorCode[1] . ': ' . $errorText[1]);
                    }
                    
                }

                return $response;
            }

            else{
                throw new \Exception('No data returned from server');
            }
        }

        /**
         * Closes the socket
         */
        public function close()
        {
            fclose(static::$socket);

            static::$socket = false;

            // FuelPHP logger
            @\Log::debug('SOCKET CLOSED');
        }
    }