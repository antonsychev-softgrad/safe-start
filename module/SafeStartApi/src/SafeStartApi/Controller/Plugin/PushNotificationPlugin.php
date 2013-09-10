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
        $this->googleClient = new GoogleGcmClient();
        $this->googleClient->getHttpClient()->setOptions(array('sslverifypeer' => false));
        $config = $this->getController()->getServiceLocator()->get('Config');
        $this->googleClient->setApiKey($config['developerApi']['google']['key']);

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
        $this->appleClient = new AppleApnsClient();
        $config = $this->getController()->getServiceLocator()->get('Config');
        $this->appleClient->open(AppleApnsClient::SANDBOX_URI, $config['developerApi']['apple']['key'], $config['developerApi']['apple']['password']);
        $logger = $this->getController()->getServiceLocator()->get('RequestLogger');
        $logger->debug("\n\n\n============ iOS Push Notification ==================\n");
        if (!$this->appleClient) {
            $logger->debug("Failure client not initialised");
            return false;
        }

        $done = 0;

        foreach ((array)$ids as $id) {
            $done += $this->_ios($id, $data);
        }

        $this->appleClient->close();

        return $done;

    }

    private function _ios($token, $data)
    {
        $logger = $this->getController()->getServiceLocator()->get('RequestLogger');
        $message = new AppleApnsMessage();
        $message->setId('safe-start-app');
        $message->setToken($token);
        $message->setBadge(1);
        $message->setAlert('Update vehicle info');
        try {
            $logger->debug("Device Token: " . $token);
            $response = $this->appleClient->send($message);
        } catch (RuntimeException $e) {
            $logger->debug("Exception: " . $e->getMessage());
            return false;
        }

        if ($response->getCode() != AppleApnsResponse::RESULT_OK) {
            switch ($response->getCode()) {
                case AppleApnsResponse::RESULT_PROCESSING_ERROR:
                    $logger->debug("Error: you may want to retry");
                    break;
                case AppleApnsResponse::RESULT_MISSING_TOKEN:
                    $logger->debug("Error: you were missing a token");
                    break;
                case AppleApnsResponse::RESULT_MISSING_TOPIC:
                    $logger->debug("Error: you are missing a message id");
                    break;
                case AppleApnsResponse::RESULT_MISSING_PAYLOAD:
                    $logger->debug("Error: you need to send a payload");
                    break;
                case AppleApnsResponse::RESULT_INVALID_TOKEN_SIZE:
                    $logger->debug("Error: the token provided was not of the proper size");
                    break;
                case AppleApnsResponse::RESULT_INVALID_TOPIC_SIZE:
                    $logger->debug("Error: the topic was too long");
                    break;
                case AppleApnsResponse::RESULT_INVALID_PAYLOAD_SIZE:
                    $logger->debug("Error: the payload was too large");
                    break;
                case AppleApnsResponse::RESULT_INVALID_TOKEN:
                    $logger->debug("Error: the token was invalid; remove it from your system");
                    break;
                case AppleApnsResponse::RESULT_UNKNOWN_ERROR:
                    $logger->debug("Error: apple didn't tell us what happened");
                    break;
            }
            return false;
        } else {
            $logger->debug("Success: " . $response->getCode());
            return true;
        }
    }
}