<?php declare(strict_types=1);

namespace Mail\SDK;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use OAuth2ClientCredentials\OAuthClient;

class MailClient
{
    /**
     * @var OAuthClient
     */
    private $oauthClient;

    /**
     * @var string
     */
    private $apiUrl;

    /**
     * @param string $apiUrl
     */
    public function __construct($apiUrl)
    {
        $this->oauthClient = new OAuthClient(
            config('mail.oauth.url'),
            config('mail.oauth.client_id'),
            config('mail.oauth.client_secret')
        );
        $this->apiUrl = $apiUrl;
    }

    /**
     * @param callable $handler
     * @return Response
     * @throws \Illuminate\Http\Client\RequestException
     */
    private function request($handler)
    {
        $request = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->oauthClient->getAccessToken(),
        ])
            ->withoutVerifying();

        $response = $handler($request);

        if ($response->status() == 401) {
            $this->oauthClient->getAccessToken(true);
        }

        return $response;
    }

    /**
     * @param string $route
     * @return string
     */
    private function getUrl(string $route)
    {
        return $this->apiUrl . '/api/client/v1' . $route;
    }

    /**
     * @param string $sender
     * @param string $senderName
     * @param string $receiver
     * @param string $subject
     * @param string $content
     * @param string $contentType
     * @return bool
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function send($sender, $senderName, $receiver, $subject, $content, $contentType)
    {
        $params = [
            "sender" => $sender,
            "sender_name" => $senderName,
            "receiver" => $receiver,
            "subject" => $subject,
            "content" => $content,
            "content_type" => $contentType,
        ];

        return $this->request(function (PendingRequest $request) use ($params) {
            return $request->asJson()
                ->post($this->getUrl('/mail/send'), $params);
        })
            ->successful();
    }
}
