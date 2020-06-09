<?php

declare(strict_types=1);

namespace App\Services;


final class NotifyAgentFactory
{
    public static function factory(string $notifyService): NotifyAgent
    {
        switch ($notifyService) {
            case SlackNotify::SERVICE_NAME:
                return new SlackNotify();
                break;
            default:
                throw new \Exception('Unknown notify service given');
        }
    }
}
