<?php

namespace SafeStartApi\Base;

use Zend\Log\Logger as ZendLogger;

class Logger extends ZendLogger
{

    public function log($priority, $message, $extra = array())
    {
        if (!APP_LOGS) return;
        parent::log($priority, $message, $extra);
    }

}
