<?php

// Copyright (C) 2021 Ivan Stasiuk <brokeyourbike@gmail.com>.
//
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this file,
// You can obtain one at https://mozilla.org/MPL/2.0/.

namespace BrokeYourBike\Termii\Tests;

use Psr\Http\Message\ResponseInterface;
use PHPUnit\Framework\TestCase;
use BrokeYourBike\Termii\OtpRequestInterface;
use BrokeYourBike\Termii\Enums\PinType;
use BrokeYourBike\Termii\Enums\MessageType;
use BrokeYourBike\Termii\Enums\ChannelType;
use BrokeYourBike\Termii\Client;
use BrokeYourBike\Termii\ApiConfigInterface;
use BrokeYourBike\HasSourceModel\Enums\RequestOptions;

/**
 * @author Ivan Stasiuk <brokeyourbike@gmail.com>
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
        $mockedOtpRequest->method('getMessageType')->willReturn(MessageType::ALPHANUMERIC());
        $mockedOtpRequest->method('getChannelType')->willReturn(ChannelType::GENERIC());
        $mockedOtpRequest->method('getPinType')->willReturn(PinType::NUMERIC());
        $mockedOtpRequest->method('getPinAttempts')->willReturn(1);
        $mockedOtpRequest->method('getPinTtlMinutes')->willReturn(10);
        $mockedOtpRequest->method('getPinLength')->willReturn(5);
        $mockedOtpRequest->method('getPinPlaceholder')->willReturn('<12345>');

        /** @var \Mockery\MockInterface $mockedClient */
        $mockedClient = \Mockery::mock(\GuzzleHttp\Client::class);
        $mockedClient->shouldReceive('request')->withArgs([
            'POST',
            'https://api.example/sms/otp/send',
            [
                \GuzzleHttp\RequestOptions::HTTP_ERRORS => false,
                \GuzzleHttp\RequestOptions::HEADERS => [
                    'Accept' => 'application/json',
                ],
                \GuzzleHttp\RequestOptions::JSON => [
                    'from' => 'Jane Doe',
                    'to' => 'John Doe',
                    'channel' => ChannelType::GENERIC(),
                    'message_type' => MessageType::ALPHANUMERIC(),
                    'message_text' => 'Hello John!',
                    'pin_type' => PinType::NUMERIC(),
                    'pin_attempts' => 1,
                    'pin_time_to_live' => 10,
                    'pin_length' => 5,
                    'pin_placeholder' => '<12345>',
                    'api_key' => $this->publicKey,
                ],
            ],
        ])->once();

        /**
         * @var ApiConfigInterface $mockedConfig
         * @var \GuzzleHttp\Client $mockedClient
         * */
        $api = new Client($this->mockedConfig, $mockedClient);

        /** @var OtpRequestInterface $mockedOtpRequest */
        $requestResult = $api->sendOneTimePassword($mockedOtpRequest);

        $this->assertInstanceOf(ResponseInterface::class, $requestResult);
    }

    /** @test */
    public function it_will_pass_source_model_as_option()
    {
        $model = $this->getMockBuilder(SourceOtpRequestFixture::class)->getMock();
        $model->method('getMessageType')->willReturn(MessageType::ALPHANUMERIC());
        $model->method('getChannelType')->willReturn(ChannelType::GENERIC());
        $model->method('getPinType')->willReturn(PinType::NUMERIC());

        /** @var SourceOtpRequestFixture $model */
        $model;

        /** @var \Mockery\MockInterface $mockedClient */
        $mockedClient = \Mockery::mock(\GuzzleHttp\Client::class);
        $mockedClient->shouldReceive('request')->withArgs([
            'POST',
            'https://api.example/sms/otp/send',
            [
                \GuzzleHttp\RequestOptions::HTTP_ERRORS => false,
                \GuzzleHttp\RequestOptions::HEADERS => [
                    'Accept' => 'application/json',
                ],
                \GuzzleHttp\RequestOptions::JSON => [
                    'from' => $model->getFrom(),
                    'to' => $model->getTo(),
                    'channel' => $model->getChannelType(),
                    'message_type' => $model->getMessageType(),
                    'message_text' => $model->getMessageText(),
                    'pin_type' => $model->getPinType(),
                    'pin_attempts' => $model->getPinAttempts(),
                    'pin_time_to_live' => $model->getPinTtlMinutes(),
                    'pin_length' => $model->getPinLength(),
                    'pin_placeholder' => $model->getPinPlaceholder(),
                    'api_key' => $this->publicKey,
                ],
                RequestOptions::SOURCE_MODEL => $model,
            ],
        ])->once();

        /**
         * @var ApiConfigInterface $mockedConfig
         * @var \GuzzleHttp\Client $mockedClient
         * */
        $api = new Client($this->mockedConfig, $mockedClient);
        $requestResult = $api->sendOneTimePassword($model);

        $this->assertInstanceOf(ResponseInterface::class, $requestResult);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        \Mockery::close();
    }
}
