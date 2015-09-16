<?php

namespace SafeStartApi\Jobs;

use SafeStartApi\Base\ResqueTask;

class SyncDbPayments extends ResqueTask
{
    const COMMAND_NAME = 'sync-db-payments';

    public function perform()
    {
        $command = 'resque run ' . self::COMMAND_NAME;
        $this->executeShelCommand($command);
    }
}