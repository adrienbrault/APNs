<?php

namespace Freegli\Component\APNs;

use Freegli\Component\APNs\Feedback;

class FeedbackTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException Freegli\Component\APNs\Exception\LengthException
     */
    public function testLengthException()
    {
        $feedback = new Feedback('foobar');
    }

    /**
     * @expectedException Freegli\Component\APNs\Exception\ConvertException
     */
    public function testConvertException()
    {
        $this->markTestIncomplete('Don\'t know how to test it');
    }

    public function testConstruct()
    {
        $bin = file_get_contents(__DIR__.'/../../../Resources/feedback.bin');
        $feedback = new Feedback($bin);
        $this->assertEquals('1313928959', $feedback->getTimestamp()->format('U'));
        $this->assertEquals('32', $feedback->getTokenLength());
        $this->assertEquals('006eaa4dc97e68f4de8131010ce6cb726508f4bfe0a723fdc6b38d64f761d56d', $feedback->getDeviceToken());
    }
}

