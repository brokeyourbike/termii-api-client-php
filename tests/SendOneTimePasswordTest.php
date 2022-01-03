<?php

// Copyright (C) 2021 Ivan Stasiuk <ivan@stasi.uk>.
//
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this file,
// You can obtain one at https://mozilla.org/MPL/2.0/.

namespace BrokeYourBike\Termii\Tests;

use Psr\Http\Message\ResponseInterface;
use BrokeYourBike\Termii\Models\SendOneTimePasswordResponse;
use BrokeYourBike\Termii\Interfaces\OtpRequestInterface;
use BrokeYourBike\Termii\Interfaces\ApiConfigInterface;
use BrokeYourBike\Termii\Enums\PinTypeEnum;
use BrokeYourBike\Termii\Enums\MessageTypeEnum;
use BrokeYourBike\Termii\Enums\ChannelTypeEnum;
use BrokeYourBike\Termii\Client;

/**
 * @author Ivan Stasiuk <ivan@stasi.uk>
 */
class SendOneTimePasswordTest extends TestCase
{
    private string $publicKey = 'some-public-key';
    private object $mockedConfig;

    protected function setUp(): void
    {
        $this->mockedConfig = $this->getMockBuilder(ApiConfigInterface::class)->getMock();
        $this->mockedConfig->method('getUrl')->willReturn('https://api.example/');
        $this->mockedConfig->method('getPublicKey')->willReturn($this->publicKey);
    }

    /** @test */
    public function it_can_prepare_request(): void
    {
        $mockedOtpRequest = $this->getMockBuilder(OtpRequestInterface::class)->getMock();
        $mockedOtpRequest->method('getFrom')->willReturn('Jane Doe');
        $mockedOtpRequest->method('getTo')->willReturn('John Doe');
        $mockedOtpRequest->method('getMessageText')->willReturn('Hello John!');
        $mockedOtpRequest->method('getMessageType')->willReturn(MessageTypeEnum::ALPHANUMERIC);
        $mockedOtpRequest->method('getChannelType')->willReturn(ChannelTypeEnum::GENERIC);
        $mockedOtpRequest->method('getPinType')->willReturn(PinTypeEnum::NUMERIC);
        $mockedOtpRequest->method('getPinAttempts')->willReturn(1);
        $mockedOtpRequest->method('getPinTtlMinutes')->willReturn(10);
        $mockedOtpRequest->method('getPinLength')->willReturn(5);
        $mockedOtpRequest->method('getPinPlaceholder')->willReturn('<12345>');

        $mockedResponse = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $mockedResponse->method('getStatusCode')->willReturn(200);
        $mockedResponse->method('getBody')
            ->willReturn('{
                "pinId": "29ae67c2-c8e1-4165-8a51-8d3d7c298081",
                "to": "2348109077743",
                "smsStatus": "Message Sent"
            }');

        /** @var \Mockery\MockInterface $mockedClient */
        $mockedClient = \Mockery::mock(\GuzzleHttp\Client::class);
        $mockedClient->shouldReceive('request')->withArgs([
            'POST',
            'https://api.example/sms/otp/send',
            [
                \GuzzleHttp\RequestOptions::HEADERS => [
                    'Accept' => 'application/json',
                ],
                \GuzzleHttp\RequestOptions::JSON => [
                    'from' => 'Jane Doe',
                    'to' => 'John Doe',
                    'channel' => ChannelTypeEnum::GENERIC->value,
                    'message_type' => MessageTypeEnum::ALPHANUMERIC->value,
                    'message_text' => 'Hello John!',
                    'pin_type' => PinTypeEnum::NUMERIC->value,
                    'pin_attempts' => 1,
                    'pin_time_to_live' => 10,
                    'pin_length' => 5,
                    'pin_placeholder' => '<12345>',
                    'api_key' => $this->publicKey,
                ],
            ],
        ])->once()->andReturn($mockedResponse);

        /**
         * @var ApiConfigInterface $mockedConfig
         * @var \GuzzleHttp\Client $mockedClient
         * */
        $api = new Client($this->mockedConfig, $mockedClient);

        /** @var OtpRequestInterface $mockedOtpRequest */
        $requestResult = $api->sendOneTimePassword($mockedOtpRequest);

        $this->assertInstanceOf(SendOneTimePasswordResponse::class, $requestResult);
    }
}
