<?php

namespace SafeStartApi\Jobs;

use SafeStartApi\Base\ResqueTask;

class PingEmail extends ResqueTask
{
    const COMMAND_NAME = 'ping-email';

    public function perform()
    {
        $command = 'resque run ' . self::COMMAND_NAME;
        $this->executeShelCommand($command);
    }
}