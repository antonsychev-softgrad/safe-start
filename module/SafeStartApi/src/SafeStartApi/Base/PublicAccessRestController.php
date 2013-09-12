<?php
namespace SafeStartApi\Base;

use SafeStartApi\Base\Exception\Rest401;

class PublicAccessRestController extends RestController
{
    public function onDispatchEvent()
    {
        parent::onDispatchEvent();

        $device = $this->params('device');
        $deviceId = $this->params('deviceId');

        if(is_null($device) || is_null($deviceId)) {
            throw new Rest401('Access denied');
        }

        switch($device) {
            case 'android':
                if(!$this->PushNotificationPlugin()->android(array($deviceId))) throw new Rest401('Access denied');
                break;
            case 'ios':
                if(!$this->PushNotificationPlugin()->ios(array($deviceId))) throw new Rest401('Access denied');
                break;
            default:
                throw new Rest401('Access denied');
                break;
        }
    }
}
