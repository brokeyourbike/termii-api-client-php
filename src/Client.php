<?php

// Copyright (C) 2021 Ivan Stasiuk <ivan@stasi.uk>.
//
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this file,
// You can obtain one at https://mozilla.org/MPL/2.0/.

namespace BrokeYourBike\Termii;

use Psr\Http\Message\ResponseInterface;
use BrokeYourBike\Termii\Models\VerifyOneTimePasswordResponse;
use BrokeYourBike\Termii\Models\SendOneTimePasswordResponse;
use BrokeYourBike\Termii\Models\SendMessageResponse;
use BrokeYourBike\Termii\Models\FetchBalanceResponse;
use BrokeYourBike\Termii\Interfaces\OtpRequestInterface;
use BrokeYourBike\Termii\Interfaces\MessageInterface;
use BrokeYourBike\Termii\Interfaces\ApiConfigInterface;
use BrokeYourBike\ResolveUri\ResolveUriTrait;
use BrokeYourBike\HttpEnums\HttpMethodEnum;
use BrokeYourBike\HttpClient\HttpClientTrait;
use BrokeYourBike\HttpClient\HttpClientInterface;
use BrokeYourBike\HasSourceModel\SourceModelInterface;
use BrokeYourBike\HasSourceModel\HasSourceModelTrait;
use BrokeYourBike\HasSourceModel\Enums\RequestOptions;
use BrokeYourBike\Termii\Interfaces\TemplateInterface;
use BrokeYourBike\Termii\Models\SendTemplateResponse;

/**
 * @author Ivan Stasiuk <ivan@stasi.uk>
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

    public function getConfig(): ApiConfigInterface
    {
        return $this->config;
    }

    public function fetchBalanceRaw(): FetchBalanceResponse
    {
        $response = $this->performRequest(HttpMethodEnum::GET, 'get-balance', []);
        return new FetchBalanceResponse($response);
    }

    public function sendMessage(MessageInterface $message): SendMessageResponse
    {
        if ($message instanceof SourceModelInterface) {
            $this->setSourceModel($message);
        }

        $response = $this->performRequest(HttpMethodEnum::POST, 'sms/send', [
            'from' => $message->getFrom(),
            'to' => $message->getTo(),
            'sms' => $message->getMessageText(),
            'type' => $message->getMessageType()->value,
            'channel' => $message->getChannelType()->value,
        ]);
        return new SendMessageResponse($response);
    }

    public function sendTemplate(TemplateInterface $message): SendTemplateResponse
    {
        if ($message instanceof SourceModelInterface) {
            $this->setSourceModel($message);
        }

        $response = $this->performRequest(HttpMethodEnum::POST, 'send/template', [
            'device_id' => $message->getDeviceId(),
            'template_id' => $message->getTemplateId(),
            'to' => $message->getTo(),
            'data' => $message->getData(),
        ]);
    
        return new SendTemplateResponse($response);
    }

    public function sendOneTimePassword(OtpRequestInterface $otpRequest): SendOneTimePasswordResponse
    {
        if ($otpRequest instanceof SourceModelInterface) {
            $this->setSourceModel($otpRequest);
        }

        $response = $this->performRequest(HttpMethodEnum::POST, 'sms/otp/send', [
            'from' => $otpRequest->getFrom(),
            'to' => $otpRequest->getTo(),
            'channel' => $otpRequest->getChannelType()->value,
            'message_type' => $otpRequest->getMessageType()->value,
            'message_text' => $otpRequest->getMessageText(),
            'pin_type' => $otpRequest->getPinType()->value,
            'pin_attempts' => $otpRequest->getPinAttempts(),
            'pin_time_to_live' => $otpRequest->getPinTtlMinutes(),
            'pin_length' => $otpRequest->getPinLength(),
            'pin_placeholder' => $otpRequest->getPinPlaceholder(),
        ]);
        return new SendOneTimePasswordResponse($response);
    }

    public function verifyOneTimePassword(OtpRequestInterface $otpRequest, string $pin): VerifyOneTimePasswordResponse
    {
        if ($otpRequest instanceof SourceModelInterface) {
            $this->setSourceModel($otpRequest);
        }

        $response = $this->performRequest(HttpMethodEnum::POST, 'sms/otp/verify', [
            'pin_id' => $otpRequest->getPinId(),
            'pin' => $pin,
        ]);
        return new VerifyOneTimePasswordResponse($response);
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
        $data['api_key'] = $this->config->getPublicKey();

        $option = match($method) {
            HttpMethodEnum::GET => \GuzzleHttp\RequestOptions::QUERY,
            default => \GuzzleHttp\RequestOptions::JSON,
        };

        $options = [
            \GuzzleHttp\RequestOptions::HEADERS => [
                'Accept' => 'application/json',
            ],
            $option => $data,
        ];

        if ($this->getSourceModel()) {
            $options[RequestOptions::SOURCE_MODEL] = $this->getSourceModel();
        }

        $uri = (string) $this->resolveUriFor($this->config->getUrl(), $uri);
        return $this->httpClient->request($method->value, $uri, $options);
    }
}
