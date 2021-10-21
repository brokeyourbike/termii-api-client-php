<?php

// Copyright (C) 2021 Ivan Stasiuk <brokeyourbike@gmail.com>.
//
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this file,
// You can obtain one at https://mozilla.org/MPL/2.0/.

namespace BrokeYourBike\Termii;

use Psr\Http\Message\ResponseInterface;
use BrokeYourBike\Termii\OtpRequestInterface;
use BrokeYourBike\Termii\MessageInterface;
use BrokeYourBike\Termii\ApiConfigInterface;
use BrokeYourBike\ResolveUri\ResolveUriTrait;
use BrokeYourBike\HttpEnums\HttpMethodEnum;
use BrokeYourBike\HttpClient\HttpClientTrait;
use BrokeYourBike\HttpClient\HttpClientInterface;
use BrokeYourBike\HasSourceModel\SourceModelInterface;
use BrokeYourBike\HasSourceModel\HasSourceModelTrait;
use BrokeYourBike\HasSourceModel\Enums\RequestOptions;

/**
 * @author Ivan Stasiuk <brokeyourbike@gmail.com>
 */
class Client implements HttpClientInterface
{
    use HttpClientTrait;
    use ResolveUriTrait;
    use HasSourceModelTrait;

    private ApiConfigInterface $config;

    public function __construct(ApiConfigInterface $config, \GuzzleHttp\ClientInterface $httpClient)
    {
        $this->config = $config;
        $this->httpClient = $httpClient;
    }

    public function fetchBalanceRaw(): ResponseInterface
    {
        return $this->performRequest(HttpMethodEnum::GET(), 'get-balance', []);
    }

    public function sendMessage(MessageInterface $message): ResponseInterface
    {
        if ($message instanceof SourceModelInterface) {
            $this->setSourceModel($message);
        }

        return $this->performRequest(HttpMethodEnum::POST(), 'sms/send', [
            'from' => $message->getFrom(),
            'to' => $message->getTo(),
            'sms' => $message->getMessageText(),
            'type' => $message->getMessageType(),
            'channel' => $message->getChannelType(),
        ]);
    }

    public function sendOneTimePassword(OtpRequestInterface $otpRequest): ResponseInterface
    {
        if ($otpRequest instanceof SourceModelInterface) {
            $this->setSourceModel($otpRequest);
        }

        return $this->performRequest(HttpMethodEnum::POST(), 'sms/otp/send', [
            'from' => $otpRequest->getFrom(),
            'to' => $otpRequest->getTo(),
            'channel' => $otpRequest->getChannelType(),
            'message_type' => $otpRequest->getMessageType(),
            'message_text' => $otpRequest->getMessageText(),
            'pin_type' => $otpRequest->getPinType(),
            'pin_attempts' => $otpRequest->getPinAttempts(),
            'pin_time_to_live' => $otpRequest->getPinTtlMinutes(),
            'pin_length' => $otpRequest->getPinLength(),
            'pin_placeholder' => $otpRequest->getPinPlaceholder(),
        ]);
    }

    public function verifyOneTimePassword(OtpRequestInterface $otpRequest, string $pin): ResponseInterface
    {
        if ($otpRequest instanceof SourceModelInterface) {
            $this->setSourceModel($otpRequest);
        }

        return $this->performRequest(HttpMethodEnum::POST(), 'sms/otp/verify', [
            'pin_id' => $otpRequest->getPinId(),
            'pin' => $pin,
        ]);
    }

    /**
     * @param HttpMethodEnum $method
     * @param string $uri
     * @param array<mixed> $data
     * @return ResponseInterface
     *
     * @throws \Exception
     */
    private function performRequest(HttpMethodEnum $method, string $uri, array $data): ResponseInterface
    {
        $options = [
            \GuzzleHttp\RequestOptions::HTTP_ERRORS => false,
            \GuzzleHttp\RequestOptions::HEADERS => [
                'Accept' => 'application/json',
            ],
        ];

        $data['api_key'] = $this->config->getPublicKey();

        if (HttpMethodEnum::GET()->equals($method)) {
            $options[\GuzzleHttp\RequestOptions::QUERY] = $data;
        } elseif (HttpMethodEnum::POST()->equals($method)) {
            $options[\GuzzleHttp\RequestOptions::JSON] = $data;
        }

        if ($this->getSourceModel()) {
            $options[RequestOptions::SOURCE_MODEL] = $this->getSourceModel();
        }

        $uri = (string) $this->resolveUriFor($this->config->getUrl(), $uri);
        return $this->httpClient->request((string) $method, $uri, $options);
    }
}
