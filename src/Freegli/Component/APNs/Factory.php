<?php

namespace Freegli\Component\APNs;

use Freegli\Component\APNs\Exception\ConnectionException;

class Factory
{
    const FEEDBACK_PRODUCTION_HOST = 'feedback.push.apple.com';
    const FEEDBACK_SANDBOX_HOST    = 'feedback.sandbox.push.apple.com';
    const FEEDBACK_PORT            = '2196';

    const NOTIFICATION_PRODUCTION_HOST = 'gateway.push.apple.com';
    const NOTIFICATION_SANDBOX_HOST    = 'gateway.sandbox.push.apple.com';
    const NOTIFICATION_PORT            = '2195';

    private $certificatPath;
    private $certificatPassPhrase;
    private $sandbox;

    public function __construct($certificatPath, $certificatPassPhrase, $sandbox = false)
    {
        $this->certificatPath       = $certificatPath;
        $this->certificatPassPhrase = $certificatPassPhrase;
        $this->sandbox              = $sandbox;
    }

    /**
     * Create an instance of PushNotificationHandler according to factory parameters.
     *
     * @return PushNotification
     */
    public function createPushNotificationHandler()
    {
        $url = sprintf('ssl://%s:%s',
            $this->sandbox ? self::NOTIFICATION_SANDBOX_HOST : self::NOTIFICATION_PRODUCTION_HOST,
            self::NOTIFICATION_PORT
        );

        $nh = new NotificationHandler();
        $nh->setConnection($this->getConnection($url));

        return $nh;
    }

    /**
     * Create an instance of FeedbackHandler according to factory parameters.
     *
     * @return FeedbackConverter
     */
    public function createFeedbackHandler()
    {
        $url = sprintf('ssl://%s:%s',
            $this->sandbox ? self::FEEDBACK_SANDBOX_HOST : self::FEEDBACK_PRODUCTION_HOST,
            self::FEEDBACK_PORT
        );

        $fh = new FeedbackHandler();
        $fh->setConnection($this->getConnection($url));

        return $fh;
    }

    /**
     * Open stream connection to APNs.
     *
     * @param string $url Service URL to connect
     *
     * @return resource
     *
     * @throws ConnectionException
     */
    private function getConnection($url)
    {
        $streamContext = stream_context_create();
        stream_context_set_option($streamContext, 'ssl', 'local_cert', $this->certificatPath);
        if ($this->certificatPassPhrase) {
            stream_context_set_option($streamContext, 'ssl', 'passphrase', $this->certificatPassPhrase);
        }

        $connection = stream_socket_client($url, $errno, $errstr, 60, STREAM_CLIENT_CONNECT, $streamContext);
        if ($connection === false) {
            throw new ConnectionException($errstr, $errno);
        }

        return $connection;
    }
}
