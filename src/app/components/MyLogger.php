<?php
// helper class for logger
namespace App\Components;

use Phalcon\Logger;
use Phalcon\Logger\Adapter\Stream;

class MyLogger
{
    /**
     * log($message,$opr)
     *function to log message in log file
     * 
     * @param [type] $message
     * @param [type] $opr
     * @return void
     */
    public function log($message, $opr)
    {
        $adapter = new Stream('../app/logs/main.log');
        $logger  = new Logger(
            'messages',
            [
                'main' => $adapter,
            ]
        );

        $logger->$opr($message);
    }
}
