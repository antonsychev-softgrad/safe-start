<?php

namespace SafeStartApi\Jobs;

use SafeStartApi\Base\ResqueTask;

class CheckListResend extends ResqueTask
{
    const COMMAND_NAME = 'checklist-resend';

    public function perform()
    {
        $emails = array();
        foreach((array)$this->args['emails'] as $email) {
            $emails[] = $email['email'] .':'. (isset($email['name']) ? $email['name'] : 'friend');
        }
        $command = 'resque run ' . self::COMMAND_NAME  .' --checkListId='. $this->args['checkListId'] . ' --emails='.implode(',', $emails);
        $this->executeShelCommand($command);
    }
}