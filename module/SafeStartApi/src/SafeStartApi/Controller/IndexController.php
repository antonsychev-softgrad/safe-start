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

    public function versionAction()
    {
        $content = @file_get_contents(dirname($_SERVER['SCRIPT_FILENAME']) . '/version');
        if($content) {
            $this->answer = json_decode($content, true);
        } else {

            $https = !empty($_SERVER['HTTPS']) && strcasecmp($_SERVER['HTTPS'], 'on') === 0 ||
                !empty($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
                strcasecmp($_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') === 0;
            $url =
                ($https ? 'https://' : 'http://').
                (!empty($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'].'@' : '').
                (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ($_SERVER['SERVER_NAME'].
                    ($https && $_SERVER['SERVER_PORT'] === 443 ||
                    $_SERVER['SERVER_PORT'] === 80 ? '' : ':'.$_SERVER['SERVER_PORT'])));

            $this->answer = array(
                array(
                  'ver' => 'v2',
                  'latest' => true,
                  'url' => $url),
            );
        }

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
                $done = $this->pushNotificationPlugin()->ios(array($deviceId, "42076023 10fcc5c9 91cfdc0f 57f2bd67 06de26e6 6d02728a 90b587b4 9cba720f"), 'fuck yeah', 1);
                break;
        }

        return $this->AnswerPlugin()->format(array('done' => $done));
    }
}
