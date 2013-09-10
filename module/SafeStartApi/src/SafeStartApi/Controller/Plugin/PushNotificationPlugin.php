<?php
namespace SafeStartApi\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use ZendService\Google\Gcm\Client as GoogleGcmClient;
use ZendService\Google\Gcm\Message as GoogleGcmMessage;
use ZendService\Google\Exception\RuntimeException as GoogleGcmRuntimeException;

use ZendService\Apple\Apns\Client\Message as AppleApnsClient;
use ZendService\Apple\Apns\Message as AppleApnsMessage;
use ZendService\Apple\Apns\Message\Alert as AppleApnsMessageAlert;
use ZendService\Apple\Apns\Response\Message as AppleApnsResponse;
use ZendService\Apple\Apns\Exception\RuntimeException as AppleApnsRuntimeException;

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
        $logger = $this->getController()->getServiceLocator()->get('RequestLogger');
        $logger->debug("\n\n\n============ Android Push Notification ==================\n");
        if (!$this->googleClient) {
            $logger->debug("Failure client not initialised ");
            return false;
        }

        $message = new GoogleGcmMessage();
        $message->setRegistrationIds((array)$ids);
        $message->setData($data);
        $message->setDelayWhileIdle(false);

        try {
            $logger->debug("IDs: " . json_encode($ids));
            $response = $this->googleClient->send($message);
            $logger->debug("Success Count: " . $response->getSuccessCount());
            return $response->getSuccessCount();
        } catch (GoogleGcmRuntimeException $e) {
            $logger->debug("Exception: " . $e->getMessage());
            return false;
        }
    }

    public function ios($ids, $data)
    {
        $this->getAppleApnsClient();
        $logger = $this->getController()->getServiceLocator()->get('RequestLogger');
        $logger->debug("\n\n\n============ iOS Push Notification ==================\n");
        if (!$this->appleClient) {
            $logger->debug("Failure client not initialised ");
            return false;
        }
    }

    private function getGoogleGcmClient()
    {
        if (!$this->googleClient) {
            $this->googleClient = new GoogleGcmClient();
            $this->googleClient->getHttpClient()->setOptions(array('sslverifypeer' => false));
            $config = $this->getController()->getServiceLocator()->get('Config');
            $this->googleClient->setApiKey($config['developerApi']['google']['key']);
        }
    }

    private function getAppleApnsClient()
    {
        if (!$this->appleClient) {
            $this->appleClient = new AppleApnsClient();
            $config = $this->getController()->getServiceLocator()->get('Config');
            $this->appleClient->open(AppleApnsClient::SANDBOX_URI, $config['developerApi']['apple']['key'], $config['developerApi']['apple']['password']);
        }
    }
}