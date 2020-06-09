<?php

declare(strict_types=1);

namespace App\Services\NotifyService;


use Illuminate\Support\Facades\Http;

class SlackNotify extends NotifyAgent
{
    const SERVICE_NAME     = 'slack';

    const SEND_MESSAGE_URL = 'https://slack.com/api/chat.postMessage';

    public $to;
    public $content;

    /**
     * Set $to param
     *
     * @param string $to
     */
    public function setTo(string $to)
    {
        $this->to = $to;
    }

    /**
     * Set $content param
     *
     * @param $content
     */
    public function setContent(string $content)
    {
        $this->content = $content;
    }

    /**
     * Return array of basic headers
     *
     * @return array|string[]
     */
    protected function getHeaders(): array
    {
        return [
            'Content-type' => 'application/json; charset=utf-8',
            'Authorization' => 'Bearer ' . env('SLACK_TOKEN'),
        ];
    }

    /**
     * Send message to slack chat
     *
     * @return bool
     * @throws \Exception
     */
    public function sendMessage(): bool
    {
        try {
            $response = Http::withHeaders($this->getHeaders())
                ->post(self::SEND_MESSAGE_URL, [
                    'channel' => $this->to,
                    'text' => $this->content,
                ]);

            return $response->successful();
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }
}
