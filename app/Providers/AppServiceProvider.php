<?php

namespace App\Providers;

use App\Services\NotifyAgentFactory;
use App\Services\SlackNotify;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;
use Illuminate\Queue\Events\JobFailed;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Queue::after(function (JobProcessed $event) {
            $content = 'Letter send successfully';
            $this->notifyAgent($content);
        });
        Queue::failing(function (JobFailed $event) {
            $content = 'Letter sending failed';
            $this->notifyAgent($content);
        });
    }

    /**
     * Send message to admin
     *
     * @param string $content
     * @throws \Exception
     */
    private function notifyAgent(string $content)
    {
        $slackNotify = (new NotifyAgentFactory())->factory(SlackNotify::SERVICE_NAME);

        $slackNotify->setTo(env('SLACK_CHANNEL_ID'));
        $slackNotify->setContent($content);
        $slackNotify->sendMessage();
    }
}
