<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Helpers\CsvHelper;
use App\Jobs\SendInviteMailJob;
use App\Mail\InviteMail;
use App\Services\NotifyService\NotifyAgentFactory;
use App\Services\NotifyService\SlackNotify;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendInviteMailCommand extends Command
{
    const CONNECTION_NAME    = 'beanstalkd';
    const QUEUE_NAME         = 'invite_mails';
    const UPLOAD_SUCCESS_MSG = 'File uploaded successfully';
    const UPLOAD_FAILED_MSG  = 'File uploaded failed';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:invite {filepath}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send invite letters to customers';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @throws \Exception
     */
    public function handle()
    {
        try {
            $filepath = $this->argument('filepath');

            if(is_file($filepath) && is_readable($filepath)) {
                $csv = CsvHelper::parseCsv($filepath);

                $message = $csv ? self::UPLOAD_SUCCESS_MSG : self::UPLOAD_FAILED_MSG;
                $this->sendUploadedMessage($message);

                if($csv) {
                    $departureDate = Carbon::today()->addWeek()->format('Y-m-d');
                    $travelersForInviting = CsvHelper::searchByField($csv, 'departure_date', $departureDate);
                    $emails = array_column($travelersForInviting, 'traveler_email');

                    // add mails to queue
                    $inviteMail = new InviteMail();
                    foreach ($emails as $email) {
                        SendInviteMailJob::dispatch($inviteMail, $email)
                            ->onConnection(self::CONNECTION_NAME)
                            ->onQueue(self::QUEUE_NAME);
                    }
                }
            } else {
                $this->sendUploadedMessage(self::UPLOAD_FAILED_MSG);
            }
        } catch (\Exception $exception) {
            $this->sendUploadedMessage(self::UPLOAD_FAILED_MSG);
            throw new \Exception($exception->getMessage());
        }
    }

    /**
     * Send message to admin
     *
     * @param string $message
     * @throws \Exception
     */
    private function sendUploadedMessage(string $message)
    {
        try {
            $slackNotify = (new NotifyAgentFactory())->factory(SlackNotify::SERVICE_NAME);

            $slackNotify->setTo(env('SLACK_CHANNEL_ID'));
            $slackNotify->setContent($message);
            $slackNotify->sendMessage();
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }
}
