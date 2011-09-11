<?php

namespace Freegli\Component\APNs;

use Freegli\Component\APNs\Notification;

class NotificationTest extends \PHPUnit_Framework_TestCase
{
    public function testToBinary()
    {
        $notification = new Notification();
        $notification->setIdentifier(2);
        $notification->setExpiry(new \DateTime('2010-01-13 00:00:00'));
        $notification->setDeviceToken('4333526ff2e8b19730cab08c7a14f8b59e80aed473e06d6a2faa95bd82c3556e');
        $notification->setPayload(array(
            'aps' => array(
                'alert' => 'Alert!'
            )
        ));
        $this->assertStringEndsWith('{"aps":{"alert":"Alert!"}}', $notification->toBinary());
    }
}
