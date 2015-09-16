<?php
/* Silent push ability for Zend APNS message modification */

namespace SafeStartApi\Controller\Plugin;

use ZendService\Apple\Apns\Message as AppleApnsMessage;
use Zend\Json\Encoder as JsonEncoder;

class CustomApnsMessage extends AppleApnsMessage
{
    /** @var boolean */
    protected $silentPushFlag = false;

    public function setSilentPushFlag($silentPushFlag)
    {
        $this->silentPushFlag = (bool) $silentPushFlag;
    }

    public function getSilentPushFlag()
    {
        return $this->silentPushFlag;
    }

    /** @Override */
    public function getPayload()
    {
        $message = parent::getPayload();

        $message['aps']['content-available'] = $this->silentPushFlag ? 1 : 0;

        return $message;
    }

    /** @Override: implements push command 2 format */
    public function getPayloadJson()
    {
        $payload = $this->getPayload();
        // don't escape utf8 payloads unless json_encode does not exist.
        if (defined('JSON_UNESCAPED_UNICODE')) {
            $payload = json_encode($payload, JSON_UNESCAPED_UNICODE);
        } else {
            $payload = JsonEncoder::encode($payload);
        }
        $length = strlen($payload);

        $frame =
            pack("CnH*", 1, 32, $this->token).//token
            pack("CnA*", 2, $length, $payload).//payload
            pack("CnA*", 3, 4, substr((string)$this->id, 0, 4)).//push id
            pack("CnN", 4, 4, $this->expire).//expire
            pack("CnC", 5, 1, 10);//priority
        $frameSize = strlen($frame);
        return pack("CN", 2, $frameSize).$frame;
    }
}