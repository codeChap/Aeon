<?php

    /**
     * PHP library to communicate with Aeon Switch
     *
     * @author     codeChap
     * @license    MIT
     * @copyright  2015 codeChap
     * 
     */

    namespace CodeChap;

    Class Aeon implements \ArrayAccess, \IteratorAggregate, \Countable
    {
        /**
         * @var array Holds an array of requests to execute
         */
        protected $container = array();

        /**
         * @var array Holds the successfull authentication array response
         */
        protected $authentication = false;

        /**
         * @var string The session id to use for future requests after authentication
         */
        protected $SessionId = false;

        /**
         * @var string The reference to use for future requests
         */
        protected $TransRef = false;

        /**
         * @var Array Holds the latest response
         */
        protected $response = null;

        /**
         * @var array Holds default configuration options
         */
        protected $config = array(
            'ip' => false,
            'port' => false,
            'DeviceId' => false,
            'DeviceSer' => false,
            'UserPin' => false
        );

        /**
         * Be verbal
         */
        protected $verbose = false;

        /**
         * Sets up the object. If a config is given, it will be combined.
         *
         * @param   array  $config  Optional config override
         */
        public function __construct(array $config = array())
        {
            // Merge configuration settings
            $this->config = array_merge($this->config, $config);
        }

        /**
         * Tell me what you are doing
         */
        public function verbose()
        {
            $this->verbose = true;
        }

        /**
         * Execute each request, each object is responsible for its own execution
         */
        public function execute()
        {
            // Loop each request
            foreach($this->container as $object){
                
                // Get the response
                $response = $object->execute($this->config);

                // Check for an error
                if( ! isset($response['data']['ErrorCode']) ){
                    $this->response[] = $object->execute($this->config);
                }

                else{
                    throw new \Exception('ERROR (Code '.$response['data']['ErrorCode'].') ' . $response['data']['ErrorText']);
                }
            }

            // Return all responses
            return true;
        }

        /**
         * Returns the latest response
         */
        public function get()
        {
            return ! empty($this->response) ? $this->response : false;
        }

        /**
         * Strip out empty values
         */
        private function arrayFilterRecursive($input) 
        { 
            foreach($input as &$value) { 
                if(is_array($value)){
                    $value = $this->arrayFilterRecursive($value); 
                }
            }
        
            return array_filter($input); 
        } 

        /**
         * Write to the command line
         */
        function write($msg, $color = false)
        {
            // Are we printing out data to the command line
            if($this->verbose){
                
                // Switch color
                switch($color){
                    case 'white' :  $c = 0; break;
                    case 'blue' :   $c = 34; break;
                    case 'green' :  $c = 32; break;
                    default :
                    case 'cyan' :   $c = 36; break;
                    case 'red' :    $c = 31; break;
                    case 'purple' : $c = 35; break;
                    case 'brown' :  $c = 33; break;
                }

                // Set timestamp
                $now = "\033[".($color ? $c : 34)."m[".date("H:i:s")."] \033[0m";

                // Message
                $message = "\033[".($color ? $c : 36)."m".trim($msg)." \033[0m";

                // Write
                fwrite(STDOUT, $now . $message . PHP_EOL);
            }
        }

        /**
         * IteratorAggregate methods
         */
        public function getIterator()
        {
            return new \ArrayIterator($this->container);
        }

        /**
         * Countable methods
         */
        public function count()
        {
            return count($this->container);
        }

        /**
         * Array Access methods
         */
        public function offsetExists($offset)
        {
            return isset($this->container[$offset]);
        }
        public function offsetGet($offset)
        {
            return $this->container[$offset];
        }
        public function offsetSet($key, $value)
        {
            $this->container[] = $value;
        }
        public function offsetUnset($offset)
        {
            unset($this->container[$offset]);
        }
    } 