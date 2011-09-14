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
    CONST COMMAND_ENHANCED_NOTIFICATION_FORMAT = 1;
    
    private $identifier;
    private $expiry;
    private $deviceToken;
    private $payload;

    public function  __construct()
    {
        //set default values
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
            $command = pack('C', self::COMMAND_ENHANCED_NOTIFICATION_FORMAT);
            $identifier = pack('N', $this->identifier);
            $expiry = pack('N', $this->expiry->format('U'));
            $deviceToken = pack('H*', $this->deviceToken);
            $deviceTokenLength = pack('n', strlen($deviceToken));
            $payload = $this->formatPayload();
            $payloadLength = pack('n', strlen($payload));

            return
                $command.
                $identifier.
                $expiry.
                $deviceTokenLength.
                $deviceToken.
                $payloadLength.
                $payload
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
