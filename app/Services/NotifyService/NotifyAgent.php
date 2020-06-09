<?php


namespace App\Services\NotifyService;


abstract class NotifyAgent
{
    /**
     * Set $to param
     *
     * @param string $to
     * @return mixed
     */
    abstract public function setTo(string $to);

    /**
     * Set $content param
     *
     * @param string $content
     * @return mixed
     */
    abstract public function setContent(string $content);

    /**
     * Send message
     *
     * @return mixed
     */
    abstract public function sendMessage();
}
