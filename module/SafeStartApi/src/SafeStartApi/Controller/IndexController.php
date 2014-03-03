<?php

namespace SafeStartApi\Controller;

use SafeStartApi\Base\RestController;

class IndexController extends RestController
{
    public function indexAction()
    {
       return $this->pingAction();
    }

    public function pingAction()
    {
        $this->answer = array(
            'version' => $this->moduleConfig['params']['version'],
        );

      /*  \Resque::enqueue('default', '\SafeStartApi\Jobs\CheckListResend', array(
            'checkListId' => 19,
            'emails' => array(array(
                'email' => "ponomarenko.t@gmail.com",
                'name' => 'Artem'
            ))
        ));*/

        return $this->AnswerPlugin()->format($this->answer);
    }

    public function sendPushAction()
    {
        $device = $this->params('device');
        $deviceId = $this->params('deviceId');

        $done = 'Wrong device';

        switch ($device) {
            case 'android':
                $done = $this->pushNotificationPlugin()->android(array($deviceId), 'fuck yeah', 1);
                break;
            case 'ios':
                $done = $this->pushNotificationPlugin()->ios(array($deviceId), 'fuck yeah', 1);
                break;
        }

        return $this->AnswerPlugin()->format(array('done' => $done));
    }
}
