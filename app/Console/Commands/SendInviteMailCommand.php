<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Helpers\CsvHelper;
use App\Jobs\SendInviteMailJob;
use App\Mail\InviteMail;
use App\Services\NotifyAgentFactory;
use App\Services\SlackNotify;
use Carbon\Carbon;
use Illuminate\Console\Command;
use ParseCsv\Csv;

class SendInviteMailCommand extends Command
{
    const CONNECTION_NAME = 'beanstalkd';
    const QUEUE_NAME      = 'invite_mails';

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
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $filepath = $this->argument('filepath');

            if(is_file($filepath) && is_readable($filepath)) {
                $csv = CsvHelper::parseCsv($filepath);

                $this->sendUploadedMessage($csv);

                //get emails from csv
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
            } else {
                $this->sendUploadedMessage();
            }
        } catch (\Exception $exception) {
            $this->sendUploadedMessage();
            throw new \Exception($exception->getMessage());
        }
    }

    /**
     * Send message to admin
     *
     * @param Csv|null $csv
     * @throws \Exception
     */
    private function sendUploadedMessage(Csv $csv = null)
    {
        try {
            $slackNotify = (new NotifyAgentFactory())->factory(SlackNotify::SERVICE_NAME);
            $content = $csv ? 'File uploaded successfully' : 'File uploaded failed';

            $slackNotify->setTo(env('SLACK_CHANNEL_ID'));
            $slackNotify->setContent($content);
            $slackNotify->sendMessage();

        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }
}
