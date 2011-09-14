<?php

namespace Freegli\Component\APNs;

class NotificationHandler
{
    static $errorCode = array(
        0   => 'No errors encountered',
        1   => 'Processing error',
        2   => 'Missing device token',
        3   => 'Missing topic',
        4   => 'Missing payload',
        5   => 'Invalid token size',
        6   => 'Invalid topic size',
        7   => 'Invalid payload size',
        8   => 'Invalid token',
        255 => 'None (unknown)',
    );

    private $connection;

    public function __destruct()
    {
        fclose($this->connection);
    }

    public function setConnection($connection)
    {
        $this->connection = $connection;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function send(Notification $pushNotification)
    {
        $binaryPushNotification = $pushNotification->toBinary();

        $written = fwrite($this->connection, $binaryPushNotification);

        return $written == strlen($binaryPushNotification);
    }

    /**
     * With the enhanced format, APNs returns a packet that associates an error code with the notification identifier.
     * If APNs returns an error, all notifications sent after the one that caused the error will be ignored.
     *
     * You should wait (probably using usleep()) after sending all notification and then call readError().
     *
     * @link http://developer.apple.com/library/ios/documentation/NetworkingInternet/Conceptual/RemoteNotificationsPG/CommunicatingWIthAPS/CommunicatingWIthAPS.html#//apple_ref/doc/uid/TP40008194-CH101-SW4
     */
    public function readError()
    {
        $read   = array($this->connection);
        $write  = $except = null;
        $changedStreamsCount = stream_select($read, $write, $except, 0);

        if (false === $changedStreamsCount) {
            // What should we do here ?
        } elseif ($changedStreamsCount > 0) {
            if (($binaryPushFeedback = fread($this->connection, 6))) {
                $pushFeedback = unpack('Ccommand/CstatusCode/Nidentifier', $binaryPushFeedback);
                $pushFeedback['description'] = self::$errorCode[$pushFeedback['statusCode']];

                return $pushFeedback;
            } else {
                // Could not read from stream.
                // But the stream read status changed so apple won't listen to our next notifications.
            }
        }
    }
}
