<?php

namespace SafeStartApi\Jobs;

use SafeStartApi\Base\ResqueTask;

class CheckCompanyPayments extends ResqueTask
{
    const COMMAND_NAME = 'check-company-payments';

    public function perform()
    {
        $command = 'resque run ' . self::COMMAND_NAME;
        $this->executeShelCommand($command);
    }
}