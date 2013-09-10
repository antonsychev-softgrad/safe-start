<?php
namespace SafeStartApi\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use ZendService\Google\Gcm\Client as GoogleGcmClient;
use ZendService\Google\Gcm\Message as GoogleGcmMessage;
use ZendService\Google\Exception\RuntimeException;

class PushNotificationPlugin extends AbstractPlugin
{
    private $googleClient = null;
    private $appleClient = null;

    public function pushNotification($ids, $data = array(), $device = 'android')
    {
        if ($device == 'android') {
            $this->android($ids, $data);
        } else {
            $this->ios($ids, $data);
        }
    }

    public function android($ids, $data)
    {
        $this->getGoogleGcmClient();

        $message = new GoogleGcmMessage();
        $message->setRegistrationIds((array)$ids);
        $message->setData($data);
        $message->setDelayWhileIdle(false);

        $logger = $this->getController()->getServiceLocator()->get('RequestLogger');
        try {
            $logger->debug("\n\n\n============ Android Push Notification [". $this->getController()->requestId ."]==================\n");
            $logger->debug("IDs: " . json_encode($ids));
            $response = $this->googleClient->send($message);
            $logger->debug("Success Count: " . $response->getSuccessCount());
            return $response->getSuccessCount();
        } catch (RuntimeException $e) {
            $logger->debug("Exception: " . $e->getMessage());
            return false;
        }
    }

    public function ios($ids, $data) {

    }

    private function getGoogleGcmClient()
    {
        if ($this->googleClient) {
            $this->googleClient = new GoogleGcmClient();
            $config = $this->getController()->getServiceLocator()->get('Config');
            $this->googleClient->setApiKey($config['developerApi']['google']['key']);
        }
    }
}