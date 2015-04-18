<?php
include_once 'Logger.php';

class DataManager
{

    //DB mysqli object
    protected $mysqli;

    //Logger
    private $logger;

    // A private constructor; prevents direct creation of object
    public function __construct($mysqli)
    {
		$this->logger = Logger::getLogger();
		$this->mysqli = $mysqli;
    }

    /**
	* Utility function to throw an exception if an error occurs
	* while running a mysql command.
	*/
	protected function throwDBExceptionOnError($errno,$errmsg) {
        $this->logger->log($errmsg,ERROR_LOG_TYPE);
        throw new Exception($errmsg,$errno);
    }


    protected function throwCustomExceptionOnError($errno = 0 ,$errmsg) {
        $this->logger->log($errmsg,ERROR_LOG_TYPE);
        throw new Exception($errmsg,$errno);
    }



}

?>
