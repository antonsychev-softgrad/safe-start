<?php

namespace SafeStartApi\Jobs;

use SafeStartApi\Base\ResqueTask;

class NewEmailCheckListUploaded extends ResqueTask
{
    const COMMAND_NAME = 'new-email-checklist-uploaded';

    public function perform()
    {
        $emails = array();
        foreach((array)$this->args['checkListId'] as $email) {
            $emails[] = $email['email'] .':'. (isset($email['name']) ? $email['name'] : 'friend');
        }
        $command = 'resque run ' . self::COMMAND_NAME  .' --checkListId='. $this->args['checkListId'] . ' --emails='.implode(',', $emails);
        $this->executeShelCommand($command);
    }
}