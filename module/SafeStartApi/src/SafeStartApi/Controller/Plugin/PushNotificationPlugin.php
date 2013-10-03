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
use SafeStartApi\Entity\Vehicle;

class PushNotificationPlugin extends AbstractPlugin
{
    private $googleClient = null;
    private $appleClient = null;

    public function pushNotification($ids, $msg = '', $badge = 0, $device = 'android')
    {
        if ($device == 'android') {
            $this->android($ids, $msg, $badge);
        } else {
            $this->ios($ids, $msg, $badge);
        }
    }

    public function android($ids, $msg = '', $badge = 0)
    {
        $this->googleClient = new GoogleGcmClient();
        $this->googleClient->getHttpClient()->setOptions(array('sslverifypeer' => false));
        $config = $this->getController()->getServiceLocator()->get('Config');
        $this->googleClient->setApiKey($config['externalApi']['google']['key']);

        $env = getenv('APP_ENV') ? getenv('APP_ENV') : 'dev';
        if ($env == 'dev') $logger = $this->getController()->getServiceLocator()->get('RequestLogger');
        if ($env == 'dev') $logger->debug("\n\n\n============ Android Push Notification ==================\n");
        if (!$this->googleClient) {
            if ($env == 'dev') $logger->debug("Failure client not initialised ");
            return false;
        }

        $message = new GoogleGcmMessage();
        $message->setRegistrationIds((array)$ids);
        $message->setData(array(
            'message' => $msg,
            'badge' => $badge,
        ));

        $message->setDelayWhileIdle(false);

        try {
            if ($env == 'dev') $logger->debug("IDs: " . json_encode($ids));
            $response = $this->googleClient->send($message);
            if ($env == 'dev') $logger->debug("Success Count: " . $response->getSuccessCount());
            return $response->getSuccessCount();
        } catch (GoogleGcmRuntimeException $e) {
            if ($env == 'dev') $logger->debug("Exception: " . $e->getMessage());
            return false;
        }
    }

    public function ios($ids, $msg = '', $badge = 0)
    {
        $this->appleClient = new AppleApnsClient();
        $config = $this->getController()->getServiceLocator()->get('Config');
        $this->appleClient->open(AppleApnsClient::SANDBOX_URI, $config['externalApi']['apple']['key'], $config['externalApi']['apple']['password']);
        $env = getenv('APP_ENV') ? getenv('APP_ENV') : 'dev';
        if ($env == 'dev') $logger = $this->getController()->getServiceLocator()->get('RequestLogger');
        if ($env == 'dev') $logger->debug("\n\n\n============ iOS Push Notification ==================\n");
        if (!$this->appleClient) {
            if ($env == 'dev') $logger->debug("Failure client not initialised");
            return false;
        }

        $done = 0;

        foreach ((array)$ids as $id) {
            if (empty($id)) continue;
            $done += $this->_ios($id, $msg, $badge);
        }

        $this->appleClient->close();

        return $done;

    }

    private function _ios($token, $msg = '', $badge = 0)
    {
        $logger = $this->getController()->getServiceLocator()->get('RequestLogger');
        $message = new AppleApnsMessage();
        $message->setId('safe-start-app');
        $message->setToken($token);
        $message->setBadge($badge);
        $message->setAlert($msg);
        $env = getenv('APP_ENV') ? getenv('APP_ENV') : 'dev';
        try {
            if ($env == 'dev') $logger->debug("Device Token: " . $token);
            $response = $this->appleClient->send($message);
        } catch (RuntimeException $e) {
            if ($env == 'dev') $logger->debug("Exception: " . $e->getMessage());
            return false;
        }

        if ($response->getCode() != AppleApnsResponse::RESULT_OK) {
            switch ($response->getCode()) {
                case AppleApnsResponse::RESULT_PROCESSING_ERROR:
                    if ($env == 'dev') $logger->debug("Error: you may want to retry");
                    break;
                case AppleApnsResponse::RESULT_MISSING_TOKEN:
                    if ($env == 'dev') $logger->debug("Error: you were missing a token");
                    break;
                case AppleApnsResponse::RESULT_MISSING_TOPIC:
                    if ($env == 'dev') $logger->debug("Error: you are missing a message id");
                    break;
                case AppleApnsResponse::RESULT_MISSING_PAYLOAD:
                    if ($env == 'dev') $logger->debug("Error: you need to send a payload");
                    break;
                case AppleApnsResponse::RESULT_INVALID_TOKEN_SIZE:
                    if ($env == 'dev') $logger->debug("Error: the token provided was not of the proper size");
                    break;
                case AppleApnsResponse::RESULT_INVALID_TOPIC_SIZE:
                    if ($env == 'dev') $logger->debug("Error: the topic was too long");
                    break;
                case AppleApnsResponse::RESULT_INVALID_PAYLOAD_SIZE:
                    if ($env == 'dev') $logger->debug("Error: the payload was too large");
                    break;
                case AppleApnsResponse::RESULT_INVALID_TOKEN:
                    if ($env == 'dev') $logger->debug("Error: the token was invalid; remove it from your system");
                    break;
                case AppleApnsResponse::RESULT_UNKNOWN_ERROR:
                    if ($env == 'dev') $logger->debug("Error: apple didn't tell us what happened");
                    break;
            }
            return false;
        } else {
            if ($env == 'dev') $logger->debug("Success: " . $response->getCode() + 1);
            return true;
        }
    }
}