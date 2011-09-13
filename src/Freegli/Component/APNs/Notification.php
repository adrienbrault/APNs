<?php

namespace Freegli\Component\APNs;

use Freegli\Component\APNs\Exception\ConvertException;

/**
 * Based on enhanced notification format.
 *
 * @link http://developer.apple.com/library/ios/#documentation/NetworkingInternet/Conceptual/RemoteNotificationsPG/CommunicatingWIthAPS/CommunicatingWIthAPS.html
 */
class Notification
{
    private $command;
    private $identifier;
    private $expiry;
    private $tokenLength;
    private $deviceToken;
    private $payloadLength;
    private $payload;

    public function  __construct()
    {
        //set default values
        $this->command    = 1;
        $this->identifier = 1;
        $this->expiry     = new \DateTime('+12 hours');
    }

    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    public function setExpiry(\DateTime $expiry)
    {
        $this->expiry = $expiry;
    }

    public function setDeviceToken($deviceToken)
    {
        $this->deviceToken = $deviceToken;
    }

    public function setPayload(array $payload)
    {
        $this->payload = $payload;
    }

    /**
     * Format push notification to binary.
     *
     * @return string Binary push notification
     *
     * @throws ConvertException
     */
    public function toBinary()
    {
        try {
            $payload = $this->formatPayload();

            return
                pack('CNNnH*',
                    $this->command,
                    $this->identifier,
                    $this->expiry->format('U'),
                    strlen($this->deviceToken) / 2,
                    $this->deviceToken
                )
                .pack('n', strlen($payload))
                .$payload
            ;
        } catch (\Exception $e) {
            throw new ConvertException('Unable to convert to binary', null, $e);
        }
    }

    /**
     * JSON encodes payload.
     *
     * @return string
     */
    private function formatPayload()
    {
        //TODO handle error
        return json_encode($this->payload);
    }
}
