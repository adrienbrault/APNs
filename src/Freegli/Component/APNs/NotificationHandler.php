<?php

namespace Freegli\Component\APNs;

class NotificationHandler
{
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
}
