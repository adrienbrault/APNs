<?php

namespace Freegli\Component\APNs;

use Freegli\Component\APNs\Exception\LengthException;

/**
 * Feedback tuple.
 *
 * @link http://developer.apple.com/library/ios/#documentation/NetworkingInternet/Conceptual/RemoteNotificationsPG/CommunicatingWIthAPS/CommunicatingWIthAPS.html
 */
class Feedback
{
    const LENGTH = 38; //4+2+32

    private $timestamp;
    private $tokenLength;
    private $deviceToken;

    /**
     * Create Feedback from a binary string.
     *
     * @param string $binaryString
     *
     * @throws LengthException|ConvertException
     */
    public function __construct($binaryString)
    {
        if (strlen($binaryString) != self::LENGTH) {
            throw new LengthException();
        }
        try {
            list ($timestamp, $this->tokenLength, $this->deviceToken) = unpack('NnH*', $binaryString);

            $this->timestamp = new \DateTime($timestamp);
        } catch (\Exception $e) {
            throw new ConvertException('Unable to convert binary string to '.__CLASS__, null, $e);
        }
    }

    /**
     * @return \DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    public function getTokenLength()
    {
        return $this->tokenLength;
    }

    public function getDeviceToken()
    {
        return $this->deviceToken;
    }
}
