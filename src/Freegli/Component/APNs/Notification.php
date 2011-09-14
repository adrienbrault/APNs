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

    private $binaryCommand;
    private $binaryIdentifier;
    private $binaryExpiry;
    private $binaryDeviceToken;
    private $binaryPayload;

    public function  __construct()
    {
        //set default values
        $this->identifier = 1;
        $this->expiry     = new \DateTime('+12 hours');
    }

    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;

        $this->binaryIdentifier = null;
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }

    public function setExpiry(\DateTime $expiry)
    {
        $this->expiry = $expiry;

        $this->binaryExpiry = null;
    }

    public function getExpiry()
    {
        return $this->expiry;
    }

    public function setDeviceToken($deviceToken)
    {
        $this->deviceToken = $deviceToken;

        $this->binaryDeviceToken = null;
        $this->binaryDeviceTokenLength = null;
    }

    public function getDeviceToken()
    {
        return $this->deviceToken;
    }

    public function setPayload(array $payload)
    {
        $this->payload = $payload;

        $this->binaryPayload = null;
        $this->binaryPayloadLength = null;
    }

    public function getPayload()
    {
        return $this->payload;
    }

    public function getBinaryCommand()
    {
        if ($this->binaryCommand === null) {
            $this->binaryCommand = pack('C', self::COMMAND_ENHANCED_NOTIFICATION_FORMAT);
        }

        return $this->binaryCommand;
    }

    public function getBinaryDeviceToken()
    {
        if ($this->binaryDeviceToken === null) {
            $deviceToken = pack('H*', $this->getDeviceToken());
            $deviceTokenLength = pack('n', strlen($deviceToken));

            $this->binaryDeviceToken = $deviceTokenLength.$deviceToken;
        }

        return $this->binaryDeviceToken;
    }

    public function getBinaryExpiry()
    {
        if ($this->binaryExpiry === null) {
            $this->binaryExpiry = pack('N', $this->getExpiry()->format('U'));
        }

        return $this->binaryExpiry;
    }

    public function getBinaryIdentifier()
    {
        if ($this->binaryIdentifier === null) {
            $this->binaryIdentifier = pack('N', $this->getIdentifier());
        }

        return $this->binaryIdentifier;
    }

    public function getBinaryPayload()
    {
        if ($this->binaryPayload === null) {
            $payload = json_encode($this->getPayload());
            $payloadLength = pack('n', strlen($payload));

            $this->binaryPayload = $payloadLength.$payload;
        }

        return $this->binaryPayload;
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
            return
                $this->getBinaryCommand().
                $this->getBinaryIdentifier().
                $this->getBinaryExpiry().
                $this->getBinaryDeviceToken().
                $this->getBinaryPayload()
            ;
        } catch (\Exception $e) {
            throw new ConvertException('Unable to convert to binary', null, $e);
        }
    }
}
